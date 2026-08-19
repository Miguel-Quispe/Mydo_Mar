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
            if (!Schema::hasColumn('muebles', 'material')) {
                $table->string('material')->nullable()->after('descripcion');
            }
            if (!Schema::hasColumn('muebles', 'precio')) {
                $table->decimal('precio', 10, 2)->nullable()->after('material');
            }
            if (!Schema::hasColumn('muebles', 'dimensiones')) {
                $table->string('dimensiones')->nullable()->after('precio');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('muebles', function (Blueprint $table) {
            $table->dropColumn(['material', 'precio', 'dimensiones']);
        });
    }
};
