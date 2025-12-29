@extends('emails.layouts.notification')

@section('content')
    <h2>Bienvenue, {{ $user->name }} !</h2>

    <p>Nous sommes ravis de vous accueillir sur notre plateforme.</p>

    <p>Voici quelques premiers pas pour commencer :</p>
    <ul>
        <li>Complétez votre profil</li>
        <li>Explorez les cours disponibles</li>
        <li>Rejoignez votre première classe</li>
    </ul>

    <a href="{{ config('app.url') }}/dashboard" class="button">
        Accéder au tableau de bord
    </a>

    <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
@endsection