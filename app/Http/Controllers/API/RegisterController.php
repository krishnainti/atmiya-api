<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\RegisterRequest;
use App\Mail\ProfileStatusUpdateNotification;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\User;
use App\Src\Payment\Paypal;
use App\Src\Registration\Reader as RegistrationReader;
use App\Src\Registration\Writer as RegistrationWriter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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

                $completed_profile_payment = Payment::where([
                    'for_type' => Profile::class,
                    'for_id' => $registrationWriter->profile->id,
                    'status' => 'completed',
                ])->first();

                if (empty($completed_profile_payment)) {
                    $paymentDetails = $registrationWriter->createPayment();
                }
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
                $success['user'] = $user;
                return $this->sendResponse($success, 'User login successfully.');
            }

            if ($profile->status == 'admin_rejected') {
                return $this->sendError(["message" => 'Unauthorized.', "mode" => "profile_rejected"], ['error' => 'Unauthorized'], 500);
            }

            return $this->sendError(["message" => 'Unauthorized.', "mode" => "profile_under_review"], ['error' => 'Unauthorized'], 500);
        } else {
            return $this->sendError(["message" => 'Unauthorized.', "mode" => "user_not_found"], ['error' => 'Unauthorized'], 500);
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

        if ($validated_input['id'] != $user->id) {
            return $this->sendError('Unauthorized request.', ['error' => 'Unauthorized']);
        }

        // save data user table
        DB::beginTransaction();
        if (array_key_exists('password', $validated_input)) {
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
            // TODO: send EMAIL
            Mail::to($user->email)->send(new ProfileStatusUpdateNotification('Under Review'));
        } else {
            return $this->sendError('Profile.', ['error' => 'Unauthorized']);
        }

        $registrationReader = new RegistrationReader();
        $data['user'] = $registrationReader->getUser($user->id);

        return $this->sendResponse($data, 'User updated successfully.');
    }

    public function findProfileByEmail(Request $request): JsonResponse
    {
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

    public function getReviewProfiles(Request $request): JsonResponse
    {

        $registrationReader = new RegistrationReader();

        $data['users'] = $registrationReader->getUnderReviewProfiles();

        return $this->sendResponse($data, 'Users data.');

    }

    public function getSingleReviewProfile(Request $request)
    {

        $registrationReader = new RegistrationReader();

        $data['user'] = $registrationReader->getUser($request->userId);

        return $this->sendResponse($data, 'User data.');

    }

    public function updateReviewProfileStatus(Request $request)
    {

        if (!in_array($request->status, ["admin_approved", "admin_rejected"])) {
            return $this->sendError(["message" => 'InValid Status.', "mode" => "invalid_status"], ['error' => 'InValid Status'], 500);
        }

        $registrationWriter = new RegistrationWriter([]);

        $registrationWriter->setUserId($request->userId);

        if (!$registrationWriter->profile) {
            return $this->sendError(["message" => 'Profile not found for the user.', "mode" => "profile_not_found"], ['error' => 'Profile not found for the user'], 500);
        }

        if ($registrationWriter->profile->status !== "under_review") {
            if ($registrationWriter->profile->status === 'admin_approved') {
                return $this->sendError(["message" => 'Already Approved.', "mode" => "already_approved"], ['error' => 'Already Approved'], 500);
            }

            if ($registrationWriter->profile->status === 'admin_rejected') {
                return $this->sendError(["message" => 'Already Rejected.', "mode" => "already_rejected"], ['error' => 'Already Rejected'], 500);
            }
        }

        $registrationWriter->updateStatus($request->status);

        return $this->sendResponse([], 'Status Updated successfully.');

    }

    public function captureRegistrationPaypalPaymentOrder(Request $request): JsonResponse
    {
        $payment_id = $request->token;
        $payment = Payment::where('payment_id', $payment_id)->first();
        if ($payment_id == null || empty($payment)) {
            return $this->sendError('Payment.', ['error' => 'User payment details found.']);
        }

        $paypal = new Paypal();
        $paypalResponse = $paypal->capturePaymentOrder($payment->payment_id);
        DB::beginTransaction();

        if (isset($paypalResponse['status']) && $paypalResponse['status'] == 'COMPLETED') {
            $payment = Payment::where('payment_id', $payment->payment_id)->first();
            $payment->status = 'completed';
            $payment->meta = $paypalResponse;
            $payment->save();

            $profile = $payment->for;
            $profile->status = 'payment_done';
            $profile->save();
            DB::commit();
            return $this->sendResponse(['status' => true, 'payment' => $payment], 'Payment completed Successfully');

        } else if (isset($paypalResponse['error']) && ($paypalResponse['error']['message'])) {
            return $this->sendError('Payment.', ['response' => $paypalResponse, 'message' => $paypalResponse['error']['message']]);
        } else {
            return $this->sendError('Payment.', ['error' => 'some thing went wrong.']);
        }
    }

    public function cancelPaypalPayment(Request $request): JsonResponse {

        $payment_id = $request->token;
        $payment = Payment::where('payment_id', $payment_id)->first();
        if ($payment_id == null || empty($payment)) {
            return $this->sendError('Payment.', ['error' => 'User payment details found.']);
        }

        DB::beginTransaction();

        $payment = Payment::where('payment_id', $payment->payment_id)->first();
        $payment->status = 'canceled';
        $payment->save();

        DB::commit();

        return $this->sendResponse(['status' => true], 'Payment status updated');

    }

}
