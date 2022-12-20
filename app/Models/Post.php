<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'author_id', 'content', 'slug', 'status'];

    protected static function boot() {
        parent::boot();

        static::creating(function ($post) {
            $post->slug = $post->slug ?? Str::slug($post->title);
        });

        static::updating(function ($post) {
            $post->slug = Str::slug($post->title);
        });
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDraft($query)
    {
        return $query->whereStatus(0);
    }

    public function scopePublish($query)
    {
        return $query->whereStatus(1);
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value ?? 0;
    }
}
