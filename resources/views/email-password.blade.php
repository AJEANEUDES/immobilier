@component('mail::message')

<h3 style="font-weight:bold; text-align:center">IMMOBILIER</h3>


    Bienvenue {{ $user_name }}
    @component('mail::button', ['url' => route('reinitialiser.reset-code', $reset_code)])
        Cliquer ici pour modifier votre mot de passe
    @endcomponent
    <p> Cliquer sur le bouton pour modifier votre mot de passe Ou copiez et collez le lien suivant dans votre navigateur</p>
    
    <p>
        <a href="http://127.0.0.1.3000/api/auth/reset/{{ $reset_code }}">
            Reinitialiser mon mot de passe
        </a>
    </p>
    Merci,<br>
    
    <h3 style="font-weight:bold; text-align:center">IMMOBILIER</h3>


@endcomponent