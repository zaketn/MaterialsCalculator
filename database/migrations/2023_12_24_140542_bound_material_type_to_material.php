<?php

use App\Models\MaterialType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('items');

            $table->after('name', fn() => [
                $table->foreignIdFor(MaterialType::class)->constrained()->restrictOnDelete(),
                $table->string('unit'),
                $table->float('price'),
                $table->integer('in_stock'),
                $table->integer('reserved'),
                $table->integer('shipped'),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('material_type_id');
            $table->dropColumn('unit');
            $table->dropColumn('price');
            $table->dropColumn('in_stock');
            $table->dropColumn('reserved');
            $table->dropColumn('shipped');

            $table->json('items')->nullable();
        });
    }
};
