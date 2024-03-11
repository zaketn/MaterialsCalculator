<?php

use App\Models\Characteristic;
use App\Models\Variation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('characteristic_variation', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Characteristic::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Variation::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('group_order');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('characteristic_variation');
    }
};
