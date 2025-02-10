<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyUsersTableNullableFields extends Migration
{
    public function up()
    {
        // First, update existing records to have default values
        DB::table('users')->whereNull('admin_id')->update(['admin_id' => 0]);
        DB::table('users')->whereNull('school_system_id')->update(['school_system_id' => 0]);
        DB::table('users')->whereNull('otp_verified')->update(['otp_verified' => false]);
        DB::table('users')->whereNull('dob')->update(['dob' => '2000-01-01']);

        // Now make the columns nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('parent_phone_number')->nullable()->default(null)->change();
            $table->unsignedBigInteger('admin_id')->nullable()->default(null)->change();
            $table->string('parent_email')->nullable()->default(null)->change();
            $table->unsignedBigInteger('school_system_id')->nullable()->default(null)->change();
            $table->string('class_level')->nullable()->default(null)->change();
            $table->boolean('otp_verified')->default(false)->change();
            $table->date('dob')->nullable()->default(null)->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('parent_phone_number')->nullable(false)->change();
            $table->unsignedBigInteger('admin_id')->nullable(false)->change();
            $table->string('parent_email')->nullable(false)->change();
            $table->unsignedBigInteger('school_system_id')->nullable(false)->change();
            $table->string('class_level')->nullable(false)->change();
            $table->boolean('otp_verified')->default(false)->nullable(false)->change();
            $table->date('dob')->nullable(false)->change();
        });
    }
}
