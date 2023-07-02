<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\RegisterRequest;
use App\Models\Profile;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

            // save data user table
            DB::beginTransaction();
            $validated_input['password'] = bcrypt($validated_input['password']);
            $validated_input['name'] = $validated_input['first_name'] . " " . $validated_input['last_name'];
            $user = User::create($validated_input);
            // update the role to user
            $user->assignRole('user');

            $validated_input['user_id'] = $user->id;
            $validated_input['status'] = 'pending';

            // save data in profiles table
            $profile = Profile::create($validated_input);

            if ($validated_input['payment_mode'] == 'Zelle') {
                $validated_input['status'] = 'pending';
            }

            
            // save data in payments if on paypal or cards else send email to user for zelle payment
            $membership_category = $profile->membership_category_details;

            if ($membership_category->fee == 0) {
                $payment_attributes = [
                    'payment_for' => 'registration',
                    'payment_mode' => $validated_input['payment_mode'],
                    'amount' => 0,
                    'status' => 'completed',
                    'payment_done_by' => $user->id,
                ];
                Payment::create($payment_attributes);
            }else {
                if(strtolower($validated_input['payment_mode']) =='paypal' || strtolower($validated_input['payment_mode']) =='card' ) {
                    $payment_attributes = [
                        'payment_for' => 'registration',
                        'payment_mode' => 'paypal',
                        'amount' => $membership_category->fee,
                        'status' => 'pending',
                        'payment_done_by' => $user->id,
                    ];
                    Payment::create($payment_attributes);

                } else if(strtolower($validated_input['payment_mode']) =='zelle') {
                    $payment_attributes = [
                        'payment_for' => 'registration',
                        'payment_mode' => 'zelle',
                        'amount' => $membership_category->fee,
                        'status' => 'pending',
                        'payment_done_by' => $user->id,
                    ];
                    Payment::create($payment_attributes);
                }
            }

            DB::commit();

            $data['user'] = $user;
            $data['profile'] = $profile;
            $data['roles'] = $user->roles;

            return $this->sendResponse($data, 'User register successfully.');

        } catch (\Exception $e) {
            dd($e);
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

            if($user->hasRole('admin')) {
                $success['token'] = $user->createToken('atmiya')->plainTextToken;
                $success['name'] = $user->name;
                $success['profile'] = $user->profile;
                $success['roles'] = $user->roles;
                return $this->sendResponse($success, 'User login successfully.');
            }else{
                $profile = $user->profile;
                if($profile->status =='admin_approved') {
                    $success['token'] = $user->createToken('atmiya')->plainTextToken;
                    $success['name'] = $user->name;
                    $success['profile'] = $user->profile;
                    $success['roles'] = $user->roles;
                    return $this->sendResponse($success, 'User login successfully.');
                }
            }
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }
}
