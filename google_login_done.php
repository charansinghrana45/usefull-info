<?php
//defining
require_once __DIR__ . '/vendor/autoload.php';
const CLIENT_ID = '';
const CLIENT_SECRET = '';
const REDIRECT_URI = 'http://localhost/rana/done.php';

session_start();

//initialization
$client = new Google_Client();
$client->setClientId(CLIENT_ID);
$client->setClientSecret(CLIENT_SECRET);
$client->setRedirectUri(REDIRECT_URI);
$client->setScopes('email');

$plus = new Google_Service_Plus($client);

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
	
	$me = $plus->people->get('me');
	
	echo $me['emails'][0]['value'];
	echo "<br>";
	echo $me['image']['url'];
}
else
{
	echo "<a href='".$authURL = $client->createAuthUrl()."'>googlelogin</a>";
}

?>