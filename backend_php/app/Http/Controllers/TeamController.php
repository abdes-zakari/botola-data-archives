<?php

namespace App\Http\Controllers;

use App\Models\Team;

class TeamController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get Teams
     *
     * @return @return \Illuminate\Database\Eloquent\Collection
     */

    public function getTeams(){

        return Team::all();
    }

    //
}
