<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\RegisterRequest;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Src\Registration\Writer as RegistrationWriter;
use App\Src\Registration\Reader as RegistrationReader;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        try {
            $validated_input = $request->validated();
            $registrationReader = new RegistrationReader();

            $registrationWriter = new RegistrationWriter($validated_input);
            $paymentDetails = null;

            DB::beginTransaction();
            if (isset($validated_input['id'])) {
                $registrationWriter->setUserId($validated_input['id']);
                $registrationWriter->updateUser();
                $registrationWriter->updateProfile();
                $paymentDetails = $registrationWriter->updatePayment();
            } else {
                $registrationWriter->createUser();
                $registrationWriter->createProfile();
                $paymentDetails = $registrationWriter->createPayment();
            }

            DB::commit();

            $data['user'] = $registrationReader->getUser($registrationWriter->user->id);
            $data['paymentDetails'] = $paymentDetails;

            return $this->sendResponse($data, 'User register successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e, ["internal server error"], 500);
        }

    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if ($user->hasRole('admin')) {
                $success['token'] = $user->createToken('atmiya')->plainTextToken;
                $success['user'] = $user;

                return $this->sendResponse($success, 'User login successfully.');
            }

            $profile = $user->profile;
            if ($profile->status == 'admin_approved') {
                $success['token'] = $user->createToken('atmiya')->plainTextToken;
                $success['user'] = $user->name;
                return $this->sendResponse($success, 'User login successfully.');
            }

            return $this->sendError(["message" => 'Unauthorised.', "mode" => "profile_under_review"], ['error' => 'Unauthorised'], 500);
        } else {
            return $this->sendError(["message" => 'Unauthorised.', "mode" => "user_not_found"], ['error' => 'Unauthorised'], 500);
        }
    }


      /**
     * get api
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request): JsonResponse
    {
        $registrationReader = new RegistrationReader();
        $data['user'] = $registrationReader->getUser($request->user()->id);

        return $this->sendResponse($data, 'User details.');

    }

        /**
     * update api
     *
     * @return \Illuminate\Http\Response
     */
    public function update(RegisterRequest $request): JsonResponse
    {
        $user = $request->user();

        $validated_input = $request->validated();

        if($validated_input['id'] != $user->id) {
            return $this->sendError('Unauthorized request.', ['error' => 'Unauthorized']);
        }

         // save data user table
         DB::beginTransaction();
         if(array_key_exists('password', $validated_input)) {
             $validated_input['password'] = bcrypt($validated_input['password']);
         }
         $validated_input['name'] = $validated_input['first_name'] . " " . $validated_input['last_name'];
         User::find($user->id)->update($validated_input);

         Profile::find($user->profile->id)->update($validated_input);
         DB::commit();

        $registrationReader = new RegistrationReader();
        $data['user'] = $registrationReader->getUser($user->id);

        return $this->sendResponse($data, 'User updated successfully.');
    }

    public function submitProfile(Request $request): JsonResponse
    {

        $user = User::find($request->user_id);

        if ($user->profile) {
            $user->profile->status = 'under_review';
            $user->profile->save();
        } else {
            return $this->sendError('Profile.', ['error' => 'Unauthorized']);
        }

        $registrationReader = new RegistrationReader();
        $data['user'] = $registrationReader->getUser($user->id);

        return $this->sendResponse($data, 'User updated successfully.');
    }

    public function findProfileByEmail(Request $request): JsonResponse {

        if (!isset($request->email)) {
            return $this->sendError('Please provide the email', ['error' => 'Please provide the email'], 422);
        }

        $registrationReader = new RegistrationReader();

        $userFound = $registrationReader->findPendingUserByEmail($request->email);

        if ($userFound) {
            $data['user'] = $registrationReader->getUser($userFound->id);
            return $this->sendResponse($data, 'User found successfully.');
        }

        return $this->sendResponse("", 'User not found.');

    }

    function getReviewProfiles(Request $request) {

        $registrationReader = new RegistrationReader();

        $data['users'] = $registrationReader->getUnderReviewProfiles();

        return $this->sendResponse($data, 'Users data.');

    }




}

