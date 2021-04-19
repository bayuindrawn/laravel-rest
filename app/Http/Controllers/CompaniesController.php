<?php

namespace App\Http\Controllers;

use App\Models\Companies;
use App\Models\FavouriteCompanies;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompaniesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Companies::getAll($request);
        return $user;
    }

    public function getFav()
    {
        $user = User::getUserFavoriteCompany($this->guard()->user()->id);
        return $user;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $companies = FavouriteCompanies::storeFav($this->guard()->user()->id, $request);
        return $companies;
    }

    public function destroy(Request $request)
    {
        $companies = FavouriteCompanies::destroyFav($this->guard()->user()->id, $request);
        return $companies;
    }

    protected function guard()
    {
        return Auth::guard();
    }
}
