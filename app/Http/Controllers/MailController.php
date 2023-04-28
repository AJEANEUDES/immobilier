<?php

namespace App\Http\Controllers;

use App\Mail\SignupEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public static function sendSignupEmail($nom_user, $prenoms_user, $email_user, $verification_code)
    {
        $data =[
            'nom_user'=> $nom_user,
            'prenoms_user'=> $prenoms_user,
            'verification_code'=> $verification_code,
        ];
        Mail::to($email_user)->send(new SignupEmail($data));
    }
}
