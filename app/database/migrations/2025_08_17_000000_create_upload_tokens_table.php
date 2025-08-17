<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('upload_tokens', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('token')->unique();
            $t->boolean('active')->default(true);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('upload_tokens'); }
};
