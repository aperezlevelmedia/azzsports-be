<?php
$urlScores = 'http://127.0.0.1:8000/api/jsonScore/create/allLeagueScores';

//Request scores NBA
$sportIdB = 18;
$idsB = [2274,2638];
// array of curl handles
$multiCurl1 = array();
// data to be returned
$result1 = array();
// multi handle
$mh1 = curl_multi_init();
foreach ($idsB as $i => $id) {
  $multiCurl1[$i] = curl_init();
  $fields_string1= array(
        'league_id' => $id,
         'sport_id' => $sportIdB
      );
  curl_setopt($multiCurl1[$i], CURLOPT_URL,$urlScores);
  curl_setopt($multiCurl1[$i], CURLOPT_POSTFIELDS, $fields_string1);
  curl_setopt($multiCurl1[$i], CURLOPT_HEADER,0);
  curl_setopt($multiCurl1[$i], CURLOPT_RETURNTRANSFER,1);
  curl_multi_add_handle($mh1, $multiCurl1[$i]);
}
$index1=null;
do {
  curl_multi_exec($mh1,$index1);
} while($index1 > 0);
// get content and remove handles
foreach($multiCurl1 as $k1 => $ch1) {
  $result1[$k1] = curl_multi_getcontent($ch1);
  curl_multi_remove_handle($mh1, $ch1);
}
// close
curl_multi_close($mh1);

//Request scores Football NFL
$sportIdF = 12;
$idsF = [474,459];
// array of curl handles
$multiCurl1 = array();
// data to be returned
$result1 = array();
// multi handle
$mh1 = curl_multi_init();
foreach ($idsF as $i => $id) {
  $multiCurl1[$i] = curl_init();
  $fields_string1= array(
        'league_id' => $id,
         'sport_id' => $sportIdF
      );
  curl_setopt($multiCurl1[$i], CURLOPT_URL,$urlScores);
  curl_setopt($multiCurl1[$i], CURLOPT_POSTFIELDS, $fields_string1);
  curl_setopt($multiCurl1[$i], CURLOPT_HEADER,0);
  curl_setopt($multiCurl1[$i], CURLOPT_RETURNTRANSFER,1);
  curl_multi_add_handle($mh1, $multiCurl1[$i]);
}
$index1=null;
do {
  curl_multi_exec($mh1,$index1);
} while($index1 > 0);
// get content and remove handles
foreach($multiCurl1 as $k1 => $ch1) {
  $result1[$k1] = curl_multi_getcontent($ch1);
  curl_multi_remove_handle($mh1, $ch1);
}
// close
curl_multi_close($mh1);

//Score MLB
$sportIdBaseball = 16;
$league_idM = 225;

//reuqest Odds
$ch = curl_init();

//set the url on post method
  $fields_string= array(
        'league_id' => $league_idM,
         'sport_id' => $sportIdBaseball,
      );
curl_setopt($ch,CURLOPT_URL, $urlScores);
curl_setopt($ch,CURLOPT_POST, true);
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

//So that curl_exec returns the contents of the cURL; rather than echoing it
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

//execute post
$result = curl_exec($ch);

//Score NHL
$sportId = 17;
$league_id = 1926;
// array of curl handles

//reuqest Odds
$ch2 = curl_init();

//set the url on post method
  $fields_string1= array(
        'league_id' => $league_id,
         'sport_id' => $sportId,
      );
curl_setopt($ch2,CURLOPT_URL, $urlScores);
curl_setopt($ch2,CURLOPT_POST, true);
curl_setopt($ch2,CURLOPT_POSTFIELDS, $fields_string1);

//So that curl_exec returns the contents of the cURL; rather than echoing it
curl_setopt($ch2,CURLOPT_RETURNTRANSFER, true);

//execute post
$result2 = curl_exec($ch2);
