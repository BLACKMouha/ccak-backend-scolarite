<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Document - {{ $metadata['title'] ?? $documentType }}</title>
</head>
<body>
<h1>Document: {{ $documentType }}</h1>
<p>Généré le: {{ $generationDate }}</p>

@if(!empty($metadata))
    <h2>Informations:</h2>
    <ul>
        @foreach($metadata as $key => $value)
            <li><strong>{{ $key }}:</strong>
                @if(is_array($value))
                    {{ json_encode($value) }}
                @else
                    {{ $value }}
                @endif
            </li>
        @endforeach
    </ul>
@endif
@include('documents.partials.qrcode')
</body>
</html>
