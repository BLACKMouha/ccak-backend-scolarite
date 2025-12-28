@extends('emails.layouts.notification')

@section('content')
    <h2>Inscription confirmée</h2>

    <p>Bonjour {{ $user->name }},</p>

    <p>Votre inscription au cours <strong>{{ $metadata['course_name'] }}</strong> a été confirmée avec succès.</p>

    <p>Vous pouvez maintenant accéder au contenu du cours et commencer votre apprentissage.</p>

    <a href="{{ config('app.url') }}/courses/{{ $metadata['enrollment_id'] }}" class="button">
        Accéder au cours
    </a>
@endsection