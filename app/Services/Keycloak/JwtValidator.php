<?php

namespace App\Services\Keycloak;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use RuntimeException;

class JwtValidator
{
    public function __construct(private readonly JwksFetcher $jwksFetcher)
    {
    }

    /**
     * Validate and decode a Keycloak JWT.
     *
     * @throws AuthenticationException
     */
    public function validate(string $token): array
    {
        [$header, $payload, $signature] = $this->decodeParts($token);

        $kid = $header['kid'] ?? null;
        if (! $kid) {
            throw new AuthenticationException('Token missing kid.');
        }

        $jwk = $this->jwksFetcher->findKeyByKid($kid);
        if (! $jwk) {
            throw new AuthenticationException('Unknown signing key.');
        }

        $publicKey = $this->jwkToPem($jwk);

        try {
            $decoded = JWT::decode($token, new Key($publicKey, $header['alg'] ?? 'RS256'));
        } catch (\Throwable $e) {
            throw new AuthenticationException('Invalid token: ' . $e->getMessage());
        }

        $claims = (array) $decoded;

        $this->assertIssuer($claims);
        $this->assertAudience($claims);
        $this->assertNotExpired($claims);

        return $claims;
    }

    private function decodeParts(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new AuthenticationException('Malformed token.');
        }

        $json = fn ($p) => json_decode($this->base64UrlDecode($p), true, flags: JSON_THROW_ON_ERROR);

        try {
            return [$json($parts[0]), $json($parts[1]), $parts[2]];
        } catch (\Throwable) {
            throw new AuthenticationException('Unable to decode token.');
        }
    }

    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $data .= str_repeat('=', $padlen);
        }

        return base64_decode(strtr($data, '-_', '+/')) ?: '';
    }

    private function jwkToPem(array $jwk): string
    {
        if (($jwk['kty'] ?? '') !== 'RSA') {
            throw new RuntimeException('Unsupported JWK key type.');
        }

        $n = $this->base64UrlDecode($jwk['n'] ?? '');
        $e = $this->base64UrlDecode($jwk['e'] ?? '');

        if (! $n || ! $e) {
            throw new RuntimeException('Invalid JWK components.');
        }

        $modulus = $this->encodeLength(strlen($n)) . $n;
        $publicExponent = $this->encodeLength(strlen($e)) . $e;

        $rsaPublicKey = "\x30" . $this->encodeLength(strlen($modulus . $publicExponent) + 2) .
            "\x02" . $this->encodeLength(strlen($n)) . $n .
            "\x02" . $this->encodeLength(strlen($e)) . $e;

        $rsaOID = "\x30\x0d\x06\x09\x2a\x86\x48\x86\xf7\x0d\x01\x01\x01\x05\x00";
        $publicKeyDER = "\x30" . $this->encodeLength(strlen($rsaOID . "\x03" . $this->encodeLength(strlen($rsaPublicKey) + 1) . "\x00" . $rsaPublicKey)) .
            $rsaOID . "\x03" . $this->encodeLength(strlen($rsaPublicKey) + 1) . "\x00" . $rsaPublicKey;

        $publicKeyPEM = "-----BEGIN PUBLIC KEY-----\n" .
            chunk_split(base64_encode($publicKeyDER), 64, "\n") .
            "-----END PUBLIC KEY-----\n";

        return $publicKeyPEM;
    }

    private function encodeLength(int $length): string
    {
        if ($length <= 0x7f) {
            return chr($length);
        }

        $temp = ltrim(pack('N', $length), "\x00");
        return chr(0x80 | strlen($temp)) . $temp;
    }

    private function assertIssuer(array $claims): void
    {
        $expected = config('keycloak.issuer');
        if (($claims['iss'] ?? null) !== $expected) {
            throw new AuthenticationException('Invalid issuer.');
        }
    }

    private function assertAudience(array $claims): void
    {
        $aud = Arr::wrap($claims['aud'] ?? []);
        $expected = config('keycloak.audiences', []);
        if (! array_intersect($expected, $aud)) {
            throw new AuthenticationException('Invalid audience.');
        }
    }

    private function assertNotExpired(array $claims): void
    {
        $exp = $claims['exp'] ?? null;
        if (! $exp || Carbon::createFromTimestamp($exp)->isPast()) {
            throw new AuthenticationException('Token expired.');
        }
    }
}
