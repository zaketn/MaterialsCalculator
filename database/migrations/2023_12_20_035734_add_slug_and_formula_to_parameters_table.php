<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parameters', function (Blueprint $table) {
            $table->string('slug')
                ->after('name')
                ->unique()
                ->nullable();

            $table->json('formula')
                ->after('slug')
                ->default('[]');
        });
    }

    public function down(): void
    {
        Schema::table('parameters', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('formula');
        });
    }
};
