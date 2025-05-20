<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopUpStatusLogsTable extends Migration
{
    public function up()
    {
        Schema::create('top_up_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topup_id')->constrained('top_ups')->onDelete('cascade');
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'verified', 'rejected']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('top_up_status_logs');
    }
}
