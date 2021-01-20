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
/**
 * Description of JsonScoreController
 *
 * @author root
 */
class JsonScoreController extends ApiController{
    public function store(Request $request)
    {              
        $token = $request['token'];
        $sport_id = $request['sport_id'];
        $league_id = $request['league_id'];
        $day = $request['day'];
        
        $eventEndedUrl = env('BETSAPI_ENDED');
        $eventUpcommingUrl = env('BETSAPI_UPCOMMING');
        $eventInplayUrl = env('BETSAPI_INPLAY');
        
        $score_directory = env('SCORE_DIRECTORY');
        
        $urlParameter = '?token='.$token.'&sport_id='.$sport_id.'&day='.$day.'&league_id='.$league_id;
        $urlInplayParameter = '?token='.$token.'&sport_id='.$sport_id.'&league_id='.$league_id;
        
        $leagues = json_decode(env('LEAGUES'),true);
        
        $eventUpcommingResponse = Http::get($eventUpcommingUrl.$urlParameter)->json();
        $eventInplayResponse = Http::get($eventInplayUrl.$urlInplayParameter)->json();
        $eventEndedResponse = Http::get($eventEndedUrl.$urlParameter)->json();
        
        $mergedResponse = array();
        $mergedResponse = array_merge($eventUpcommingResponse['results'],$eventInplayResponse['results']);        
        $mergedResponse = array_merge($mergedResponse, $eventEndedResponse['results']);
        
        foreach ($mergedResponse as $position=>$event) {
            $extra = $this->getExtraEventData($event['id'],$token);
            $mergedResponse[$position]['stadium_data'] = $extra['stadium_data'] ;
            
        }
        
        Storage::disk('public')->put('SCORES/'.$leagues[$league_id].'/'.$day.'.json', json_encode($mergedResponse));
                     
        
        return response()->json(['success'=>'Json files created!', 'message'=>'test', 'status_code' => 200, 'state' => 'test'], 200);
    }
    
    public function getExtraEventData($event_id,$token)
    {
        $eventViewUrl = env('BETSAPI_EVENT_VIEW');
        $eventViewResponse = Http::get($eventViewUrl.'?token='.$token.'&event_id='.$event_id)->json();
        $extraResponse = array();
        if (array_key_exists("extra",$eventViewResponse['results'][0]))
        {
        $extraResponse = $eventViewResponse['results'][0]['extra'];
        }
        else{
            $extraResponse['stadium_data']='';
        }
        return $extraResponse;
    }
    
    public function index(Request $request)
    {
        $league_id = $request->league_id;
        $day = $request->day;
        $leagues = json_decode(env('LEAGUES'),true);
        $score_directory = env('SCORE_DIRECTORY');
        
            
            $file = File::get(storage_path() .$score_directory.$leagues[$league_id].'/'.$day.".json");
            $leagueArray = json_decode($file,TRUE);
            return response()->json(['success'=>true, 'data'=>$leagueArray, 'status_code' => 200, 'state' => true], 200);
        

        
    }
}
