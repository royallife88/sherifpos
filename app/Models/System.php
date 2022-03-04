<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class System extends Model
{
    use HasFactory;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function getProperty($key = null)
    {
        $row = System::where('key', $key)
            ->first();

        if (isset($row->value)) {
            return $row->value;
        } else {
            return null;
        }
    }

    public static function saveProperty($key, $value)
    {
        $row = System::where('key', $key)
            ->first();

        if ($row) {
            $row->value = $value;
            $row->save();
        } else {
            $row = System::create(['key' => $key, 'value' => $value, 'date_and_time' => Carbon::now(), 'created_by' => Auth::user()->id]);
        }

        return $row;
    }

    public static function getLanguageDropdown()
    {
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }

        return $languages;
    }
}
