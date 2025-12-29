<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Certificat de Scolarité - {{ $student_name ?? '' }}</title>
    @include('documents.partials.styles')
    <style>
        .certificate-container {
            border: 15px solid #2c5282;
            padding: 40px;
            text-align: center;
            margin: 20px;
        }
        .certificate-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #2c5282;
        }
        .student-name {
            font-size: 24px;
            margin: 30px 0;
            font-weight: bold;
        }
        .signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
<div class="certificate-container">
    <div class="certificate-title">CERTIFICAT DE SCOLARITÉ</div>

    <div class="content">
        <p>Je soussigné, le recteur du CCAK certifie que :</p>

        <div class="student-name">{{ $student_name }}</div>

        <p>Né(e) le : {{ $date_of_birth ?? 'N/A' }}</p>
        <p>Matricule : {{ $student_number }}</p>

        <p>est régulièrement inscrit(e) en qualité d'étudiant(e) :</p>

        <p><strong>{{ $program ?? 'N/A' }}</strong></p>
        <p>Année académique : {{ $academic_year ?? 'N/A' }}</p>
        <p>Semestre : {{ $semester ?? 'N/A' }}</p>

        <p>Le présent certificat est délivré pour servir et valoir ce que de droit.</p>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <p>Fait à {{ $city ?? 'Ville' }}, le {{ $generationDate }}</p>
            <p>Le Recteur</p>
            <p class="stamp">(Cachet officiel)</p>
        </div>
    </div>
</div>
@include('documents.partials.qrcode')
</body>
</html>
