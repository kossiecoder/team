<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    const NUM_MESSAGES_DEFAULT = 30;

    protected $guarded = [];

    public function from()
    {
        return $this->belongsTo(User::class, 'from');
    }

    public function to()
    {
        return $this->belongsTo(User::class, 'to');
    }
}
