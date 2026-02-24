<?php

namespace App\Services;
use App\Models\User;
class UserService{
public function getuserbyid($id){
    $user = User::where("id",$id)->first();
    return $user;
}

public function getuserbyemail($email){
    $user = User::where("email",$email)->first();
    return $user;
}
}