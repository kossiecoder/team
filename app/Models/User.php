<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    private $permissions_cache = [];

    private $allowedPermissionCodes = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function channels()
    {
        return $this->belongsToMany(Channel::class)->using(ChannelUser::class)->withPivot(['is_read']);
    }

    public function channelMessages()
    {
        return $this->hasMany(ChannelMessage::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'creator_id');
    }

    public function followingTasks()
    {
        return $this->belongsToMany(Task::class);
    }

	public function permissions()
    {
        return $this->belongsToMany(Permission::class)
            ->withTimestamps()
            ->withPivot('allowed');
    }
	
	public function allowedPermissions()
    {
        return $this->permissions()->wherePivot('allowed', '=', true);
    }
	
	public function disallowedPermissions()
    {
        return $this->permissions()->wherePivot('allowed', '=', false);
    }
	
	public function __call($method, $arguments)
    {
        foreach (['can', 'allowTo', 'dontAllowTo'] as $permissions_method) {

            if (strpos($method, $permissions_method) === 0) {

                return $this->{$permissions_method}(
                    Helper::camel2snake(substr($method, strlen($permissions_method))),
                    count($arguments) ? $arguments[0] : null
                );
            }
        }

        return parent::__call($method, $arguments);
    }
	
	public function fromMessages()
	{
		return $this->hasMany(Message::class, 'from');
	}
	
	public function can($permission_code, $value = null)
    {
        return ($permission = Permission::whereCode($permission_code)->first())
            ? $this->hasPermission($permission)
            : false;
    }

    public function allowTo($permission_code)
    {
        if ($permission = Permission::whereCode($permission_code)->first()) {
            $this->addPermission($permission);
        }

        return $this;
    }

    public function dontAllowTo($permission_code)
    {
        if ($permission = Permission::whereCode($permission_code)->first()) {
            $this->removePermission($permission);
        }

        return $this;
    }

    public function hasPermission(Permission $permission)
    {
        if (array_key_exists($permission->id, $this->permissions_cache)) {
            return $this->permissions_cache[$permission->id];
        }

        $allowed    = $this->allowedPermissions()->pluck('permission_id')->all();
        $disallowed = $this->disallowedPermissions()->pluck('permission_id')->all();

        while ($permission) {

            if (in_array($permission->id, $disallowed)) {
                return false;
            }

            if (in_array($permission->id, $allowed)) {
                return true;
            }

            $permission = $permission->parent;
        }

        return false;
    }

    public function addPermission(Permission $permission)
    {
        if($parent = $permission->parent()->first()) {
            if($permission->hasSameStatusAsParent($this)) {
                $this->permissions()->attach($permission->id);
            } else {
                $this->permissions()->detach($permission->id);
            }
            $permission->detachAllChildren($this);
        } else {
            $this->permissions()->detach();
            $this->permissions()->attach($permission->id);
        }

        return $this;
    }

    public function removePermission(Permission $permission)
    {
        if($parent = $permission->parent()->first()) {

            $permission->detachAllChildren($this);
            if($permission->hasSameStatusAsParent($this)) {
                $this->permissions()->attach($permission->id, ['allowed' => false]);
            } else {
                $this->permissions()->detach($permission->id);
            }
        } else {
            $this->permissions()->detach();
        }

        return $this;
    }

    public function getPermissionCodes(array $permissions)
    {
        foreach ($permissions as $permission) {
            if($permission['allowed'] === true) {
                $this->allowedPermissionCodes[] = $permission['code'];
            }
            if(isset($permission['children'])) {
                $this->getPermissionCodes($permission['children']);
            }
        }

        return $this->allowedPermissionCodes;
    }

    public function checkChildrenPermission(array $children)
    {

    }

    public function getPermissionTreeArray()
    {
        $permissions = Permission::all();
        $new = [];
        $parent = null;

        foreach($permissions as $permission) {
            $permission_array = $permission->toArray();
            $permission_array['name'] = ucwords($permission->name);
            $permission_array['allowed'] = $this->hasPermission($permission);

            if($permission->parent_id === null) {
                $parent = $permission_array;
            }

            $new[$permission->parent_id][] = $permission_array;
        }

        return self::createPermissionTreeArray($new, [$parent]);
    }

    private function createPermissionTreeArray(&$list, $parent)
    {
        $tree_array = [];
        foreach($parent as $key => $value) {
            if(isset($list[$value['id']])) {
                $value['children'] = self::createPermissionTreeArray($list, $list[$value['id']]);
            }
            $tree_array[] = $value;
        }

        return $tree_array;
    }
}
