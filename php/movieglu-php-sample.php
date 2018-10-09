<?php

##############
# API METHOD #
##############

$api = 'filmsNowShowing/?n=10';
// See documentation for other methods available.
// https://developer.movieglu.com/v2/api-index/

####################
# REQUIRED HEADERS #
####################

// You can find the details below in the email you received when you registered for a MovieGlu evaluation account.
$api_endpoint = 'https://api-gate2.movieglu.com/';
$username = 'YOUR USERNAME'; // Example: $username = 'ABCD';
$api_key = 'YOUR X-API-KEY';  //Example: $api_key = 'AbCdEFG7CuTTc6KX76mI5aAoGtqbrGW2ga6B4jRg';
$basic_authorization = 'YOUR AUTHORIZATION'; // Example: $basic_authorization = 'Basic UHSYGF4xNTpNOHdJQllxckYyN3y=';
$territory = 'UK'; // Territory chosen as part of your evaluation key request  (Options: UK, FR, ES, DE, US, CA, IE, IN)
$api_version = 'v200'; // API Version for evaluation - check documentation for later versions
$device_datetime = (new DateTime())->format('Y-m-d H:i:s'); // Current device date/time 
$geolocation = '51.510408;-0.130105'; // Device Geolocation. Note semicolon (;) used as separator. IMPORTANT: This MUST be a location in the territory you selected above. The sample location is set at: Leicester Square, London, UK

########
# cURL #
########

// Initialize a cURL session
$ch = curl_init();

// Assign cURL Settings
curl_setopt($ch, CURLOPT_URL, $api_endpoint . $api);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Authorization: ' . $basic_authorization, 
  'client: ' . $username,
  'x-api-key: ' . $api_key,  
  'territory: ' . $territory,
  'api-version: ' .$api_version,
  'device-datetime: ' . $device_datetime,
  'geolocation: ' .$geolocation 
 ]
);

// Send cURL request 
$ret = curl_exec($ch);

// Get HTTP Response Code
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Separate Headers and Body 
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($ret, 0, $header_size);
$body = substr($ret, $header_size);

// Close cURL request
curl_close($ch);

######################
# OUTPUT HTTP STATUS #
######################

echo '<p> Returned Status: <b>' . $http_code . '</b></p>';

###############################
# EXTRACT & OUTPUT MG-MESSAGE #
###############################

//Extract mg-message from response headers (Possibly not required)
//MovieGlu generated error message, providing guidance on possible causes of errors, or lack of data.

function searchHeaders($keyword, $arrayToSearch){
    foreach($arrayToSearch as $key => $arrayItem){
        if( stristr( $arrayItem, $keyword ) ){
            return $arrayItem;
        }
    }
}

$mg_message = searchHeaders('MG-Message', $headers);
echo '<p>' .$mg_message . '</p>';

##################
# OUTPUT HEADERS #
##################

//Convert Headers to Array and cleanup

$headers = explode("\r\n", $headers);
$headers = array_filter($headers);

//Output Headers

$allHeaders = '';
foreach ($headers as $value) {
    $allHeaders .= '<li>' . $value . '</li>';
}
$allHeaders = '<ul>' . $allHeaders . '</ul>';

echo $allHeaders;

###############
# OUTPUT BODY #
###############

//JSON decode body
$response = json_decode($body, true);

  if($http_code == 200){
      echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</pre>";
  }elseif($http_code == 204){
      echo 'No results for request';
      echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</pre>";
  }else{
      exit();
  }


