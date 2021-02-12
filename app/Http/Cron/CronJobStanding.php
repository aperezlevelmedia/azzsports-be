<?php
$urlStanding = 'https://bettings-dev.oddsandnews.com/api/jsonStandingLeague/create';
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
