<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'published', 'unpublished', 'solved') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'published') NOT NULL DEFAULT 'pending'");
    }
};
