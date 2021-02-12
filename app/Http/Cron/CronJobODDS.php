<?php
//The url you wish to send the POST request to generate Odds
$urlOdds = 'https://bettings-dev.oddsandnews.com/api/json';
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

