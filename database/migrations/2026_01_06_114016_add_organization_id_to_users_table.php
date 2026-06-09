<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom organization_id setelah role
            $table->unsignedBigInteger('organization_id')
                  ->nullable()
                  ->after('role');
            
            // Tambahkan foreign key constraint
            $table->foreign('organization_id')
                  ->references('organization_id')
                  ->on('organizations')
                  ->onDelete('set null');
            
            // Index untuk performa
            $table->index('organization_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key constraint
            $table->dropForeign(['organization_id']);
            
            // Hapus kolom
            $table->dropColumn('organization_id');
        });
    }
};