<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Profile extends Model
{

    use  HasTranslations;

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
    protected $translatable = ['address', 'gender', 'social_links'];

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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
