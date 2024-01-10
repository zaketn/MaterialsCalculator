<?php

use App\Models\MaterialType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::drop('materials');

        Schema::create('materials', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->foreignIdFor(MaterialType::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->string('unit');
            $table->float('price');
            $table->integer('in_stock');
            $table->integer('reserved');
            $table->integer('shipped');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('materials');

        Schema::create('materials', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->foreignIdFor(MaterialType::class)
                ->constrained()
                ->restrictOnDelete();

            $table->string('unit');
            $table->float('price');
            $table->integer('in_stock');
            $table->integer('reserved');
            $table->integer('shipped');

            $table->timestamps();
        });
    }
};
