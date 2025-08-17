<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UploadToken;
use Illuminate\Support\Str;

class UploadTokenSeeder extends Seeder
{
    public function run(): void
    {
        UploadToken::updateOrCreate(
            ['name' => 'default'],
            ['token' => env('SEED_UPLOAD_TOKEN', Str::random(32)), 'active' => true]
        );
    }
}
