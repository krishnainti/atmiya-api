<?php

namespace App\Src\Registration;

use App\Models\User;
use App\Models\Payment;
use App\Models\Profile;

class Writer {

    private $registrationData;
    public $user;
    public $profile;


    public function __construct($registrationData) {
        $this->registrationData = $registrationData;
    }

    public function setUserId($id) {
        $this->user = User::find($id);
        $this->profile = $this->user->profile;
    }

    public function createUser() {

        $userData['name'] = $this->registrationData['first_name']. " " .$this->registrationData['last_name'];
        $userData['email'] = $this->registrationData['email'];
        $userData['password'] = bcrypt($this->registrationData['password']);

        $this->user = User::create($userData);
        $this->user->assignRole('user');

        return $this->user;
    }

    public function createProfile() {
        $profileData = [
            'user_id' => $this->user->id ,
            'reference_by'=> $this->registrationData['reference_by'],
            'reference_phone'=> $this->registrationData['reference_phone'],
            'first_name'=> $this->registrationData['first_name'],
            'last_name'=> $this->registrationData['last_name'],
            'phone'=> $this->registrationData['phone'],
            'marital_status'=> $this->registrationData['marital_status'],
            'gender' => $this->registrationData['gender'],
            'spouse_first_name'=> $this->registrationData['spouse_first_name'],
            'spouse_last_name'=> $this->registrationData['spouse_last_name'],
            'spouse_email'=> $this->registrationData['spouse_email'],
            'spouse_phone'=> $this->registrationData['spouse_phone'],
            'family_members' => $this->registrationData['family_members'],
            'address_line_1'=> $this->registrationData['address_line_1'],
            'address_line_2'=> $this->registrationData['address_line_2'],
            'city'=> $this->registrationData['city'],
            'state'=> $this->registrationData['state'],
            'metro_area'=> $this->registrationData['metro_area'],
            'zip_code'=> $this->registrationData['zip_code'],
            'country' => $this->registrationData['country'],
            'membership_category' => $this->registrationData['membership_category'],
            'payment_mode' => $this->registrationData['payment_mode'],
            'status' => "pending"
        ];

        $this->profile = Profile::create($profileData);

        return $this->profile;
    }

    public function createPayment() {
        $membershipCategory = $this->profile->membershipCategory;

        if ($membershipCategory->fee == 0) {
            $paymentData = [
                'for_id' => $this->profile->id,
                'for_type' => Profile::class,
                'payment_mode' => strtolower($this->registrationData['payment_mode']),
                'amount' => 0,
                'status' => 'completed',
                'payment_done_by' => $this->user->id,
            ];
        } else {
            $paymentData = [
                'for_id' => $this->profile->id,
                'for_type' => Profile::class,
                'payment_mode' => in_array(strtolower($this->registrationData['payment_mode']), ['paypal','card']) ? 'paypal' : 'zelle',
                'amount' => $membershipCategory->fee,
                'status' => 'pending',
                'payment_done_by' => $this->user->id,
            ];
        }

        return Payment::create($paymentData);
    }


    public function updateUser() {

        if (isset($this->registrationData['password'])) {
            $this->user->password = bcrypt($this->registrationData['password']);
        }

        if (isset($this->registrationData['email'])) {
            $this->user->email = $this->registrationData['email'];
        }

        $this->user->name = $this->registrationData['first_name']." ".$this->registrationData['last_name'];

        $this->user->save();

    }

    public function updateProfile() {

        $this->profile->user_id = $this->user->id;
        $this->profile->reference_by = $this->registrationData['reference_by'];
        $this->profile->reference_phone = $this->registrationData['reference_phone'];
        $this->profile->first_name = $this->registrationData['first_name'];
        $this->profile->last_name = $this->registrationData['last_name'];
        $this->profile->phone = $this->registrationData['phone'];
        $this->profile->marital_status = $this->registrationData['marital_status'];
        $this->profile->gender = $this->registrationData['gender'];
        $this->profile->spouse_first_name = $this->registrationData['spouse_first_name'];
        $this->profile->spouse_last_name = $this->registrationData['spouse_last_name'];
        $this->profile->spouse_email = $this->registrationData['spouse_email'];
        $this->profile->spouse_phone = $this->registrationData['spouse_phone'];
        $this->profile->family_members = $this->registrationData['family_members'];
        $this->profile->address_line_1 = $this->registrationData['address_line_1'];
        $this->profile->address_line_2 = $this->registrationData['address_line_2'];
        $this->profile->city = $this->registrationData['city'];
        $this->profile->state = $this->registrationData['state'];
        $this->profile->metro_area = $this->registrationData['metro_area'];
        $this->profile->zip_code = $this->registrationData['zip_code'];
        $this->profile->country = $this->registrationData['country'];
        $this->profile->membership_category = $this->registrationData['membership_category'];
        $this->profile->payment_mode = $this->registrationData['payment_mode'];
        $this->profile->status = "pending";

        $this->profile->save();

    }

    public function updatePayment() {

        Payment::whereIn('id', $this->profile->payments->pluck("id")->toArray())->update(["status" => "expired"]);

        $membershipCategory = $this->profile->membershipCategory;

        if ($membershipCategory->fee == 0) {
            $paymentData = [
                'for_id' => $this->profile->id,
                'for_type' => Profile::class,
                'payment_mode' => strtolower($this->registrationData['payment_mode']),
                'amount' => 0,
                'status' => 'completed',
                'payment_done_by' => $this->user->id,
            ];
        } else {
            $paymentData = [
                'for_id' => $this->profile->id,
                'for_type' => Profile::class,
                'payment_mode' => in_array(strtolower($this->registrationData['payment_mode']), ['paypal','card']) ? 'paypal' : 'zelle',
                'amount' => $membershipCategory->fee,
                'status' => 'pending',
                'payment_done_by' => $this->user->id,
            ];
        }


        return Payment::create($paymentData);
    }

    public function updateStatus($status) {

        $this->profile->status = $status;

        return $this->profile->save();

    }

}
