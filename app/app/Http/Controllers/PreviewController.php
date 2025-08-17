<?php

namespace App\Http\Controllers;

use App\Models\FileEntry;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class PreviewController extends Controller
{
    /**
     * GET /{uuid}/-/preview/{w}/{format?}
     *
     * Примеры:
     *  /UUID/-/preview/500/           -> webp по умолчанию (если fmt не задан)
     *  /UUID/-/preview/500/jpeg       -> JPEG
     *  /UUID/-/preview/1200/avif      -> AVIF
     *
     * Query (необяз.): h, fit=fit|cover, q(качество), dpr
     */
    public function show(Request $req, string $uuid, int $w, ?string $format = null)
    {
//        return response()->json([
//            'key' => config('imgproxy.key'),
//            'salt' => config('imgproxy.salt')
//        ]);

        // 1) Ищем запись
        /** @var FileEntry|null $file */
        $file = FileEntry::query()
            ->where('uuid', $uuid)
            ->where('is_banned', false)
            ->first();

        abort_if(!$file, 404);

        // 2) Нормализуем параметры
        $w = max(1, min($w, 4096));
        $h = (int) $req->query('h', 0); // 0 = авто
        $h = max(0, min($h, 4096));

        $fit = $req->query('fit', 'fit'); // fit | cover
        if (!in_array($fit, ['fit', 'cover'], true)) {
            $fit = 'fit';
        }

        $dpr = (int) $req->query('dpr', 1);
        $dpr = max(1, min($dpr, 3));

        $q = (int) $req->query('q', 80);
        $q = max(1, min($q, 100));

        // формат вывода
        $fmt = $format ? strtolower($format) : null;
        $allowedFmt = ['webp','avif','jpg','jpeg','png'];
        if ($fmt && !in_array($fmt, $allowedFmt, true)) {
            $fmt = null;
        }
        if ($fmt === 'jpeg') $fmt = 'jpg';

        // 3) Источник (MinIO/S3)
        $bucket = Config::get('filesystems.disks.s3.bucket');
        // ожидаем, что в БД ключ хранится как "media/<uuid>" или "uploads/2025/..../<uuid>"
        $s3Key  = ltrim($file->s3_key ?? $file->storage_key ?? '', '/');
        abort_if($s3Key === '', 500, 'Empty storage key');

        $sourcePlain = "s3://{$bucket}/{$s3Key}";

        // 4) Опции imgproxy. Синтаксис в духе: rs:fit:{w}:{h}/a:true/dpr:{dpr}/q:{q}
        $opts = [
            "rs:" . ($fit === 'cover' ? 'fill' : 'fit') . ":{$w}:" . ($h ?: 0),
            "ar:true",
            "dpr:{$dpr}",
            "q:{$q}",
        ];

        $mode = config('imgproxy.mode', 'plain');
        $path = $this->buildImgproxyPath($mode, implode('/', $opts), $sourcePlain, $fmt);

        $signed = $this->signPath($path);
        $imgproxyUrl = rtrim(config('imgproxy.url'), '/') . $signed;

        \Log::info('imgproxy_path_before_sign', ['path' => $path]);

        $proxy = filter_var(\config('imgproxy.proxy'), FILTER_VALIDATE_BOOL);
        if (!$proxy) {
            return redirect()->away($imgproxyUrl, 302);
        }

        // Проксирование (если нужно отдавать байты прямо из PHP)
        $resp = Http::timeout(10)->withHeaders([
            'Accept' => '*/*',
        ])->get($imgproxyUrl);

        if (!$resp->ok()) {
            return response()->json([
                'error' => 'imgproxy_failed',
                'status' => $resp->status(),
                'message' => $resp->body(),
                'link' => $imgproxyUrl,
            ], Response::HTTP_BAD_GATEWAY);
        }

        // Пробрасываем контент и кэш-заголовки
        return response($resp->body(), 200, array_filter([
            'Content-Type'  => $resp->header('Content-Type'),
            'Cache-Control' => $resp->header('Cache-Control') ?: 'public, max-age=31536000, immutable',
        ]));
    }

    /**
     * Собирает путь для imgproxy по выбранному режиму.
     * @param string      $mode   'plain' | 'encoded' | 'encrypted'
     * @param string      $opts   "rs:fit:500:0/ar:true/q:80"
     * @param string      $source "s3://bucket/path"
     * @param string|null $fmt    "webp|avif|jpg|png" или null
     */
    private function buildImgproxyPath(string $mode, string $opts, string $source, ?string $fmt): string
    {
        switch ($mode) {
            case 'encoded': {
                // /<sig>/{opts}/{base64url(source)}.{ext}
                $encoded = $this->b64url($source);
                $ext = $fmt ?: 'auto';
                return "/{$opts}/{$encoded}.{$ext}";
            }
            case 'encrypted': {
                // /<sig>/{opts}/enc/{encrypted(source)}.{ext}
                $enc = $this->encryptSource($source);
                $ext = $fmt ?: 'auto';
                return "/{$opts}/enc/{$enc}.{$ext}";
            }
            case 'plain':
            default: {
                // /<sig>/{opts}/plain/{source}@{ext}
                $encodedSource = rawurlencode($source);
                $path = "/{$opts}/plain/{$encodedSource}";
                if ($fmt) {
                    $path .= "@{$fmt}";
                }
                return $path;
            }
        }
    }

    /** URL-safe base64 без '=' */
    private function b64url(string $bin): string
    {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    }

    /**
     * Подпись пути для imgproxy:
     * signature = base64url( HMAC_SHA256( KEY, SALT || path ) )
     * Итоговый URL: /{signature}{path}
     */
    private function signPath(string $path): string
    {
        $keyHex  = config('imgproxy.key');
        $saltHex = config('imgproxy.salt');

        abort_if(strlen($keyHex) === 0 || strlen($saltHex) === 0, 500, 'IMGPROXY_KEY/SALT not set');

        $key  = hex2bin($keyHex);
        $salt = hex2bin($saltHex);
        if ($key === false || $salt === false) {
            abort(500, 'IMGPROXY_KEY/SALT invalid hex');
        }

        $sigBin = hash_hmac('sha256', $salt . $path, $key, true);
        $sig    = $this->b64url($sigBin);

        return '/' . $sig . $path;
    }

    /**
     * Шифрование источника (режим encrypted).
     * По актуальным сборкам imgproxy: AES-256-CTR, IV = нули.
     * Важно: использует те же KEY/SALT, что и подпись.
     * Если используешь отдельные ключи шифрования — выдели их под IMGPROXY_ENCRYPTION_*.
     */
    private function encryptSource(string $src): string
    {
        $keyHex  = config('imgproxy.key');
        $saltHex = config('imgproxy.salt');

        $key  = hex2bin($keyHex);
        $salt = hex2bin($saltHex);
        if ($key === false || $salt === false) {
            abort(500, 'IMGPROXY_KEY/SALT invalid hex');
        }

        // В большинстве релизов достаточно aes-256-ctr с нулевым IV.
        // Если используешь специфичную версию imgproxy со своей KDF — проверь README к твоей версии.
        $iv = str_repeat("\0", 16);
        $cipher = openssl_encrypt($src, 'aes-256-ctr', $key, OPENSSL_RAW_DATA, $iv);
        if ($cipher === false) {
            abort(500, 'Encryption failed');
        }
        return $this->b64url($cipher);
    }
}
