<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerifikasisTable extends Migration
{
    public function up()
    {
        Schema::create('verifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type', 50)->comment('Jenis verifikasi: identity_check, npwp_check, etc.');
            $table->json('request_data')->nullable()->comment('Parameter yang dikirim ke API');
            $table->json('api_response')->comment('Full response JSON dari API');
            $table->boolean('is_paid')->default(false)->comment('Apakah pengguna dikenakan biaya untuk verifikasi ini');
            $table->string('name')->nullable();
            $table->string('id_number')->nullable();
            $table->string('phone_number')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('verifikasis');
    }
}