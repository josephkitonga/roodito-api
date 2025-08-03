<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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

    protected $fillable = [
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
        'exam_body',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
