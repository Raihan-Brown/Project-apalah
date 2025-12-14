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
        // GANTI 'berita' JADI 'beritas' (Pake S)
        Schema::table('beritas', function (Blueprint $table) {
            $table->string('image')->nullable()->after('konten');
        });
    }
    
    public function down()
    {
        // GANTI 'berita' JADI 'beritas' JUGA
        Schema::table('beritas', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
