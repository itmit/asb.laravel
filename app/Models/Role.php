<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Zizaco\Entrust\EntrustRole;

/**
 * Class Role
 * @package App\Models
 */
class Role extends EntrustRole
{
    /**
     * Возвращает пользователей, по нозванию роли к которой они пренадлежат.
     *
     * @param string $roleName
     * @return Collection
     */
    public static function getUsersByRoleName(string $roleName) : Collection
    {
        return self::where('name', '=', $roleName)->first()->users()->get();
    }
}