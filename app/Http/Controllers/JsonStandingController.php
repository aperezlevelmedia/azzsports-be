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
use File;
/**
 * Description of JsonStandingController
 *
 * @author root
 */
class JsonStandingController extends ApiController{
    
    public function store(Request $request)
    {
        $league_url = env('LEAGUE');
        $response = Http::get($league_url.$request['sport_id'])->body();
        $league = json_decode($response,True);
       
        $sports = env('SPORTS');
        $sports = json_decode($sports,true);
        
        $standings = array();
        foreach($league['results'] as $singleLeague)
        {            
            if($singleLeague['has_leaguetable'] == "1")
            {
                
                $league_table_url = env('LEAGUE_TABLE');
                $response = Http::get($league_table_url.$singleLeague['id'])->body();
                $standingsResponse = json_decode($response,True);
                $standingsResponse['results']['league_name'] = $singleLeague['name'];
                $standings[] = $standingsResponse['results'];
            }
            
        }
        Storage::disk('public')->put('STANDINGS/'.$sports[$request['sport_id']].'.json', json_encode($standings));
        return response()->json(['data'=>$standings, 'success'=>'Json files created!', 'message'=>'test', 'status_code' => 200, 'state' => True], 200);
        
    }
    
    public function index(Request $request)
    {
        $pagination = $request->per_page;
        $currentPage = $request->page;
        $sport_id = $request->sport_id;
        $sports = json_decode(env('SPORTS'),true);
        $standingsDirectory = env('STANDING_DIRECTORY');
        
        //echo storage_path() .$allLeafueScoreDirectory.$sports[$league_id].'/'.$day.".json";
        $file = File::get(storage_path() .$standingsDirectory.$sports[$sport_id].".json");
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
}
