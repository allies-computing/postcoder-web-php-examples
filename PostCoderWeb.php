<?php

/**
* @file
* PostCoderWeb.php
*
* Postcoder Web Soap v3
*
* @see http://www.alliescomputing.com
*
* Allies Computing ®2014
*/


$searchKey = 'ENTER_YOUR_KEY_HERE';    // replace with your search key



if(isset($_GET['method'])){ 	$method 	= $_GET['method']; }
if(isset($_GET['searchterm'])){ $searchterm = rawurlencode($_GET['searchterm']); }
if(isset($_GET['identifier'])){ $identifier = rawurlencode($_GET['identifier']); }
if(isset($_GET['lines'])){		$lines 		= $_GET['lines']; }
if(isset($_GET['include'])){ 	$include 	= $_GET['include']; }
if(isset($_GET['exclude'])){ 	$exclude 	= $_GET['exclude']; }


// build the URL:

$RestURL = 'http://ws.postcoder.com/pcw/' . $searchKey .'/' . $method . '/UK/' . $searchterm . '?identifier=' . $identifier . '&lines=' . $lines;

if (isset($include)){ 
	$RestURL .= '&include=' . $include; 
}

if (isset($exclude)){ 
	$RestURL .= '&exclude=' . $exclude; 
}



// METHOD 1 - cURL
// The cURL extension must be loaded in your PHP configuration file, php.ini. To see if cURL is installed and active, call the phpinfo function and browse the output.
// If cURL is not enabled and you are unable to change that, comment out the curl method and try the file_get_contents method below.


$session = curl_init($RestURL); 
// Tell cURL to return the request data
curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
// Include the header in the output
curl_setopt($session, CURLOPT_HEADER, 1);
// Set the HTTP request headers, if we use application/json, then json will be returned from the service.
$headers = array(
    'Content-Type: application/json'
);
curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

// Execute cURL on the session handle
$response = curl_exec($session);

// get the header and body of the response
$header_size = curl_getinfo($session, CURLINFO_HEADER_SIZE);
$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);

// Close the cURL session
curl_close($session);

// send the response back to the client with the same header received from the service.
header(strtok($header, "\n"));
echo $body;
exit();




// METHOD 2 - file_get_contents
// To use file_get_contents your PHP installation must have fopen wrappers enabled.

// Set the HTTP request headers, if we use application/json, then json will be returned from the service.
$headers = array(
    'http' => array(
        'method' => "GET",
        'header' => "Content-Type: application/json"
  )
);

// Creates a stream context
$context = stream_context_create($headers);

// Open the URL with the HTTP headers (fopen wrappers must be enabled)
$response = file_get_contents($RestURL, false, $context);

// send the response back to the client.
echo $response;
exit();


?>