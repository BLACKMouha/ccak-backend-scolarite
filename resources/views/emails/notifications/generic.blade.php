@extends('emails.layouts.notification')

@section('content')
    <h2>{{ $notification->title }}</h2>

    <p>Bonjour {{ $user->name }},</p>

    <p>{{ $notification->message }}</p>

    <a href="{{ config('app.url') }}/notifications" class="button">
        Voir toutes les notifications
    </a>
@endsection