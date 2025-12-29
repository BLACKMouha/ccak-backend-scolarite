{{-- QR Code pour vérification --}}
<div class="qr-code-section" style="margin-top: 30px; text-align: center;">
    <p style="font-size: 10px; color: #666; margin-bottom: 10px;">
        <strong>Code de vérification:</strong> {{ $document_number }}
    </p>

    {{-- QR Code SVG --}}
    <div style="margin: 0 auto; width: 150px;">
        {!! $qr_code !!}
    </div>

    <p style="font-size: 9px; color: #999; margin-top: 10px;">
        Scanner pour vérifier l'authenticité<br>
        <a href="{{ $verification_url }}" style="color: #666; text-decoration: none;">
            {{ $verification_url }}
        </a>
    </p>
</div>
