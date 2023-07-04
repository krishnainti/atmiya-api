<?php

namespace App\Src\Registration;

use App\Models\User;


class Reader {

    public function __construct() {

    }

    public function getUser($id) {
        return User::with(['profile', 'roles'])->find($id);
    }


}
