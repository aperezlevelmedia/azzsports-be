<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use File;
use Illuminate\Filesystem\Filesystem;

/**
 * Description of jsonController
 *
 * @author root
 */
class JsonController extends ApiController{
    public function league(Request $request)
    {       
        $odd_directory = env('ODDS_DIRECTORY');
        //$path = glob(storage_path() .$odd_directory.$league.".json");
        $path = storage_path() .$odd_directory."LEAGUES/leagues.json";            
            
        $file = File::get($path);
        $leagueArray = json_decode($file,TRUE);
        return response()->json(['success'=>true, 'data'=>$leagueArray, 'status_code' => 200, 'state' => true], 200); 
    }
    public function leagueTable(Request $request)
    {   
        $league_table_url = env('LEAGUE_TABLE');
        $response = Http::get($league_table_url.$request->league_id)->body();
        $table = json_decode($response,True);
        return response()->json(['success'=>true, 'data'=>$table, 'status_code' => 200, 'state' => true], 200);
    }
    
    public function index(Request $request)
    {
        $league = $request->name;
        $odd_directory = env('ODDS_DIRECTORY');
        //$path = glob(storage_path() .$odd_directory.$league.".json");
        $path = storage_path() .$odd_directory.$league.".json";            
            
        $file = File::get($path);
        $leagueArray = json_decode($file,TRUE);
        return response()->json(['success'=>true, 'data'=>$leagueArray, 'status_code' => 200, 'state' => true], 200);        

        
    }
    public function store(Request $request)
    {
        $oddsUrl = env('BETSLIPAPI');
        $leagueListUrl = env('BETSLIPAPI_LEAGUES');
        $odd_directory = env('ODDS_DIRECTORY');
        
        $leaguesListBySport = Http::withToken('MTp0ZXN0MQ==')->get($leagueListUrl)->json();        
        $finalLeagueList = array();
        
        foreach($leaguesListBySport['Sport'] as $leagueList)
        {
            $cont = 0;
            foreach($leagueList['Leagues'] as $league)
            {                        
                $finalLeagueList[] = [ 'id' => $league['IdLeague'], 'name' => $league['Description'], 'short_name' => $this->trueFileName($league['Description']) ];                
                $cont++;
            }
        }
        
        Storage::disk('public')->put('ODDS/LEAGUES/'.'leagues.json', json_encode($finalLeagueList));
        
        foreach($finalLeagueList as $singleLeague)
        {   $enabledLeagues = [389,784,272,3,564,7,725,58,60,334,505];
            if(in_array($singleLeague['id'], $enabledLeagues))
            {
                $response = Http::post($oddsUrl, [
                'Gmt' => 4,
                'NextHour' => false,
                'SiteId' => 1,
                'IdPlayer' => 0,
                'Player' => "test10",
                'LineStyle' => "D",
                'LeagueList' => [
                    $singleLeague['id']
                ]
                ])->json();
                $trueFileName = $this->trueFileName($singleLeague['name']);
                Storage::disk('public')->put('ODDS/'.$trueFileName.'.json', json_encode($response));
            }
        }       

        return response()->json(['success'=>'Json files created!', 'message'=>'test', 'status_code' => 200, 'state' => 'test'], 200);
    }
    
    public function trueFileName($fileName)
    {
        $convertedName = "";
        switch ($fileName){
            case 'MLB DIVISION ODDS':
                $convertedName = 'MLB';
                break;
            case 'NATIONAL HOCKEY LEAGUE':
                $convertedName = 'NHL';
                break;
            case 'NBA':
                $convertedName = 'NBA';
                break;
            case 'NCAA BASKETBALL':
                $convertedName = 'NCAAB';
                break;
            case 'NFL SUPER BOWL LV':
                $convertedName = 'NFL';
                break;
            case 'ODDS TO WIN NCAA FOOTBALL CHAMPIONSHIP':
                $convertedName = 'NCAAF';
                break;
            case 'UEFA - CHAMPIONS LEAGUE':
                $convertedName = 'UEFACL';
                break;
            case 'UFC / MMA':
                $convertedName = 'UEFCMMA';
                break;
            case 'ITALY- SERIE A':
                $convertedName = 'ITALYSERIEA';
                break;
            case 'GERMANY- BUNDESLIGA':
                $convertedName = 'GERMANYBUNDESLIGA';
                break;
            case 'ATP TENNIS - SPREAD IS FOR SETS':
                $convertedName = 'ATPTENNISSPREADISFORSETS';
                break;
        }
        return $convertedName;
    }
    public function tabNames()
    {
        $oddsUrl = env('ODDS_XML');
        $response = Http::get($oddsUrl)->body();
        
        $xml = simplexml_load_string($response);
        $ugly_json = json_decode(json_encode($xml));
        $nice_json = $this->cleanupJson($ugly_json);
        $newJson = json_encode($nice_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $array = json_decode($newJson,TRUE);
        $finalArray = array();
        foreach ($array as $sportKey=>$sport) {
            if($sportKey == 'HLW_Sport')
            {
                foreach ($sport as $subSportKey=>$subSport) {


                    foreach ($subSport as $leagueKey => $league)
                    {
                        if($leagueKey == 'HLW_SubSport')
                        {
                            $fileName = '';
                            if(array_key_exists('id', $league))
                            {                                
                                $finalArray[] = str_replace(array(' ','/'), '', $league['name']);
                            }
                            else{
                                foreach ($league as $unitKey => $item)
                                {                                    
                                    $finalArray[] = str_replace(array(' ','/'), '', $item['name']);
                                }
                            }


                        }
                    }

                }
            }
        }
        

        return response()->json(['success'=>true, 'data'=>$finalArray, 'status_code' => 200, 'state' => true], 200);
    }


    function cleanupJson ($ugly_json) {
        if (is_object($ugly_json)) {
           $nice_json = new \stdClass();
           foreach ($ugly_json as $attr => $value) {
              if ($attr == '@attributes') {
                 foreach ($value as $xattr => $xvalue) {
                   $nice_json->$xattr = $xvalue;
                 }
              } else {
                 $nice_json->$attr = $this->cleanupJson($value);
              }
           }
           return $nice_json;
        } else if (is_array($ugly_json)) {
           $nice_json = array();
           foreach ($ugly_json as $n => $e) {
             $nice_json[$n] = $this->cleanupJson($e);
           }
           return $nice_json;
        } else {
           return $ugly_json;
        }
    }

}
