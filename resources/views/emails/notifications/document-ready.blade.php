@extends('emails.layouts.notification')

@section('content')
    <h2>Document disponible</h2>

    <p>Bonjour {{ $user->name }},</p>

    <p>Le document <strong>{{ $metadata['document_name'] }}</strong> est maintenant disponible.</p>

    <a href="{{ $metadata['download_url'] }}" class="button">
        Télécharger le document
    </a>
@endsection