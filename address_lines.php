<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>PostCoder Web V3 Example</title>
    <link rel="stylesheet" type="text/css" href="css/websoap.css" media="screen" />
  </head>
  <body>

    <div id="wsExample">
      <h1>PostCoder Web V3</h1>

        <div>
          <p>Example with request routed via a server-side PHP script, making it possible to secure your account to the IP address 
            of your web server - you can do this using the IP Addresses section in the <a href="http://www.postcoder.com/admin">admin area</a></p>
        </div>

        <div class="container">
          <form method="post" action="">

<?php

/*
  Note: This script has been developed for php version >= 5.2.0
  cURL and json_decode/json_encode functions are used.
*/

  $searchKey = 'ENTER_YOUR_KEY_HERE';    // replace with your search key

  if (isset($_POST['address'])) {
    // an address has been selected so display it
    displayResult(json_decode($_POST['address']));
  }
  else if (isset($_POST['searchField'])) {
    // we have a string to use for an address search
     
    $searchterm     = rawurlencode($_POST['searchField']);        
    $identifier     = rawurlencode('v3 PHP Example');  // something to identify this lookup script in your PostCoder Web stats page (optional)
    $addresslines   = 3;                            // number of address lines required in your form / database
    $exclude        = 'organisation';               // if organisation has it's own field in your form / database, exclude it from the address lines
    //$include        = 'posttown,postcode';          // you could include posttown, county, postcode in the address lines if they don't have thier own fields.

    // build the URL, using the 'address' search method:
    $RestURL = 'http://ws.postcoder.com/pcw/' . $searchKey .'/address/UK/' . $searchterm . '?identifier=' . $identifier . '&lines=' . $addresslines;

    if (isset($include)){ 
      $RestURL .= '&include=' . $include; 
    }

    if (isset($exclude)){ 
      $RestURL .= '&exclude=' . $exclude; 
    }


    // use cURL to send the request and get the response

    $session = curl_init($RestURL); 
    // Tell cURL to return the request data
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true); 
    // Set the HTTP request headers, if we use application/json, then json will be returned from the PostCoder Web service, the default is XML.
    $headers = array(
        'Content-Type: application/json'
    );
    curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
    // Execute cURL on the session handle
    $response = curl_exec($session);
    // Close the cURL session
    curl_close($session);

    // decode the response
    $addresses = json_decode($response);

    if (count($addresses) > 1){
      // more than one result, so display a selection
      displaySelection($addresses);

    }else if(count($addresses) == 1){

      displayResult($addresses[0]); 

    }
    else{

      echo '<div id="postcodelookup">';
      echo '<label id="searchText">Enter Postcode</label>';
      echo '<input id="searchField" name="searchField" class="searchField" type="text" />';
      echo '<input id="searchButton" class="searchButton" type="submit" value="Find Address" />';
      echo '</div>';
      echo '<p>No results, please try again.</p>';

    }

  }
  else {

      echo '<div id="postcodelookup">';
      echo '<label id="searchText">Enter Postcode</label>';
      echo '<input id="searchField" name="searchField" class="searchField" type="text" />';
      echo '<input id="searchButton" class="searchButton" type="submit" value="Find Address" />';
      echo '</div>';
}

?> 

        </form>
      </div>

</body>
</html>




<?php


  function displaySelection($addresses){

    echo '<label>Select an address</label>';
    echo '<select id="address" name="address">';

    foreach ($addresses as $address){

      $summaryline = $address->summaryline;
      unset($address->summaryline);

      // put the full result (minus the unset summaryline) in the option value so we can use it after selection
      echo "<option value='".json_encode($address)."'>";
      echo $summaryline;
      echo '</option>';

    }

    echo '</select>';

    echo '<input id="searchButton" class="searchButton" type="submit" value="Select Address" />';

  }



  function displayResult($address){
    // single address found from a search or selction.

    echo '<fieldset>';
    echo '<label>Organisation</label>';
    echo '<input id="OrganisationInputField" name="OrganisationInputField" type="text" value="' . $address->organisation . '" >';
    echo '<label>Address</label>';
    echo '<input id="AddressLine1" name="AddressLine1" type="text" value="' . $address->addressline1 . '">';
    echo '<input id="AddressLine2" name="AddressLine2" type="text" value="' . $address->addressline2 . '">';
    echo '<input id="AddressLine3" name="AddressLine3" type="text" value="' . $address->addressline3 . '">';
    echo '<label>Town</label>';
    echo '<input id="TownInputField" name="TownInputField" type="text" value="' . $address->posttown . '">';
    echo '<label>County</label>';
    echo '<input id="CountyInputField" name="CountyInputField" type="text" value="' . $address->county . '">';
    echo '<label>Postcode</label>';
    echo '<input id="PostcodeInputField" name="PostcodeInputField" type="text" value="' . $address->postcode . '">';
    echo '</fieldset>';

  }


?>