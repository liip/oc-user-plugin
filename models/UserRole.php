<?php namespace Liip\User\Models;

use Model;
use RainLab\User\Models\User;
use Auth;

/**
 * Administrator role
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class UserRole extends Model
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'liip_user_roles';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'name' => 'required|between:2,128|unique:liip_user_roles',
        'code' => 'required|unique:liip_user_roles',
    ];

    public $jsonable = [
        'permissions',
    ];

    /**
     * @var array Relations
     */
    public $hasMany = [
        'users' => [User::class, 'key' => 'role_id'],
        'users_count' => [User::class, 'key' => 'role_id', 'count' => true]
    ];

    public function getPermissionsOptions()
    {
        return Auth::listPermissions();
    }
}
