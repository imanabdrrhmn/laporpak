<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegionToReportsAndAllowedRegionsToUsers extends Migration
{
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('region')->nullable()->after('longitude'); 
        });

        Schema::table('users', function (Blueprint $table) {
            $table->json('allowed_regions')->nullable()->after('avatar');
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('region');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('allowed_regions');
        });
    }
}
