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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
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
        
        $eventInplayResponse = Http::get($eventInplayUrl.$urlInplayParameter)->json();
        $eventEndedResponse = Http::get($eventEndedUrl.$urlParameter)->json();
        
        $mergedResponse = array();     
        $mergedResponse = array_merge($eventInplayResponse['results'], $eventEndedResponse['results']);
        
        foreach ($mergedResponse as $position=>$event) {
            $extra = $this->getExtraEventData($event['id'],$token);
            $mergedResponse[$position]['stadium_data'] = $extra['stadium_data'] ;
            
        }
        
        Storage::disk('public')->put('SCORES/'.$leagues[$league_id].'/'.$day.'.json', json_encode($mergedResponse));
                     
        
        return response()->json(['success'=>'Json files created!', 'message'=>'test', 'status_code' => 200, 'state' => 'test'], 200);
    }
    
    public function ScoreAllLeagues(Request $request)
    {              

        $sport_id = $request['sport_id'];
        $league_id = $request['league_id'];
        $leagues = json_decode(env('LEAGUES'),true);

        $eventInplayUrl = env('BETSAPI_INPLAY');
        
        $score_directory = env('SCORE_DIRECTORY');
        $token = env('TOKENBETAPI');
        $urlInplayParameter = '?token='.$token.'&sport_id='.$sport_id.'&league_id='.$league_id;
        $sports = env('SPORTS');
        $sports = json_decode($sports,true);

        $eventInplayResponse = Http::get($eventInplayUrl.$urlInplayParameter)->json();

        $page = 1;
        $eventEnded = array();
        //do {
            $urlParameter = '?token='.$token.'&sport_id='.$sport_id.'&page='.$page.'&league_id='.$league_id;
            $data = $this->getData($urlParameter);
            $eventEnded = array_merge($eventEnded,$data);
            //$page++;
        //} while($page <= 2);

        $mergedResponse = array();       
        $mergedResponse = array_merge($eventInplayResponse['results'], $eventEnded);
        foreach ($mergedResponse as $position=>$event) {
            if($event['id']){
            $extra = $this->getExtraEventData($event['id'],$token);
            $mergedResponse[$position]['scores'] = $extra['scores'] ;
            }
        }
        Storage::disk('public')->put('ALL_LEAGUE_SCORES/'.$sports[$sport_id].'/'.$leagues[$league_id].'.json', json_encode($mergedResponse));
        return response()->json(['success'=>'Json files created!', 'message'=>'scores', 'status_code' => 200, 'state' => 'done'], 200);
    }
    
    public function getScoreAllLeagues(Request $request)
    {
        $pagination = $request->per_page;
        $currentPage = $request->page;
        $sport_id = $request->sport_id;
        $league_id = $request->league_id;
        $sports = json_decode(env('SPORTS'),true);
        $leagues = json_decode(env('LEAGUES'),true);
        $allLeafueScoreDirectory = env('ALL_LEAGUES_SCORE_DIRECTORY');
        
        //echo storage_path() .$allLeafueScoreDirectory.$sports[$sport_id].'/'.$leagues[$league_id].".json";
        $file = File::get(storage_path() .$allLeafueScoreDirectory.$sports[$sport_id].'/'.$leagues[$league_id].".json");
        $leagueArray = json_decode($file,TRUE);
        //return response()->json(['success'=>true, 'data'=>$leagueArray, 'status_code' => 200, 'state' => true], 200);   
        
        $offset = ($currentPage * $pagination) - $pagination;
        return $this->responsePaginate(new LengthAwarePaginator(
            array_slice($leagueArray, $offset, $pagination, false), // Only grab the items we need
            count($leagueArray), // Total items
            $pagination, // Items per page
            $currentPage, // Current page
            ['path' => $request->url(), 'query' => $request->query()] // We need this so we can keep all old query parameters from the url
            ));
    }

    public function getExtraEventData($event_id,$token)
    {        
        $eventViewUrl = env('BETSAPI_EVENT_VIEW');
        $eventViewResponse = Http::get($eventViewUrl.'?token='.$token.'&event_id='.$event_id)->json();
        $extraResponse = array();
        if (array_key_exists("scores",$eventViewResponse['results'][0]))
        {
        $extraResponse['scores'] = $eventViewResponse['results'][0]['scores'];
        }
        else{
            $extraResponse['scores']='';
        }
        if (!array_key_exists("scores",$extraResponse))
        {
            $extraResponse['scores']='';
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
    
    function groupArray($array)
    {
        $groupkey = 'league';
        $score_directory = env('SCORE_DIRECTORY');
        /*$array = array(array('name'=>'Juan','color'=>'Azul','edad'=>24),array('name'=>'Juan','color'=>'Rojo','edad'=>24),
            array('name'=>'Juan','color'=>'Verde','edad'=>25),array('name'=>'Pablo','color'=>'Azul','edad'=>25),array('name'=>'Pablo','color'=>'Amarillo','edad'=>25));*/
        
        /*$file = File::get(storage_path() .$score_directory.'Basketball/'.'20210120'.".json");
            $array = json_decode($file,TRUE);*/
        
     if (count($array)>0)
     {
            $keys = array_keys($array[0]['league']);
            $mainKeys = array_keys($array[0]);
            $removekey = array_search('id', $keys);		
            if ($removekey===false)
                    return array("Clave \"$groupkey\" no existe");            
            $groupcriteria = array();
            $return=array();
            foreach($array as $value)
            {
                    $item=null;
                    foreach ($mainKeys as $key)
                    {
                        if($key === 'bet365_id')
                        {
                            if (array_key_exists($key,$value))
                            {
                                $item[$key] = $value[$key];
                            }
                        }
                        else{
                            $item[$key] = $value[$key];
                        }
                            
                    }
                    $busca = array_search($value[$groupkey]['id'], $groupcriteria);
                    if ($busca === false)
                    {
                            $groupcriteria[]=$value[$groupkey]['id'];
                            $return[]=array($groupkey=>$value[$groupkey],'results'=>array());
                            $busca=count($return)-1;
                    }
                    $return[$busca]['results'][]=$item;
            }
            return $return;
        }
        else
               return array();
       }
    function getData($urlParameter){
        $eventEndedUrl = env('BETSAPI_ENDED');
        $eventEndedResponse = Http::get($eventEndedUrl.$urlParameter)->json();
        $data = $eventEndedResponse['results'];
        return $data; //return the results for use
        }

    }
