<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iphones', function (Blueprint $table) {
            $table->id();
            $table->string('model', 100)->nullable();
            $table->string('storage', 20)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('kondisi', 20)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('status', 20)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iphones');
    }
};
