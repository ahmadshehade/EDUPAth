<?php

namespace Modules\CourseManagement\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

// use Modules\CourseManagement\Database\Factories\CategoryFactory;

class Category extends Model implements HasMedia {
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'parent_id'
    ];

    /**
     * Summary of casts
     * @return array{name: string, parent_id: string, slug: string}
     */
    protected function casts() {
        return [
            'name' => 'array',
            'slug' => 'string',
            'parent_id' => 'integer'
        ];
    }

    protected $translatable = ['name'];
    /**
     * Summary of parent
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Category, Category>
     */
    public function parent() {
        return $this->belongsTo(self::class, 'parent_id');
    }
    /**
     * Summary of childrens
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Category, Category>
     */
    public function childrens() {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Summary of newFactory
     * @return CategoryFactory
     */
    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }
}
