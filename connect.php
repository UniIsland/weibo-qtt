<?php
require_once('./config.php');

define('DB_TABLE', 'oauth');
define('CALLBACK_URL', URL_BASE . 'connect.php');

function dbAddEntry($tuid, $token, $sec) {
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if ($mysqli->connect_error) {
	    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
//	$charset = $mysqli->character_set_name();
//	echo "<p>current charset is: $charset </p>";
	$query = "insert into `".DB_TABLE."` (tuid,token,secret) values ($tuid,'$token','$sec')";
//	echo "<p>$query</p>";
	if($mysqli->query($query) !== TRUE && $mysqli->errno != 1062 )
		die("Error updating database($mysqli->errno): $mysqli->error .");
}

session_name('QTTSESSION');
session_start();

if(!isset($_GET['oauth_token']) && $_SESSION['state']==1) $_SESSION['state'] = 0;

try {
	$oauth = new OAuth($appkey, $appsec);
	if(DEBUG_MODE) $oauth->enableDebug();
	if(!isset($_GET['oauth_token']) && !$_SESSION['state']) {
		$request_token = $oauth->getRequestToken($oauth_req);
		$_SESSION['state'] = 1;
		$_SESSION['secret'] = $request_token['oauth_token_secret'];
		header('Location: ' . $oauth_auth . '?oauth_token=' . $request_token['oauth_token'] . '&oauth_callback=' . rawurlencode(CALLBACK_URL) . '&display=page');
		exit;
	} elseif($_SESSION['state']==1) {
		$oauth->setToken($_GET['oauth_token'],$_SESSION['secret']);
		$access_token = $oauth->getAccessToken($oauth_acc);
		$_SESSION['state'] = 2;
		$_SESSION['token'] = $access_token['oauth_token'];
		$_SESSION['secret'] = $access_token['oauth_token_secret'];
		$_SESSION['t_uid'] = $access_token['user_id'];
	}
	dbAddEntry($_SESSION['t_uid'], $_SESSION['token'], $_SESSION['secret']);
	$oauth->setToken($_SESSION['token'],$_SESSION['secret']);
	$oauth->fetch(API_BASE . 'account/verify_credentials.json');
	$json = json_decode($oauth->getLastResponse(), true);
//	print_r($json);
	echo "<p>Hi, <strong>".$json['name']."</strong>!<br />
		You've successfully connected your account.<br />
		We really appreciate your help.</p>
		<p><a href='./'>Go Back</a>.</p>";
} catch(OAuthException $E) {
	echo '<p>An error occurred, please come back later.<p>';
//	print_r($E);
}
?>

