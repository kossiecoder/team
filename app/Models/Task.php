<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];
	
	public function creator()
	{
		return $this->belongsTo(User::class, 'creator_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function isFollowedBy($user_id)
    {
        return $this->followers()->wherePivot('user_id', $user_id)->exists();
    }

    public function followersExceptAuthUser()
    {
        return $this->followers()->wherePivot('user_id', '!=', auth()->id());
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function priorityLevel()
    {
        return $this->belongsTo(PriorityLevel::class);
    }
}
