<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'parent_id', 'code', 'name', 'value',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps()
            ->withPivot('allowed');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function hasSameStatusAsParent(User $user)
    {
        return (($parent = $this->parent) && ($user->hasPermission($this) == $user->hasPermission($parent)));
    }

    public function detachAllChildren(User $user){
        $children = $this->children();
        if($children->count() > 0) {
            foreach ($children->get() as $child) {
                $user->permissions()->detach($child->id);
                $child->detachAllChildren($user);
            }
        }
    }
}
