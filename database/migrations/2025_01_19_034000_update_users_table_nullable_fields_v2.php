<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Allow invalid dates temporarily
        DB::statement('SET SESSION sql_mode = ""');

        // Set default values for existing records
        DB::statement("UPDATE users SET admin_id = 1 WHERE admin_id IS NULL OR admin_id = ''");
        DB::statement("UPDATE users SET school_system_id = 1 WHERE school_system_id IS NULL OR school_system_id = ''");
        DB::statement("UPDATE users SET otp_verified = 0 WHERE otp_verified IS NULL");
        DB::statement("UPDATE users SET dob = NULL WHERE dob = '0000-00-00'");
        
        // Make columns nullable and set defaults
        DB::statement('ALTER TABLE users 
            MODIFY parent_phone_number VARCHAR(255) NULL DEFAULT NULL,
            MODIFY admin_id BIGINT UNSIGNED NULL DEFAULT NULL,
            MODIFY parent_email VARCHAR(255) NULL DEFAULT NULL,
            MODIFY school_system_id BIGINT UNSIGNED NULL DEFAULT NULL,
            MODIFY class_level VARCHAR(255) NULL DEFAULT NULL,
            MODIFY otp_verified TINYINT(1) NULL DEFAULT 0,
            MODIFY dob DATE NULL DEFAULT NULL');

        // Reset sql_mode back to default
        DB::statement("SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    }

    public function down()
    {
        // Set default values before making columns non-nullable
        DB::statement("UPDATE users SET parent_phone_number = '' WHERE parent_phone_number IS NULL");
        DB::statement("UPDATE users SET admin_id = 1 WHERE admin_id IS NULL");
        DB::statement("UPDATE users SET parent_email = '' WHERE parent_email IS NULL");
        DB::statement("UPDATE users SET school_system_id = 1 WHERE school_system_id IS NULL");
        DB::statement("UPDATE users SET class_level = '' WHERE class_level IS NULL");
        DB::statement("UPDATE users SET otp_verified = 0 WHERE otp_verified IS NULL");
        DB::statement("UPDATE users SET dob = '2000-01-01' WHERE dob IS NULL");

        // Make columns non-nullable
        DB::statement('ALTER TABLE users 
            MODIFY parent_phone_number VARCHAR(255) NOT NULL,
            MODIFY admin_id BIGINT UNSIGNED NOT NULL,
            MODIFY parent_email VARCHAR(255) NOT NULL,
            MODIFY school_system_id BIGINT UNSIGNED NOT NULL,
            MODIFY class_level VARCHAR(255) NOT NULL,
            MODIFY otp_verified TINYINT(1) NOT NULL DEFAULT 0,
            MODIFY dob DATE NOT NULL');
    }
};
