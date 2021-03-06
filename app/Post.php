<?php

namespace App;

use Carbon\Carbon;

class Post extends Model
{
    protected $fillable = [
        'title',
        'body',
        'user_id'
    ];
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function addComment($body, $userId)
    {        
        return Comment::create([
            'body' => $body,
            'post_id' => $this->id,
            'user_id' => $userId
        ]);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    
    public function scopeFilter($query, $filters)
    {
        if  ($month = $filters['month']) {
            $query->whereMonth('created_at', Carbon::parse($month)->month);
        }
        
        if  ($year = $filters['year']) {
            $query->whereYear('created_at', $year);
        }
        
        return $query;
    }
    
    public static function archives()
    {
        return static::selectRaw(
                    'YEAR(created_at) AS `year`, 
                    MONTHNAME(created_at) AS `month`,
                    COUNT(id) AS published')
                ->groupBy('year', 'month')
                ->orderByRaw('min(created_at) desc')
                ->get()
                ->toArray();   
    }
    
    public function tags()
    {
        //$post->tags()->attach($tag);
        //$post->tags()->detach($tag);
        return $this->belongsToMany(Tag::class);
    }
}
