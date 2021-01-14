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
        $league_url = env('LEAGUE');
        $response = Http::get($league_url.$request->sport_id)->body();
        $league = json_decode($response,True);
        return response()->json(['success'=>true, 'data'=>$league, 'status_code' => 200, 'state' => true], 200);
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
        $path = glob(storage_path() .$odd_directory.$league."*.json");
        
        foreach ($path as $singleFile)
        {
            
            $file = File::get($singleFile);
            $leagueArray = json_decode($file,TRUE);
            return response()->json(['success'=>true, 'data'=>$leagueArray, 'status_code' => 200, 'state' => true], 200);
        }

        
    }
    public function store()
    {
        $oddsUrl = env('ODDS_XML');
        $odd_directory = env('ODDS_DIRECTORY');
        $response = Http::get($oddsUrl)->body();
        
        $xml = simplexml_load_string($response);
        $ugly_json = json_decode(json_encode($xml));
        $nice_json = $this->cleanupJson($ugly_json);
        $newJson = json_encode($nice_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $array = json_decode($newJson,TRUE);
        $finalArray = null;
        
        $currentFiles = Storage::disk('public')->allFiles('ODDS/');
        
        foreach($currentFiles as $currentFile)
        {
            Storage::disk('public')->delete($currentFile);
        }       
               
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
                                Storage::disk('public')->put('ODDS/'.str_replace(array(' ','/'), '', $league['name']).date('Ymdhs', time()).'.json', json_encode($league));
                            }
                            else{
                                foreach ($league as $unitKey => $item)
                                {
                                    Storage::disk('public')->put('ODDS/'.str_replace(array(' ','/'), '', $item['name']).date('Ymdhs', time()).'.json', json_encode($item));
                                }
                            }

                            $finalArray = $league;
                            
                        }
                    }

                }
            }
        }
        

        return response()->json(['success'=>'Json files created!', 'message'=>'test', 'status_code' => 200, 'state' => 'test'], 200);
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
