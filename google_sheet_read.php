<?php
//session_start();
//session_destroy();
//defining
require_once __DIR__ . '/vendor/autoload.php';
const CLIENT_ID = '748294224449-s1aqkfmo4m02hs5hop4lpe6f857ssamu.apps.googleusercontent.com';
const CLIENT_SECRET = 'thsAnmIlVyFOK7v4BAg01_9P';
const REDIRECT_URI = 'http://localhost/rana/done.php';

session_start();

//initialization
$client = new Google_Client();
$client->setClientId(CLIENT_ID);
$client->setClientSecret(CLIENT_SECRET);
$client->setRedirectUri(REDIRECT_URI);

define('SCOPES', implode(' ', array(
  Google_Service_Sheets::SPREADSHEETS_READONLY)
));

$client->setScopes(SCOPES);
$client->setAccessType('offline');

//Actual Process
if(isset($_GET['code']))
{
	//verify received code
	$client->authenticate($_GET['code']);
	
	//get access token
	$_SESSION['access_token'] = $client->getAccessToken();
	
	header('location:'.filter_var('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'],FILTER_SANITIZE_URL));
}

if(isset($_SESSION['access_token']) && $_SESSION['access_token'])
{
	//set access token (access token will be used in every request)
	$client->setAccessToken($_SESSION['access_token']);
	
	//https://docs.google.com/spreadsheets/d/10HahFSPLzle4MyIcaIaPfBK-7w_Zajwd86nPc9lpTgI/edit#gid=0
	$service = new Google_Service_Sheets($client);

	$spreadsheetId = '10HahFSPLzle4MyIcaIaPfBK-7w_Zajwd86nPc9lpTgI';
	$range = 'A1:F2';
	$response = $service->spreadsheets_values->get($spreadsheetId, $range);
	
	echo "<pre>";
	print_r($response);
	$values = $response->getValues();

	if (count($values) == 0) {
	  print "No data found.\n";
	} else {
	  
	  foreach ($values as $row) {
		foreach($row as $cell)
		{
			echo $cell;
		}
		echo "<br>";
	  }
	}
}
else
{
	echo "<a href='".$authURL = $client->createAuthUrl()."'>googlelogin</a>";
}

?>