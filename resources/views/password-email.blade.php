
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Envoi de mail pour la réinitialisation de mot de passe : Youpigoo</title>
</head>

<body style="margin: 100px">

    <h3 style="font-weight:bold; text-align:center">IMMOBILIER</h3>

    Bienvenue {{ $user_name }}
    
    <p> Cliquer ici pour modifier votre mot de passe Ou copiez 
        et collez le lien suivant dans votre navigateur</p>
    
    <p>
        <a href="http://127.0.0.1.3000/api/auth/reset/{{ $reset_code }}">
            Reinitialiser mon mot de passe
        </a>
    </p>
    Merci,<br>
    IMMOBILIER

</body>

</html>