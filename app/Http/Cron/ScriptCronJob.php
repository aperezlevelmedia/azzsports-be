<?php
//The url you wish to send the POST request to generate Odds
$urlOdds = 'https://bettings-dev.oddsandnews.com/api/json';
$urlStanding = 'https://bettings-dev.oddsandnews.com/api/jsonStandingLeague/create';
$urlScores = 'https://bettings-dev.oddsandnews.com/api/jsonScore';
//reuqest Odds
$ch = curl_init();

//set the url on post method
curl_setopt($ch,CURLOPT_URL, $urlOdds);
curl_setopt($ch,CURLOPT_POST, true);
//curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

//So that curl_exec returns the contents of the cURL; rather than echoing it
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

//execute post
$result = curl_exec($ch);
echo "test";
echo $result;

//Request standing
$ids = [459,2274,2638,474,225,1926];
// array of curl handles
$multiCurl = array();
// data to be returned
$result = array();
// multi handle
$mh = curl_multi_init();
foreach ($ids as $i => $id) {
  $multiCurl[$i] = curl_init();
  $fields_string= array(
        'league_id' => $id,
        );
  curl_setopt($multiCurl[$i], CURLOPT_URL,$urlStanding);
  curl_setopt($multiCurl[$i], CURLOPT_POSTFIELDS, $fields_string);
  curl_setopt($multiCurl[$i], CURLOPT_HEADER,0);
  curl_setopt($multiCurl[$i], CURLOPT_RETURNTRANSFER,1);
  curl_multi_add_handle($mh, $multiCurl[$i]);
}
$index=null;
do {
  curl_multi_exec($mh,$index);
} while($index > 0);
// get content and remove handles
foreach($multiCurl as $k => $ch) {
  $result[$k] = curl_multi_getcontent($ch);
  curl_multi_remove_handle($mh, $ch);
}
// close
curl_multi_close($mh);

//Request scores NBA
$sportId = 18;
$league_id = 2274;
$timestamp = time();
$days = array();
for ($i = 0 ; $i < 5 ; $i++) {
    //echo date('Ymd', $timestamp) . '<br />';
    array_push($days,date('Ymd', $timestamp));
    $timestamp -= 24 * 3600;
}
// array of curl handles
$multiCurl1 = array();
// data to be returned
$result1 = array();
// multi handle
$mh1 = curl_multi_init();
foreach ($days as $i => $day) {
  $multiCurl1[$i] = curl_init();
  echo $day;
  $fields_string1= array(
        'league_id' => $league_id,
         'sport_id' => $sportId,
         'day'=> $day,
         'token' =>'57433-hZTMCMkt9QpYow'
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

//Request scores NCAAB
$sportId = 18;
$league_id = 2638;
$timestamp = time();
// array of curl handles
$multiCurl1 = array();
// data to be returned
$result1 = array();
// multi handle
$mh1 = curl_multi_init();
foreach ($days as $i => $day) {
  $multiCurl1[$i] = curl_init();
  echo $day;
  $fields_string1= array(
        'league_id' => $league_id,
         'sport_id' => $sportId,
         'day'=> $day,
         'token' =>'57433-hZTMCMkt9QpYow'
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
$sportId = 12;
$league_id = 459;
$timestamp = time();
// array of curl handles
$multiCurl1 = array();
// data to be returned
$result1 = array();
// multi handle
$mh1 = curl_multi_init();
foreach ($days as $i => $day) {
  $multiCurl1[$i] = curl_init();
  echo $day;
  $fields_string1= array(
        'league_id' => $league_id,
         'sport_id' => $sportId,
         'day'=> $day,
         'token' =>'57433-hZTMCMkt9QpYow'
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

//Score NCAAF
$sportId = 12;
$league_id = 474;
$timestamp = time();
// array of curl handles
$multiCurl1 = array();
// data to be returned
$result1 = array();
// multi handle
$mh1 = curl_multi_init();
foreach ($days as $i => $day) {
  $multiCurl1[$i] = curl_init();
  echo $day;
  $fields_string1= array(
        'league_id' => $league_id,
         'sport_id' => $sportId,
         'day'=> $day,
         'token' =>'57433-hZTMCMkt9QpYow'
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


//Score MBL
$sportId = 16;
$league_id = 225;
$timestamp = time();
// array of curl handles
$multiCurl1 = array();
// data to be returned
$result1 = array();
// multi handle
$mh1 = curl_multi_init();
foreach ($days as $i => $day) {
  $multiCurl1[$i] = curl_init();
  echo $day;
  $fields_string1= array(
        'league_id' => $league_id,
         'sport_id' => $sportId,
         'day'=> $day,
         'token' =>'57433-hZTMCMkt9QpYow'
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

//Score NHL
$sportId = 17;
$league_id = 1926;
$timestamp = time();
// array of curl handles
$multiCurl1 = array();
// data to be returned
$result1 = array();
// multi handle
$mh1 = curl_multi_init();
foreach ($days as $i => $day) {
  $multiCurl1[$i] = curl_init();
  echo $day;
  $fields_string1= array(
        'league_id' => $league_id,
         'sport_id' => $sportId,
         'day'=> $day,
         'token' =>'57433-hZTMCMkt9QpYow'
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

