<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\File;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Mail\MyMail;



class UserController extends Controller
{
    public function guard()
    {
        return Auth::guard();
    }




    //la fonction de génération aléatoire du token

    public function respondWithToken($token)
    {


        return response()->json(
            [
                "title" => "CONNEXION",
                "message" => "Connexion réussie.Vous vous êtes connecté(e) avec succès.",
                'token' => $token,
                'token_type' => 'bearer',
                'token_validity' => $this->guard()->factory()->getTTL() * 60
            ]
        );
    }


    //la  fonction d'inscription d'un utilisateur
    public function register(Request $request)

    {

        $getTemporaryProfileImageUrl = URL::current() . "/user/";
        $defaultProfile = "avatar.jpg";

        $messages = [
            "nom_user.required" => "Votre nom est requis",
            "nom_user.max" => "Votre nom est trop long",
            "prenoms_user.required" => "Votre prenoms est requis",
            "prenoms_user.max" => "Votre prenoms est trop long",
            "email_user.required" => "Votre adresse mail est requise",
            "email_user.email" => "Votre adresse mail est invalide",
            "email_user.max" => "Votre adresse mail est trop longue",
            "email_user.unique" => "Votre adresse mail est déjà utilisé et enrégistré dans la base",
            "prefixpays_user.required" => "le code téléphonique de votre pays est requis",
            "adresse_user.required" => "Votre adresse est requise",
            "roles_user.required" => "Le Type de votre compte est requis",
            "roles_user.max" => "La valeur du type est trop longue",
            "telephone_user.required" => "Votre numéro de telephone est requis",
            "telephone_user.unique" => "Votre numéro de telephone est déjà utilisé et enrégistré dans la base",
            "telephone_user.min" => "Votre numero de telephone est court",
            "telephone_user.regex" => "Votre numero de telephone est invalide",
            "password.required" => "Le mot de passe est requis",
            "password.min" => "Le mot de passe est trop court",
            "password.same" => "Les mots de passes ne sont pas identiques",
        ];

        $validator = Validator::make($request->all(), [

            "nom_user" => "bail|required|max:50|",
            "prenoms_user" => "bail|required|max:50",
            "prefixpays_user" => "bail|required|min:2|max:10",
            "email_user" => "bail|required|email|max:50|unique:users,email_user",
            "roles_user" => "bail|required|max:30",
            "adresse_user" => "bail|required|",
            "telephone_user" => "bail|required|min:8|max:10|regex:/^([0-9\s\-\+\(\)]*)$/|unique:users,telephone_user",
            "password" => "bail|required|min:4|same:confirmation_password",

        ], $messages);

        //Send failed response if request is not valid

        if ($validator->fails()) return response()->json([
            "status" => "error",
            "title" => "INSCRIPTION",
            "message" => $validator->errors()->first()
        ]);


        $client = new User();
        $client->nom_user = $request->nom_user;
        $client->prenoms_user = $request->prenoms_user;
        $client->prefixpays_user = $request->prefixpays_user;
        $client->adresse_user = $request->adresse_user;
        $client->email_user = $request->email_user;
        $client->telephone_user = $request->telephone_user;
        $client->roles_user = $request->roles_user;

        //condition d'existence du role
        if ($request->roles_user == "Client") {
            $client->roles_user = "Client";
        } elseif ($request->roles_user == "Demarcheur") {
            $client->roles_user = "Demarcheur";
        } else {
            $client->roles_user = "Proprietaire";
        }


        $client->password = Hash::make($request->password);

        $client->image_user = $getTemporaryProfileImageUrl . $defaultProfile;
        if ($request->hasfile('image_user')) {
            $file = $request->file('image_user');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move('user/profile/', $filename);
            $client->image_user = $getTemporaryProfileImageUrl . $filename;
        }

        $verification_code = Str::random(60);
        $client->verification_code = $verification_code;


        $token = Auth::login($client);
        //Générer un token
        $client->save();

        if ($client != null) {

            //l'envoi de maiil pour l'inscription ausite

            //le code suivant marche avec l'exécution de l'envoi de mail(à utiliser)
            // penser à configurer et à modifier les informations concernant l'envoi
            // de mail avec le username_email qu'il faut

            $verification_code = $client->verification_code;
            $email_user = $client->email_user;
            $nom_user = $client->nom_user;
            $prenoms_user = $client->prenoms_user;



            Mail::send(
                'signup-email',
                [
                    'verification_code' => $verification_code,
                    'nom_user' => $nom_user,
                    'prenoms_user' => $prenoms_user,
                ],

                function (Message $message) use ($email_user) {
                    $message->APP_NAME('IMMOBILIER');
                    $message->subject('INSCRIPTION SUR LE SITE DE IMMO');
                    $message->to($email_user);
                }
            );


            // Voir le message de confirmation 

            return response()->json([
                "status" => true,
                "redirect_to" => back(),
                "redirect_to" => url("http://127.0.0.1.8000/api/auth/confirmation-email/verificationcode/" . $verification_code),
                "title" => "INSCRIPTION",
                "message" => "Mr/Mlle " . $client->nom_user . " " . $client->prenoms_user . ". Votre compte a été crée avec succes. Vérifier votre adresse mail pour le lien de confirmation."

            ], 200);
        }


        return response()->json([
            "status" => "error",
            "redirect_to" => back(),
            "title" => "INSCRIPTION",
            "message" => "Une erreur s'est produite. Veuillez réessayer s'il vous plait!"

        ], 403);
    }


