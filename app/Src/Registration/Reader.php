<?php

namespace App\Src\Registration;

use App\Models\User;


class Reader {

    public function __construct() {

    }

    public function getUser($id) {
        return User::with(['profile', 'roles'])->find($id);
    }

    function findPendingUserByEmail($email) {
        $user = User::where("email", $email)->first();

        if (isset($user->profile)) {
            if (in_array($user->profile->status, ['pending', 'payment_done'])) {
                return $user;
            }
        }

        return null;
    }

    function getUnderReviewProfiles() {

        $users = User::with(['profile', 'roles'])
                ->whereHas("profile", function($q) {
                    $q->where("status","=","under_review");
                })->get();

        return $users;
    }


}
