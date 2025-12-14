<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gurus', function (Blueprint $table) {
            
            // 1. Cek NIP
            if (!Schema::hasColumn('gurus', 'nip')) {
                $table->string('nip')->nullable()->after('nama');
            }
    
            // 2. Cek Mata Pelajaran
            if (!Schema::hasColumn('gurus', 'mata_pelajaran')) {
                $table->string('mata_pelajaran')->nullable()->after('nip');
            }
    
            // 3. Cek No HP
            if (!Schema::hasColumn('gurus', 'no_hp')) {
                $table->string('no_hp')->nullable()->after('mata_pelajaran');
            }
    
            // 4. Cek Foto
            if (!Schema::hasColumn('gurus', 'foto')) {
                $table->string('foto')->nullable()->after('no_hp');
            }
        });
    }
    
    public function down()
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn(['nip', 'mata_pelajaran', 'no_hp', 'foto']);
        });
    }
};
