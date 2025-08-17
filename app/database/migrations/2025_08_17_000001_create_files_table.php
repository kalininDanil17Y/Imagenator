<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('files', function (Blueprint $t) {
            $t->uuid('uuid')->primary();
            $t->string('s3_key');
            $t->string('original_name')->nullable();
            $t->string('mime')->nullable();
            $t->string('format')->nullable(); // webp|jpg|png|...
            $t->unsignedBigInteger('size')->default(0);
            $t->unsignedInteger('width')->nullable();
            $t->unsignedInteger('height')->nullable();
            $t->string('color', 7)->nullable(); // #RRGGBB
            $t->boolean('is_banned')->default(false);
            $t->foreignId('upload_token_id')->nullable()->constrained('upload_tokens')->nullOnDelete();
            $t->json('tags')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('files'); }
};
