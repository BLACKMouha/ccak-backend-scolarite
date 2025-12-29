{{-- resources/views/emails/notifications/password-reset.blade.php --}}
@extends('emails.layouts.notification')

@section('content')
    <h2>Réinitialisation de mot de passe</h2>

    <p>Bonjour {{ $user->name }},</p>

    <p>Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.</p>

    <a href="{{ $metadata['reset_url'] }}" class="button">
        Réinitialiser mon mot de passe
    </a>

    <p>Ce lien expirera dans 60 minutes.</p>

    <p>Si vous n'avez pas demandé de réinitialisation, aucune action n'est requise.</p>
@endsection