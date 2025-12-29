<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Relevé de Notes - {{ $student_name ?? '' }}</title>
    @include('documents.partials.styles')
</head>
<body>
@include('documents.partials.header')

<div class="content">
    <h2>Relevé de Notes Officiel</h2>

    <div class="student-info">
        <p><strong>Étudiant:</strong> {{ $student_name }}</p>
        <p><strong>Matricule:</strong> {{ $student_number }}</p>
        <p><strong>Programme:</strong> {{ $program }}</p>
        <p><strong>Année académique:</strong> {{ $academic_year }}</p>
        <p><strong>Semestre:</strong> {{ $semester }}</p>
    </div>

    @if(isset($courses) && count($courses) > 0)
        <table class="grades-table">
            <thead>
            <tr>
                <th>Code UE</th>
                <th>Unité d'Enseignement</th>
                <th>Crédits</th>
                <th>Note</th>
                <th>Mention</th>
            </tr>
            </thead>
            <tbody>
            @foreach($courses as $course)
                <tr>
                    <td>{{ $course['code'] ?? '' }}</td>
                    <td>{{ $course['name'] ?? '' }}</td>
                    <td>{{ $course['credits'] ?? '' }}</td>
                    <td>{{ $course['grade'] ?? '' }}</td>
                    <td>{{ $course['mention'] ?? '' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    <div class="summary">
        <p><strong>Moyenne générale:</strong> {{ $average ?? 'N/A' }}</p>
        <p><strong>Crédits obtenus:</strong> {{ $credits_earned ?? '0' }} / {{ $total_credits ?? '0' }}</p>
        @if(isset($jury_decision))
            <p><strong>Décision du jury:</strong> {{ $jury_decision }}</p>
        @endif
    </div>
</div>

@include('documents.partials.footer')
</body>
</html>
