<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_characteristic', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(\App\Models\Catalog::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignIdFor(\App\Models\Characteristic::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->integer('value');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_characteristic');
    }
};
