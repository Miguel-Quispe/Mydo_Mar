<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('muebles', function (Blueprint $table) {
            if (!Schema::hasColumn('muebles', 'estructura_json')) {
                $table->text('estructura_json')->nullable()->after('dimensiones');
            }
        });
    }

    public function down(): void
    {
        Schema::table('muebles', function (Blueprint $table) {
            $table->dropColumn('estructura_json');
        });
    }
};
