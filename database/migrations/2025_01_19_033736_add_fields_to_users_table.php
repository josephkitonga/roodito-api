<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('parent_phone_number')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('parent_email')->nullable();
            $table->unsignedBigInteger('school_system_id')->nullable();
            $table->string('class_level')->nullable();
            $table->boolean('otp_verified')->default(false);
            $table->date('dob')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'parent_phone_number',
                'admin_id',
                'school_system_id',
                'parent_email',
                'class_level',
                'otp_verified',
                'dob'
            ]);
        });
    }
}