    // La fonction de vérification de l'authencité de l'utilisateur


    public function verificationUser($verification_code, Request $request)
    {
        // $verification_code = \Illuminate\Support\Facades\Request::get('verification_code');
        $client = User::where(['verification_code' => $verification_code])->first();

        if ($client != null) {

            $messages = [

                "email_user.required" => "Votre adresse mail est requis",
                // "password.required" => "Le nouveau mot de passe est requis",
                // "password.min" => "Le nouveau mot de passe est trop court",
                // "password.max" => "Le nouveau mot de passe est trop long",
                // "confirmation_password.required" => "La confirmation du mot de passe est requis",
                // "confirmation_password.same" => "La confirmation du mot de passe ne correspond pas"
            ];

            $validator = Validator::make($request->all(), [
                "email_user" => "bail|required|email",
                // "password" => "bail|required|min:8|same:confirmation_password",
                // "confirmation_password" => "required|same:password"

            ], $messages);

            if ($validator->fails()) return response()->json([
                "status" => false,
                "reload" => false,
                "title" => "VERIFICATION DU CODE",
                "message" => $validator->errors()->first()
            ]);

            // $client = User::find($verification_code->user_id);


            if ($client->email_user != $request->email_user) {
                return response()->json([
                    "status" => false,
                    "reload" => false,
                    "redirect_to" => null,
                    "title" => "VERIFICATION DU CODE ET DU COMPTE",
                    "message" => "Votre adresse mail est incorrecte."
                ]);
            } else {

                $client->status_user = 1;
                $client->save();

                // Voir le message de confirmation 
                return response()->json([
                    "status" => true,
                    "reload" => true,
                    "redirect_to" => route("login"),
                    "title" => "VERIFICATION DU CODE",
                    "message" => "Votre compte a été vérifié. Vous pouvez maintenant vous y connecter!"

                ]);
            }
        }

        return response()->json([
            "status" => true,
            "reload" => true,
            "redirect_to" => back(),
            "title" => "VERIFICATION DU CODE",
            "message" => "Code de vérification invalide"

        ]);
    }



    //la  fonction de connexion d'un utilisateur

    public function login(Request $request)

