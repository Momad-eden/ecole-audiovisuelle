<?php

namespace App\Support;

/**
 * Jeton d'aperçu signé et temporaire : permet au site Next.js d'afficher
 * le brouillon d'une page sans qu'elle soit publiée.
 */
final class PreviewToken
{
    public static function make(string $type, int|string $id, int $minutes = 60): string
    {
        $expires = now()->addMinutes($minutes)->timestamp;
        $payload = "{$type}:{$id}:{$expires}";

        return rtrim(strtr(base64_encode($payload.':'.self::sign($payload)), '+/', '-_'), '=');
    }

    /** @return array{type: string, id: string}|null */
    public static function verify(string $token): ?array
    {
        $decoded = base64_decode(strtr($token, '-_', '+/'), true);
        if ($decoded === false || substr_count($decoded, ':') !== 3) {
            return null;
        }

        [$type, $id, $expires, $signature] = explode(':', $decoded);
        if (! hash_equals(self::sign("{$type}:{$id}:{$expires}"), $signature) || (int) $expires < now()->timestamp) {
            return null;
        }

        return ['type' => $type, 'id' => $id];
    }

    private static function sign(string $payload): string
    {
        return hash_hmac('sha256', $payload, (string) config('app.key'));
    }
}
