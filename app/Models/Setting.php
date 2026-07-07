<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // app/Models/Setting.php

protected $fillable = ['key', 'value', 'group'];

public static function getValue($key, $default = null)
{
    $setting = self::where('key', $key)->first();
    return $setting ? $setting->value : $default;
}

public static function getGroup($group)
{
    return self::where('group', $group)->pluck('value', 'key')->toArray();
}
}
