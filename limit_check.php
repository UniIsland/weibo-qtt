<?php
include_once('./config.php');

$uri = 'account/rate_limit_status.json';

$oauth = new OAuth($appkey, $appsec);
$oauth->setToken($oauth_token, $oauth_sec);
$oauth->fetch(API_BASE.$uri);

echo '<pre>';
print_r(json_decode($oauth->getLastResponse(), true));
echo '</pre>';

?>

