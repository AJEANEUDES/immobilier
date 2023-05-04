<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Mail\ForgetPasswordMail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
   
    // Fonction de réinitialisation du mot de passe
    public function resetPassword(Request $request)
    {
        $messages = [
            'email_user.required' => "Votre adresse email est requis",
            'email_user.email' => "Votre adresse email est invalide"
        ];

        $validator = Validator::make($request->all(), [
            'email_user' => "bail|required|email"
        ], $messages);

        if ($validator->fails()) return response()->json([
            "status" => false,
            "title" => "REINITIALISATION DE MOT DE PASSE",
            "message" => $validator->errors()->first()
        ]);

        $user = User::where('email_user', $request->email_user)->first();

        if (!$user) {
            return response()->json([
                "status" => false,
                "title" => "REINITIALISATION DE MOT DE PASSE",
                "message" => "Vous n'avez pas de compte car l'email saisi n'existe pas, donc impossible d'effectuer cette action !"
            ], 404);
        } else {

            //Générer un code

            $reset_code = Str::random(60);
            $passwordReset = new PasswordReset();
            $passwordReset->user_id = $user->id;
            $passwordReset->reset_code = $reset_code;
            $passwordReset->created_at = Carbon::now();

            $passwordReset->save();

            // dump("http://127.0.0.1.8000/api/auth/reset/" .$reset_code);

            //Envoie du mail de reinitialisation de mot de passe 
            //1-le code suivant marche avec l'exécution de l'envoi de mail
            // Mail::to($user->email_user)->send(new ForgetPasswordMail($user->nom_user, $reset_code));

            //l'envoi de maiil pour la réinitialisation du mot de passe

            //2-le code suivant marche avec l'exécution de l'envoi de mail(à utiliser)

            $email_user = $user->email_user;
            $user_name = $user->nom_user + $user->prenoms_user;

            Mail::send(
                'password-email',
                [
                    'reset_code' => $reset_code,
                    'user_name' => $user_name,
                ],

                function (Message $message) use ($email_user) {
                  
                    $message->APP_NAME('IMMOBILIER');
                    $message->subject('Réinitialisation du mot de passe');
                    $message->to($email_user);
                }
            );

            return response()->json([
                "status" => true,
                "reload" => true,
                "redirect_to" => url("http://127.0.0.1.3000/api/auth/reset/" . $reset_code),
                "title" => "REINITIALISATION DE MOT DE PASSE",
                "message" => "Mr/Mlle " . $user->nom_user . " " . $user->prenoms_user . " Nous vous avons envoyé un lien de réinitialisation de mot de passe. Merci de consulter vos emails."
            ]);
        }
    }


    // fonction de réinitialisation du mot de passe (ajout du nouveau mot de passe)

    public function getPasswordReset($reset_code, Request $request)
    {
        $password_reset_data = PasswordReset::where('reset_code', $reset_code)->first();

        if (!$password_reset_data || Carbon::now()->subMinutes(50) > $password_reset_data->created_at) {
            return response()->json([
                "status" => false,
                "title" => "REINITIALISATION DE MOT DE PASSE",
                "message" => "Lien de réinitialisation du mot de passe invalide ou lien expiré."
            ]);
        } 
        
        else {
            $messages = [

                "email_user.required" => "Votre adresse mail est requis",
                "password.required" => "Le nouveau mot de passe est requis",
                "password.min" => "Le nouveau mot de passe est trop court",
                "password.max" => "Le nouveau mot de passe est trop long",
                "confirmation_password.required" => "La confirmation du mot de passe est requise",
                "confirmation_password.same" => "La confirmation du mot de passe ne correspond pas"
            ];

            $validator = Validator::make($request->all(), [
                "email_user" => "bail|required|email",
                "password" => "bail|required|min:8|same:confirmation_password",

            ], $messages);

            if ($validator->fails()) return response()->json([
                "status" => false,
                "title" => "REINITIALISATION DE MOT DE PASSE",
                "message" => $validator->errors()->first()
            ]);


            $user = User::find($password_reset_data->user_id);


            if ($user->email_user != $request->email_user) {
                return response()->json([
                    "status" => false,
                    "redirect_to" => null,
                    "title" => "REINITIALISATION DE MOT DE PASSE",
                    "message" => "Votre adresse mail est incorrecte."
                ]);
            } else {

                $user->update(['password' => Hash::make($request->password)]);
                $password_reset_data->delete();

                return response()->json([
                    "status" => true,
                    "redirect_to" => route('login'),
                    "title" => "REINITIALISATION DE MOT DE PASSE",
                    "message" => "Votre mot de passe a ete reinitialisé, veuillez vous connecter avec votre nouveau mot de passe."
                ]);
            }
        }
    }




}
