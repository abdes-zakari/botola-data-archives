<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Game extends Model
{   
    protected $table = "games3";

    public static function goalsByStage(){
       
        $query = DB::table('games3');
        $query = $query->select(DB::raw('SUM(home_score)+SUM(away_score) as "goals"'));
        $query = $query->groupBy("stage");
        return $query->get();
     }
 
    
}