    {

        $messages = [

            "email_user.required" => "Votre adresse mail est requise",
            "email_user.email" => "Votre adresse mail est invalide",
            "password.required" => "Le mot de passe est requis",
            "password.min" => "Le mot de passe est trop court",
        ];

        $validator = Validator::make($request->all(), [

            "email_user" => "bail|required|email|max:100|",
            "password" => "bail|required|min:8|max:50",

        ], $messages);

        if ($validator->fails()) {

            return response()->json([
                "status" => false,
                "reload" => false,
                "title" => "CONNEXION",
                "message" => $validator->errors()->first()
            ]);
        }

        //validité du token(1j = 24h et 1h =60min donc token_validity = 24*60)

        $token_validity = 60;

        $this->guard()->factory()->setTTL($token_validity);
        // $client = User::where(['status_user' => $status_user])->first();

        //condition de fonctionnement

        if (!$token = $this->guard()->attempt($validator->validated())) {
            return response()->json([
                "status" => false,
                "reload" => false,
                "title" => "CONNEXION",
                'erreur' => "Adresse Email ou mot de passe incorrect"

            ], 401);
        }




        return $this->respondWithToken($token);


        //Vérifier si le client existe

        $client = User::where('email_user', '=', $request->email_user)->first();

        if ($client) {

            if (Hash::check($request->password, $client->password)) {

                //créer un jeton ou un token

                $token = $client->createToken('auth_token')->plainTextToken;

                //réponse
                return response()->json([
                    "status" => true,
                    "message" => "Connexion Réussie",
                    "acces_token" => $token
                ], 200);


                return response()->json([
                    "status" => true,
                    "redirect_to" => null,
                    'acces_token' => $token,
                    "title" => "CONNEXION",
                    "message" => "Mr/Mlle " . $client->nom_user . " " . $client->prenoms_user . ", vous vous êtes connectés avec succes"
                ], 200);
            } else {
                //réponse
                return response()->json([
                    "status" => 0,
                    "message" => "Mot de Passe incorrect"
                ]);
            }
        } else {

            //réponse
            return response()->json([
                "status" => 0,
                "message" => "Utilisateur n'existe pas ou est introuvable"
            ], 404);
        }
    }



    //la  fonction de déconnexion d'un utilisateur

    public function logout()
    {

        $this->guard()->logout();

        // $client = Auth::user()->tokens()->delete();

        return response()->json([
            "status" => true,
            "reload" => true,
            "redirect_to" => null,
            "title" => "DECONNEXION",
            "message" => 'Vous vous êtes déconnectés avec succès',
        ]);
    }


    //profile de l'utilisateur

    public function profile()
    {

        if (!$this->guard()->user()) {
            return response()->json([
                "status" => true,
                "redirect_to" => route("login"),
                "title" => "AVERTISSEMENT",
                "message" => "Vous n'êtes pas connectés. Veuilez vous connecter.",
            ]);
        } else

            return response()->json(
                [
                    "status" => true,
                    "title" => "INFORMATIONS SUR LE PROFIL DU CLIENT",
                    "Informations" => $this->guard()->user()

                ]
            );
    }



    //rafraichir pour regénerer un token pour un utilisateur 
    //connecté

    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }


    // fonction pour changer le mot de passe 

    public function updateMotdepasse(Request $request)
    {
        if (!$this->guard()->user()) {
            return response()->json([
                "status" => true,
                "redirect_to" => route("login"),
                "title" => "AVERTISSEMENT",
                "message" => "Vous n'êtes pas connectés. Veuilez vous connecter.",
            ]);
        } else {

            $messages = [

                "password.required" => "Le mot de passe est requis",
                "password.min" => "Le mot de passe est trop court",
                "password.same" => "Les mots de passes ne sont pas identiques",

            ];



            $validator = Validator::make($request->all(), [

                "password" => "bail|required|min:8|max:50|same:confirmation_password",

            ], $messages);


            if ($validator->fails())
                return response()->json([
                    "status" => false,
                    "title" => "CHANGEMENT DE MOT DE PASSE",
                    "message" => $validator->errors()->first()
                ]);

            // loggeduser :données utilisateur enregistré

            $loggeduser = $this->guard()->user();
            $loggeduser->password = Hash::make($request->password);
            $loggeduser->save();


            return response()->json([
                "status" => true,
                "redirect_to" => back(),
                "title" => "CHANGEMENT DE MOT DE PASSE",
                "message" => "Le mot de passe a été changé avec succès.",
            ], 200);
        }
    }
}
