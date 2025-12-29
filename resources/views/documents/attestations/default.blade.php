<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation - {{ $student_name ?? '' }}</title>
    @include('documents.partials.styles')
</head>
<body>
@include('documents.partials.header')

<div class="content">
    <h2>ATTESTATION DE SCOLARITÉ</h2>

    <div class="recipient">
        <p>À l'attention de : {{ $recipient ?? 'Destinataire' }}</p>
    </div>

    <div class="attestation-body">
        <p>Je soussigné(e), responsable des affaires académiques, atteste que :</p>

        <div class="student-details">
            <p><strong>Nom et prénom(s) :</strong> {{ $student_name }}</p>
            <p><strong>Numéro de matricule :</strong> {{ $student_number }}</p>
            <p><strong>Date de naissance :</strong> {{ $date_of_birth ?? 'N/A' }}</p>
            <p><strong>Lieu de naissance :</strong> {{ $place_of_birth ?? 'N/A' }}</p>
        </div>

        <p>est régulièrement inscrit(e) à l'Université en qualité d'étudiant(e) pour :</p>

        <div class="program-details">
            <p><strong>Programme :</strong> {{ $program ?? 'N/A' }}</p>
            <p><strong>Année académique :</strong> {{ $academic_year ?? 'N/A' }}</p>
            <p><strong>Niveau :</strong> {{ $level ?? 'N/A' }}</p>
            <p><strong>Statut :</strong> {{ $status ?? 'Actif' }}</p>
        </div>

        <p>La présente attestation est délivrée à l'intéressé(e) pour les besoins de {{ $purpose ?? 'ses démarches administratives' }}.</p>
    </div>

    <div class="closing">
        <p>Je vous prie d'agréer, Madame, Monsieur, l'expression de mes salutations distinguées.</p>
    </div>

    <div class="signature-box">
        <p>Le Responsable des Affaires Académiques,</p>
        <p><strong>{{ $director_name ?? 'Nom du Responsable' }}</strong></p>
        <p class="stamp">(Signature et cachet)</p>
    </div>
</div>

@include('documents.partials.qrcode')
@include('documents.partials.footer')
</body>
</html>

