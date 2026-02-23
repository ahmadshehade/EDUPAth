<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Mail\ResetPasswordMail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

class User extends Authenticatable {

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasTranslations, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'github_id'
    ];

    /**
     * Summary of transaltable
     * @var array
     */
    protected $translatable = ['name'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Summary of profile
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<Profile, User>
     */
    public function profile() {
        return $this->hasOne(Profile::class, 'user_id');
    }


    /**
     * Summary of sendPasswordResetNotification
     * @param mixed $token
     * @return void
     */
    public function sendPasswordResetNotification($token) {
        Log::info('Sending password reset email to: ' . $this->email . ' with token: ' . $token);
        Mail::to($this->email)
            ->send(new ResetPasswordMail($this, $token));
    }

    /**
     * Summary of getCreatedAtAttributes
     * @param mixed $value
     * @return string
     */
    public function getCreatedAtAttributes($value) {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }

    /**
     * Summary of getUpdatedAtAttributes
     * @param mixed $value
     * @return string
     */
    public function getUpdatedAtAttributes($value) {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }
}
