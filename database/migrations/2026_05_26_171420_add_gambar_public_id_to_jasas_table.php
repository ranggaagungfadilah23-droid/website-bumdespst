<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('jasas', function (Blueprint $table) {
        $table->string('gambar_public_id')->nullable()->after('gambar');
    });
}

public function down()
{
    Schema::table('jasas', function (Blueprint $table) {
        $table->dropColumn('gambar_public_id');
    });
}


};
