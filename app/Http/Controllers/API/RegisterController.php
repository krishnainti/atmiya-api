<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use App\Http\Controllers\API\BaseController as BaseController;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $validated_input = $request->validated();
        
            // save data user table
            DB::beginTransaction();
            $validated_input['password'] = bcrypt($validated_input['password']);
            $validated_input['name']=$validated_input['first_name']." " .$validated_input['last_name'];
            $user = User::create($validated_input);
            // update the role to user
            $user->assignRole('user');
            
            $validated_input['user_id'] = $user->id;
            $validated_input['status'] = 'pending';

            // save data in profiles table
            $profile = Profile::create($validated_input);
            
            DB::commit();
            // save data in payments if on paypal or cards else send email to user for zelle payment
    
           $data['user'] = $user;
           $data['profile'] = $profile;
           $data['roles'] = $user->roles;
    
            return $this->sendResponse($data, 'User register successfully.');

        } catch(\Exception $e) {
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

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('atmiya')->plainTextToken;
            $success['name'] =  $user->name;
            $success['profile'] =  $user->profile;
            $success['roles'] =  $user->roles;

            return $this->sendResponse($success, 'User login successfully.');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }
}
