<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouriteCompanies extends Model
{
    use HasFactory;

    protected $fillble = [
        'user_id',
        'companies_id'
    ];

    public function companies(){
    	return $this->belongsTo(Companies::class);
    }

    public static function storeFav($userID, $payload)
    {
        $selected = $payload['favCompanies'];
        $data = [];

        foreach ($selected as $key => $value) {
            $mdSave = new FavouriteCompanies();
            $mdSave->user_id = $userID;
            $mdSave->companies_id = $value;
            $data[$key] = $mdSave->save();
        }
        return $data;
    }
    
    public static function destroyFav($userID, $payload)
    {
        $selected = $payload['favCompanies'];

        $data = self::where('user_id', $userID)->whereIn('id', $selected)->delete();
        
        return $data;
    }
}
