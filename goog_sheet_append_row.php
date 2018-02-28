<?php
//session_start();
//session_destroy();
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

define('SCOPES', implode(' ', array(
  Google_Service_Sheets::SPREADSHEETS)
));

$client->setScopes(SCOPES);
//$client->setAccessType('offline');

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
		
	//inserting data
	$ary_values = [date('d/m/Y H:i:s'),'rana2','rana3','rana4','rana5','rana6','rana4','rana5','rana6'];
	
// Build the CellData array
	$values = array();
	foreach( $ary_values AS $d ) {
		$cellData = new Google_Service_Sheets_CellData();
		$value = new Google_Service_Sheets_ExtendedValue();
		$value->setStringValue($d);
		$cellData->setUserEnteredValue($value);
		$values[] = $cellData;
	}

	// Build the RowData
	$rowData = new Google_Service_Sheets_RowData();
	$rowData->setValues($values);

	// Prepare the request
	$append_request = new Google_Service_Sheets_AppendCellsRequest();
	$append_request->setSheetId(0);
	$append_request->setRows($rowData);
	$append_request->setFields('userEnteredValue');

	// Set the request
	$request = new Google_Service_Sheets_Request();
	$request->setAppendCells($append_request);

	// Add the request to the requests array
	$requests = array();
	$requests[] = $request;

	// Prepare the update
	$batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(array(
		'requests' => $requests
	));
		
	try {
		// Execute the request
		$response = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
			
		if( $response->valid() ) {
		 echo "Success, the row has been added";
			return true;
		}
		else
		{
			echo "no row added";
			exit;
		}
	} catch (Exception $e) {
		// Something went wrong
		error_log($e->getMessage());
	}
		
	
}
else
{
	echo "<a href='".$authURL = $client->createAuthUrl()."'>googlelogin</a>";
}

?>