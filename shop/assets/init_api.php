<?php
if(!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}
define('API_PATH', 'api.dlcph.com');
define('API_TOKEN', '923660b5-db83-led8-c4ef');
define('API_COID', 'DLC');

$host = API_PATH . $endpoint;

//Initiate cURL request
$ch = curl_init($host);

// Set the header by creating the basic authentication
$headers = array(
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode( API_COID . ":" . API_TOKEN )
);

//Set the headers that we want our cURL client to use.
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Set the RETURNTRANSFER as true so that output will come as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

//Execute the cURL request. Convert response to json
$response = json_decode(curl_exec($ch), true);

//Check if any errors occured.
if(curl_errno($ch)) {
    // throw the an Exception.
    throw new Exception(curl_error($ch));
}

curl_close($ch);

//get the response.
// $data = $response['data'][0];

?>