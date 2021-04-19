<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Response;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'email_verified_at',
        'verification_code',
        'is_verified'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     /**
     * Get all of the companys for the project.
     */
    public function favourite_companies()
    {
        return $this->hasMany(FavouriteCompanies::class, 'user_id', 'id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function getAll()
    {
        return self::whereNotNull('id');
    }

    public static function createUser($payload)
    {
        return self::create($payload);
    }

    public static function verifyUser($verificationCode)
    {
        $user = self::where('verification_code', $verificationCode)->first();
        
        if(!empty($user)) {
            $user->email_verified_at = Carbon::now();
            $user->is_verified = 1;
            $user->save();
        }

        return $user;
    }

    public static function changePass($payload, $id)
    {
        $user = self::findOrFail($id);
        
        if(Hash::check($payload['old_password'], $user->password)){
            $user->fill([
                'password' => bcrypt($payload['new_password'])
            ])->save();

            return response()->json(['message' => 'Password changed successfully'] , Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Password changed failed'], Response::HTTP_FAILED_DEPENDENCY);
        }
    }

    public static function getUserFavoriteCompany($userId)
    {
        $data = User::with(['favourite_companies:id,user_id,companies_id', 'favourite_companies.companies:id,name,address,phone'])
        ->where('id', $userId)
        ->first();

        return response()->json([
            'message' => 'Get data user favorite companies successfully',
            'data' => $data
        ] , Response::HTTP_OK);
    }
}
