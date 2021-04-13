<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouriteCompany extends Model
{
    use HasFactory;

    protected $fillble = [
        'user_id',
        'companies_id'
    ];
}
