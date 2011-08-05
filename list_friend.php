<?php
include_once('./config.php');

$uri = 'statuses/friends.json';
$param = array('count' => 200);

$oauth = new OAuth($appkey, $appsec);
$oauth->setToken($oauth_token, $oauth_sec);
$oauth->fetch(API_BASE.$uri, $param);

//echo '<pre>';
//print_r(json_decode($oauth->getLastResponse(), true));
//echo '</pre>';

$r = json_decode($oauth->getLastResponse(), true);

foreach($r as $u)
	echo $u['name'] . '<br />';

?>

