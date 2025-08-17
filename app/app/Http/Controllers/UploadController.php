<?php

namespace App\Http\Controllers;

use App\Models\FileEntry;
use App\Models\UploadToken;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ColorThief\ColorThief;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UploadController extends Controller
{
    public function store(Request $req)
    {
        // Токен: X-Upload-Token или ?token=
        $tokenPlain = $req->header('X-Upload-Token', $req->query('token'));
        abort_unless($tokenPlain, 401, 'Upload token required');

        $token = UploadToken::where('token', $tokenPlain)->where('active', true)->first();
        abort_unless($token, 403, 'Invalid token');

        $req->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,avif|max:25600', // до 25MB
            'tags' => 'array',
        ]);

        $file = $req->file('file');
        $uuid = (string) Str::uuid();
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $mime = $file->getMimeType();
        $size = $file->getSize();

        $key = sprintf('uploads/%s/%s.%s', now()->format('Y/m/d'), $uuid, $ext);

        // Считаем метаданные (w,h, dominant color)
        $manager = new ImageManager(new Driver());

        try {
            $img = $manager->read($file->getPathname());
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Unsupported image format for current driver (try jpg/png/webp).',
                'details' => $e->getMessage(),
            ], 422);
        }

        $width = $img->width();
        $height = $img->height();

        // Dominant color (#RRGGBB)
        try {
            [$r,$g,$b] = ColorThief::getColor($file->getPathname());
            $hex = sprintf("#%02x%02x%02x", $r, $g, $b);
        } catch (\Throwable $e) {
            $hex = null;
        }

        // 100x100 превью base64 (webp)
        $preview = (clone $img)->scaleDown(100, 100)->toWebp(80);
        $base64preview = base64_encode($preview->toString());

        // Загрузка в S3
        $disk = Storage::disk('s3');
        $disk->put($key, file_get_contents($file->getPathname()), [
            'visibility' => 'public',
            'ContentType' => $mime,
        ]);

        // Сохраняем запись
        FileEntry::create([
            'uuid' => $uuid,
            's3_key' => $key,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $mime,
            'format' => $ext,
            'size' => $size,
            'width' => $width,
            'height' => $height,
            'color' => $hex,
            'is_banned' => false,
            'upload_token_id' => $token->id,
            'tags' => $req->input('tags', []),
        ]);

        // Ответ в стиле Osnova
        return response()->json([
            'files' => [[
                'uuid' => $uuid,
                'type' => 1,
                'dateUploaded' => now()->toIso8601String(),
                'v' => 2,
                'tags' => $req->input('tags', []),
                'is_banned' => false,
                'base64preview' => $base64preview,
                'size' => $size,
                'width' => $width,
                'height' => $height,
                'format' => $ext,
                'mimeType' => $mime,
                'color' => $hex,
            ]],
        ], 201);
    }
}
