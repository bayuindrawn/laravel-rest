<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

    protected $fillable = [
        'name',
        'address',
        'phone'
    ];

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public static function getAll($payload)
    {
        if(!empty($payload['searchKey'])){
            return self::where('name', 'like', '%'.$payload['searchKey'].'%')->get();
        }
        return self::get();
    }
}
