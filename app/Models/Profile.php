<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Profile extends Model implements HasMedia {

    use  HasTranslations, InteractsWithMedia;

    /**
     * Summary of fillable
     * @var array
     */
    protected $fillable = [
        'user_id',
        'phone',
        'bio',
        'address',
        'date_of_birth',
        'gender',
        'social_links',
        'website',
    ];


    /**
     * Summary of translatable
     * @var array
     */
    protected $translatable = ['address', 'gender'];

    /**
     * Summary of casts
     * @var array
     */
    protected $casts = [
        'address'      => 'array',
        'gender'       => 'array',
        'social_links' => 'array',
        'date_of_birth' => 'date',
    ];


    /**
     * Summary of user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Profile>
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * Summary of getCreatedAtAttribute
     * @param mixed $value
     * @return string
     */
    public function getCreatedAtAttribute($value) {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }

    /**
     * Summary of getUpdatedAtAttribute
     * @param mixed $value
     * @return string
     */
    public function getUpdatedAtAttribute($value) {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }

    /**
     * Summary of getDateOfBirthAttribute
     * @param mixed $value
     * @return string
     */
    public  function getDateOfBirthAttribute($value) {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }
}
