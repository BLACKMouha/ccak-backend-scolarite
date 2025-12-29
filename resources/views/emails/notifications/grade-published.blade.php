@extends('emails.layouts.notification')

@section('content')
    <h2>Nouvelle note disponible</h2>

    <p>Bonjour {{ $user->name }},</p>

    <p>Une nouvelle note a été publiée pour le cours <strong>{{ $metadata['subject'] }}</strong>.</p>

    <div style="background: #f8f8f8; padding: 15px; border-radius: 6px; margin: 20px 0;">
        <p style="margin: 0; font-size: 18px;">
            Note obtenue : <strong>{{ $metadata['score'] }}/20</strong>
        </p>
    </div>

    <a href="{{ config('app.url') }}/grades" class="button">
        Voir mes notes
    </a>
@endsection