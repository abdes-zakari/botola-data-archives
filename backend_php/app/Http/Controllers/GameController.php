<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use FastRoute\RouteParser\Std;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        //
    }

    /**
     * Get all Games
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index():\Illuminate\Http\JsonResponse{
     
        $games = Game::all();
        // $stats = $this->getStats();
        // $games->put('stats',$stats);
        return response()->json($games);

    }
    
     /**
     * Get games and handle Standing
     *
     * @param  Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function standing():\Illuminate\Http\JsonResponse{

        $games = Game::all();

        $teams = $this->getTeams();

        $standing = $this->standingHandler($games,$teams);
        
        // echo "<pre>";
        // print_r($collection);
        // die;

        return response()->json($standing);

    }

    /**
     * Get Standing by Stage
     *
     * @param  int  $stage
     * @return  \Illuminate\Http\JsonResponse
     */

    public function getStandingByStage(int $stage):\Illuminate\Http\JsonResponse{

        $games = $this->fetchGamesUntilStage($stage);

        $teams = $this->getTeams();

        $standing = $this->standingHandler($games,$teams);

        return response()->json($standing);

    }

    /**
     * Get Teams and affect another colomuns
     *
     * @return @return \Illuminate\Database\Eloquent\Collection
     */

    private function getTeams(){

        $teams = Team::all();

        // $collection = collect(['John Doe', 'Jane Doe']);
        $teams = collect($teams);

        // $collection->dd();
        $teams = $teams->map(function ($item, $key) {
            // return $item."Hola";
            $item->pts = 0;
            $item->played = 0;
            $item->name_full = null;
            $item->gf = 0;
            $item->ga = 0;
            $item->gf = 0;
            $item->win = 0;
            $item->draw = 0;
            $item->los = 0;
            return $item;
        });
        return $teams;
    }

    /**
     * Handle Standing
     *
     * @param  \Illuminate\Database\Eloquent\Collection $games
     * @return \Illuminate\Database\Eloquent\Collection
     */

    private function standingHandler(\Illuminate\Database\Eloquent\Collection $games,$teams){

        // $teams = $this->getTeams();

        $games = $games->map(function ($game, $key) use ($teams) {
            
            if($game->home_score > $game->away_score){

                $teams = $teams->map(function ($team, $key) use ($game) {
                    if($team->id == $game->home_id){
                        $team->pts = $team->pts+3;
                        $team->played = $team->played+1;
                        $team->win = $team->win+1;
                    }
                    if($team->id == $game->away_id){
                        $team->pts = $team->pts+0; 
                        $team->played = $team->played+1;
                        $team->los = $team->los+1;
                    }
                    return $team;
                });
            }
            if($game->home_score < $game->away_score){

                $teams = $teams->map(function ($team, $key) use ($game) {
                    if($team->id == $game->home_id){
                        $team->pts = $team->pts+0;
                        $team->played = $team->played+1;
                        $team->los = $team->los+1;
                    }
                    if($team->id == $game->away_id){
                        $team->pts = $team->pts+3; 
                        $team->played = $team->played+1;
                        $team->win = $team->win+1;
                    }
                    return $team;
                });
            }
            if($game->home_score == $game->away_score){

                $teams = $teams->map(function ($team, $key) use ($game) {
                    if($team->id == $game->home_id){
                        $team->pts = $team->pts+1;
                        $team->played = $team->played+1;
                        $team->draw = $team->draw+1;
                    }
                    if($team->id == $game->away_id){
                        $team->pts = $team->pts+1; 
                        $team->played = $team->played+1;
                        $team->draw = $team->draw+1;
                    }
                    return $team;
                });
            }

            $teams = $teams->map(function ($team, $key) use ($game) {
                if($team->id == $game->home_id){
                    $team->gf = $team->gf + $game->home_score;
                    $team->ga = $team->ga + $game->away_score;
                    $team->name_full = $game->home_name;
                }

                if($team->id == $game->away_id){
                    $team->gf = $team->gf + $game->away_score;
                    $team->ga = $team->ga + $game->home_score;
                    $team->name_full = $game->away_name;
                }
                $team->gd = $team->gf - $team->ga;

                return $team;
            });
            
        });
        $teams = $teams->sortByDesc('gd')->sortByDesc('pts')->values()->all();
        // dd($teams);
        return $teams;

    }
    
     /**
     * Fetch game until given stage
     *
     * @param  int $stage
     * @return \Illuminate\Database\Eloquent\Collection
     */

    private function fetchGamesUntilStage(int $stage){

        return Game::where('stage','<=',$stage)->get();

    }

     /**
     * Get standing by every stage
     *
     * @param  int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStandingEveryStage($id){

        $positions = DB::table('positionteams')->where('team_id', $id)->get();

        // $positions = array_extract( $positions->toArray(), ['position'] );
        $positions = $positions->pluck('position');

        return $positions->prepend(0);
    }

    /**
     * Get standing by every stage OLD
     *
     * @param  int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStandingEveryStageOld(int $id){

        $games = Game::orderBy('stage')->get();
        // $teams = $this->getTeams()->toJson(JSON_PRETTY_PRINT);
        // dd($teams);
        // var_dump(count($games));
        $gamesParts = [];
        for ($i=1; $i <31 ; $i++) { 
            // $gamesPart = $games->slice(0,16);
            $gamesParts[] = $games->slice(0,$i*8);
        }

        // die('HOLA');
        $allPositions = [];
        $tata = collect();
        for ($k=0; $k <30 ; $k++) { 
             $standing = collect($this->standingHandler($gamesParts[$k]));
            //  echo count($standing);
            // print_r($standing);echo"<br> +++++++++++++++++++++++++ ******************* ##########";
            // $allPositions[] = $standing;
            $tata->push($standing->toArray());
            if($k==10){
                // die('END');
                break;
            }
            //  for ($j=0; $j < count($standing) ; $j++) { 
            //      if($standing[$j]->id ==$id){
            //         $allPositions[] = $j+1;
            //      }
            //  }
        }
        dd($tata);
        // $games = $games->slice(0,16);
        // print_r(array_slice($games,100));
        // die;
        return $allPositions;
    }

    /**
     * Get Stats of games 
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getStats():\Illuminate\Http\JsonResponse{

        $games = Game::all();
        $games = $this->buildStats($games);
        $games->put('goalByStage',$this->getGoalsByStage());
        // dd($games);
        return response()->json($games);

    }

    /**
     * Get Stats of games until given stage 
     *
     * @param  int $stage
     * @return \Illuminate\Http\JsonResponse
     */

    public function getStatsByStage(int $stage):\Illuminate\Http\JsonResponse{

        $games = $this->fetchGamesUntilStage($stage);

        return response()->json($this->buildStats($games));

    }

    /**
     * Handle and get stats
     *
     * @param   \Illuminate\Database\Eloquent\Collection $games
     * @return  \Illuminate\Database\Eloquent\Collection
     */

    public function buildStats( \Illuminate\Database\Eloquent\Collection $games){

        $games = $games->toArray();
        $stats = collect([]);
        $allGoals = 0;
        $allYellowCards = 0;
        $allRedCards = 0;
        $allPenos = 0;
        $allMissedPenos = 0;
        $goalsMinutes = [];
        foreach ($games as $game ) {
            // dd($game['home_data']);
            $home_data  = json_decode($game['home_data']);
            $away_data  = json_decode($game['away_data']);
            $countGoalHome = count($home_data->goals)+count($home_data->ownGoals)+count($home_data->penalties);
            $countGoalAway = count($away_data->goals)+count($away_data->ownGoals)+count($away_data->penalties);
            $allGoals = $allGoals + $countGoalHome + $countGoalAway;

            $allYellowCards = $allYellowCards + count($home_data->yellowCards) + count($away_data->yellowCards);

            $allRedCards = $allRedCards + count($home_data->redCards) + count($away_data->redCards);

            $allPenos = $allPenos + count($home_data->penalties) + count($away_data->penalties);

            $allMissedPenos = $allMissedPenos + count($home_data->missedPenalties) + count($away_data->missedPenalties);
            
            // dump($countGoalHome);
            // dd($game);
            $goalsMinutes [] = array_merge($home_data->goals,$away_data->goals,$home_data->ownGoals,$away_data->ownGoals,$home_data->penalties,$away_data->penalties);
            
        }
        $stats->put('goals',$allGoals);
        $stats->put('yellowCards',$allYellowCards);
        $stats->put('redCards',$allRedCards);
        $stats->put('penos',$allPenos);
        $stats->put('missedPenos',$allMissedPenos);
        $goalsMinutes = $this->handleGoalMinutes($goalsMinutes);
        $stats->put('goalMins',$goalsMinutes);
        
        // dd($stats->toArray());
        return $stats;
    }

    /**
     * Get games by given stage
     *
     * @param  int $stage
     * @return \Illuminate\Http\JsonResponse
     */

    public function gamesByStage(int $stage):\Illuminate\Http\JsonResponse{

        // dd();
        return response()->json(Game::where('stage',$stage)->get());
    }

    /**
     * Get Goals by Minutes
     *
     * @param  int $goalsMinutes
     * @return \Illuminate\Support\Collection
     */

    public function handleGoalMinutes(array $goalsMinutes){

        $flattMins =[];
        array_walk_recursive($goalsMinutes, function($v) use (&$flattMins){$flattMins[]=explode("min,",$v)[0];});
        $mins1 = []; // from start to 15 min
        $mins2 = []; // from 15 to 30 min
        $mins3 = []; // from 30 to 45 min
        $mins4 = []; // from 45 to 60 min
        $mins5 = []; // from 60 to 75 min
        $mins6 = []; // from 75 to end

        foreach ($flattMins as $row) {
            if($row<=15) $mins1 [] = $row;
            if($row>15 && $row<=30) $mins2 [] = $row;
            if($row>30 && $row<=45) $mins3 [] = $row;
            if($row>45 && $row<=60) $mins4 [] = $row;
            if($row>60 && $row<=75) $mins5 [] = $row;
            if($row>75) $mins6 [] = $row;

        }
  
        $statsGoalMinutes = collect([
                                    "mins1" => count($mins1),
                                    "mins2" => count($mins2),
                                    "mins3" => count($mins3),
                                    "mins4" => count($mins4),
                                    "mins5" => count($mins5),
                                    "mins6" => count($mins6)
                                    ]);
        // echo"<pre>";print_r($statsGoalMinutes);die;
        return $statsGoalMinutes;
    }

    /**
     * Get Goals by Minutes
     *
     * @param  int $goalsMinutes
     * @return \Illuminate\Support\Collection
     */
    public function getGoalsByStage(){

        $goals = Game::goalsByStage()->map(function ($item, $key) {
            return (int)$item->goals;
        });
        // echo"<pre>";print_r($goals->all());die;
        return $goals;
    }

    /**
     * show Games by Team 
     *
     * @param  int $team_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showGamesByTeam(int $team_id):\Illuminate\Http\JsonResponse{

        return response()->json($this->getGamesByTeam($team_id)->paginate(10));
    }

    /**
     * Get Games by Team 
     *
     * @param  int $team_id
     * @return \Illuminate\Support\Collection
     */
    public function getGamesByTeam(int $team_id){

        return Game::where('home_id',$team_id)->orWhere('away_id',$team_id); 
    }

    /**
     * Get Games by Team 
     *
     * @param  int $team_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showStatsByTeam(int $team_id):\Illuminate\Http\JsonResponse{

        $games = $this->getGamesByTeam($team_id)->get();
        $team = Team::where('id',$team_id)->first();
        $team->pts = 0;
        $team->played = 0;
        $team->name_full = null;
        $team->gf = 0;
        $team->ga = 0;
        $team->gf = 0;
        $team->win = 0;
        $team->draw = 0;
        $team->los = 0;
        $team = collect([$team]);
        // dd($team);
        // dd($games->toArray());
        // $standing = $this->standingHandler($games,$team);
        // $games = $games->toArray();
        $stats = collect([]);
        $homeYellowCards = 0;
        $awayYellowCards = 0;
        $homeRedCards = 0;
        $awayRedCards = 0;
        $allPenos = 0;
        $penosAgainst = 0;
        $allMissedPenos = 0;
        $penosMissedAgainst = 0;
        $goalsMinutes = [];
        $goalsMinutesAgainst = [];
        $homeGoals = 0;
        $homeGoalsAgainst = 0;
        $awayGoals = 0;
        $awayGoalsAgainst=0;
        $homeWins = 0;
        $awayWins = 0;
        $homeLos = 0;
        $awayLos =0;
        $homeDraw = 0;
        $awayDraw = 0;
        $listYellowCard = [];
        $listRedCard = [];
        foreach ($games as $game ) {
            // dd($game->toArray());
            $home_data  = json_decode($game['home_data']);
            $away_data  = json_decode($game['away_data']);
            if($game->home_id == $team_id){
                $homeGoals = $homeGoals + $game->home_score;
                $homeGoalsAgainst = $homeGoalsAgainst + $game->away_score;
                $homeYellowCards = $homeYellowCards + count($home_data->yellowCards);
                $homeRedCards = $homeRedCards + count($home_data->redCards);
                $allPenos = $allPenos + count($home_data->penalties);
                $penosAgainst = $penosAgainst + count($away_data->penalties);
                $allMissedPenos = $allMissedPenos + count($home_data->missedPenalties);
                $penosMissedAgainst = $penosMissedAgainst + count($away_data->missedPenalties);
                if($game->home_score > $game->away_score) $homeWins = $homeWins +1;
                if($game->home_score < $game->away_score) $homeLos = $homeLos +1;
                if($game->home_score == $game->away_score) $homeDraw = $homeDraw +1;
                $goalsMinutes [] = array_merge($home_data->goals,$home_data->penalties);
                $goalsMinutesAgainst [] = array_merge($away_data->goals,$away_data->penalties);
                $listYellowCard [] = $home_data->yellowCards;
                $listRedCard [] = $home_data->redCards;
            }
            if($game->away_id == $team_id){
                $awayGoals = $awayGoals + $game->away_score;
                $awayGoalsAgainst = $awayGoalsAgainst + $game->home_score;
                $awayYellowCards = $awayYellowCards + count($away_data->yellowCards);
                $awayRedCards = $awayRedCards + count($away_data->redCards);
                $allPenos = $allPenos + count($away_data->penalties);
                $penosAgainst = $penosAgainst + count($home_data->penalties);
                $allMissedPenos = $allMissedPenos + count($away_data->missedPenalties);
                $penosMissedAgainst = $penosMissedAgainst + count($home_data->missedPenalties);
                if($game->home_score > $game->away_score) $awayLos = $awayLos +1;
                if($game->home_score < $game->away_score) $awayWins = $awayWins +1;
                if($game->home_score == $game->away_score) $awayDraw = $awayDraw +1;
                $goalsMinutes [] = array_merge($away_data->goals,$away_data->penalties);
                $goalsMinutesAgainst [] = array_merge($home_data->goals,$home_data->penalties);
                $listYellowCard [] = $away_data->yellowCards;
                $listRedCard [] = $away_data->redCards;
            }
        }

        // dump($games->toJson(JSON_PRETTY_PRINT));
        // dd($listYellowCard);
        $goalsByMinutes = $this->handleGoalMinutes($goalsMinutes);//dump($goalsByMinutes);
        $goalsByMinutesAgainst = $this->handleGoalMinutes($goalsMinutesAgainst);
        $scoredListName = $this->getListName($goalsMinutes);
        $yellowCardListName = $this->getListName($listYellowCard);
        $redCardListName = $this->getListName($listRedCard);
        $name_full = $this->getFullName($games,$team_id);
        $stats->put('name_full',$name_full);
        // dd($team[0]->name);
        $stats->put('name_short',$team[0]->name);
        // dd($yellowCardListName);
        $stats->put('played',count($games));
        $stats->put('homeGoals',$homeGoals);
        $stats->put('homeGoalsAgainst',$homeGoalsAgainst);
        $stats->put('awayGoals',$awayGoals);
        $stats->put('awayGoalsAgainst',$awayGoalsAgainst);
        $stats->put('homeWins',$homeWins);
        $stats->put('homeDraw',$homeDraw);
        $stats->put('homeLos',$homeLos);
        $stats->put('awayWins',$awayWins);
        $stats->put('awayDraw',$awayDraw);
        $stats->put('awayLos',$awayLos);
        $stats->put('homeYellowCards',$homeYellowCards);
        $stats->put('awayYellowCards',$awayYellowCards);
        $stats->put('homeRedCards',$homeRedCards);
        $stats->put('awayRedCards',$awayRedCards);
        $stats->put('allPenos',$allPenos);
        $stats->put('allMissedPenos',$allMissedPenos);
        $stats->put('penosAgainst',$penosAgainst);
        $stats->put('penosMissedAgainst',$penosMissedAgainst);
        $stats->put('goalsByMinutes',$goalsByMinutes);
        $stats->put('goalsByMinutesAgainst',$goalsByMinutesAgainst);
        $stats->put('scoredListName',$scoredListName);
        $stats->put('yellowCardListName',$yellowCardListName);
        $stats->put('redCardListName',$redCardListName);
        // dd($stats);
        
        
        // dd($games->first()->toArray());
        return response()->json($stats);
        // dd($test);
        // $standing = collect($standing);
        // return response()->json($standing[0]);
    }

    /**
     * Get list name by counting (Goals,cards,ect ...) and sort it 
     *
     * @param  array $listName
     * @return array
     */

    public function getListName(array $listName):array{
        //
        $flattListName =[];
        array_walk_recursive($listName, function($v) use (&$flattListName){$flattListName[]=explode("min,",$v)[1];});
        // explode(" ",explode("min,",$v)[1])[1]
        
        $nameNotDuplicates = array_unique($flattListName);
        // dump($nameNotDuplicates);
        
        // $flattListName = collect($flattListName);
        // $flattListScored =[];
        // array_walk_recursive($flattListName, function($v) use (&$flattListScored){$flattListScored[]=explode(" ",$v)[1];});
        $flattListName = array_count_values($flattListName); 
        // dump($flattListName);
        // $listNameFinal = [];
        arsort($flattListName);
        
        return $flattListName;
        // return arsort($flattListName);
        
    }

    /**
     * Get Full name of team from Games
     *
     * @param  \Illuminate\Database\Eloquent\Collection $games
     * @param  int $team_id
     * @return string
     */

    public function getFullName(\Illuminate\Database\Eloquent\Collection $games,int $team_id):string{

        $game = $games->first();
        if($team_id == $game->home_id) return $game->home_name;
        if($team_id == $game->away_id) return $game->away_name;

        // dd($game->home_name);
    }

    /**
     * Get one game by given ID
     *
     * @param  int $game_id
     * @return \Illuminate\Http\JsonResponse
     */

    public function oneGame(int $game_id):\Illuminate\Http\JsonResponse{

        $game = Game::where("id",$game_id)->first();
        // dd($game->toArray());
        $home_data = json_decode($game->home_data);
        $away_data = json_decode($game->away_data);
        // dump($home_data);
        $homeEvents = $this->gameEvent($home_data,'home');
        $awayEvents = $this->gameEvent($away_data,'away');
        $gameEvents = collect(array_merge($homeEvents,$awayEvents))->sortBy('min')->values();
        $game->events = $gameEvents;
        // dd($gameEvents);
        // dd();
        return response()->json($game);
    }

    public function alterEvents(array $arrEvents,string $type,string $team):array{
        $events = [];

        foreach ($arrEvents as $ev) {
            $event = (object)[];
            $ev = explode(",",$ev);
            $event->min = str_replace("min","",$ev[0]);
            $event->name = $ev[1];
            $event->type = $type;
            $event->team = $team;
            $events[] = $event;
        }
        return $events;
    }

    public function gameEvent($data,$team){

        $gameEvents = []; 
        $goals = $this->alterEvents($data->goals,'goal',$team);
        $yellowCards = $this->alterEvents($data->yellowCards,'yellow',$team);
        $redCards = $this->alterEvents($data->redCards,'red',$team);
        $ownGoals = $this->alterEvents($data->ownGoals,'owngoal',$team);
        $penalties = $this->alterEvents($data->penalties,'penGoal',$team);
        $missedPenalties = $this->alterEvents($data->missedPenalties,'penMiss',$team);
        $gameEvents = array_merge($goals,$yellowCards,$redCards,$ownGoals,$penalties,$missedPenalties);

        return $gameEvents;
        
    }
       


}


//https://laravel.com/docs/5.8/eloquent-serialization#serializing-to-arrays
//toJson(JSON_PRETTY_PRINT);
//toArray()