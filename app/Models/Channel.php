<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function messages()
    {
        return $this->hasMany(ChannelMessage::class);
    }

    public function updateMessageStatusToNotRead($channel_id)
    {
        return $this->users()->wherePivot('user_id', '!=', auth()->id())->each(function($user) use ($channel_id){
            $user->channels()->updateExistingPivot($channel_id, ['is_read' => 0]);
        });
    }
}
