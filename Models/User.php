<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'users';

    protected $fillable = [
        'id',
        'user_id',
        'admin_id',
        'name',
        'middle_name',
        'last_name',
        'username',
        'user_subject',
        'email',
        'other_email',
        'phone_number',
        'parent_phone_number',
        'parent_email',
        'email_verified_at',
        'password',
        'api_token',
        'school_level_id',
        'school_system_id',
        'class_level',
        'level_id',
        'platform_id',
        'gender_id',
        'profile_image',
        'user_type',
        'activation_status',
        'confirmed',
        'verification_code',
        'is_logged_in',
        'last_login',
        'referral_code',
        'last_logout',
        'remember_token',
        'otp_verified',
        'dob',
        'reset',
        'state',
        'account_type',
        'exam_quiz_free_acc',
        'introducer',
        'price_package_id',
        'updated_at',
        'created_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Boot the model and set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Set default values for required fields
            if (empty($user->admin_id)) {
                $user->admin_id = 1;
            }
            if (empty($user->school_system_id)) {
                $user->school_system_id = 1;
            }
            if (empty($user->school_level_id)) {
                $user->school_level_id = 1;
            }
            if (empty($user->otp_verified)) {
                $user->otp_verified = 0;
            }
            if (empty($user->confirmed)) {
                $user->confirmed = 0;
            }
            if (empty($user->is_logged_in)) {
                $user->is_logged_in = 0;
            }
            if (empty($user->exam_quiz_free_acc)) {
                $user->exam_quiz_free_acc = 0;
            }
            // Set default values for other required fields
            if (empty($user->class_level)) {
                $user->class_level = null;
            }
            if (empty($user->level_id)) {
                $user->level_id = 1;
            }
            if (empty($user->platform_id)) {
                $user->platform_id = 1;
            }
            if (empty($user->gender_id)) {
                $user->gender_id = 1;
            }
            if (empty($user->activation_status)) {
                $user->activation_status = 'active';
            }
            if (empty($user->verification_code)) {
                $user->verification_code = null;
            }
            if (empty($user->referral_code)) {
                $user->referral_code = null;
            }
            if (empty($user->reset)) {
                $user->reset = null;
            }
            if (empty($user->state)) {
                $user->state = 'active';
            }
            if (empty($user->account_type)) {
                $user->account_type = 'student';
            }
            if (empty($user->introducer)) {
                $user->introducer = null;
            }
            if (empty($user->price_package_id)) {
                $user->price_package_id = 1;
            }
            if (empty($user->exam_body)) {
                $user->exam_body = null;
            }
            // Set null for nullable fields that might cause issues
            if (!isset($user->parent_phone_number)) {
                $user->parent_phone_number = null;
            }
            if (!isset($user->parent_email)) {
                $user->parent_email = null;
            }
            if (!isset($user->middle_name)) {
                $user->middle_name = null;
            }
        });
    }
}
