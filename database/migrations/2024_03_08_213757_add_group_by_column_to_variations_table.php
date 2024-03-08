<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('variations', function (Blueprint $table) {
            $table->unsignedBigInteger('group_by')
                ->nullable()
                ->after('product_id');

            $table->foreign('group_by')
                ->references('id')
                ->on('characteristics')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('variations', function (Blueprint $table) {
            $table->dropForeign('variations_group_by_foreign');
            $table->dropColumn('group_by');
        });
    }
};
