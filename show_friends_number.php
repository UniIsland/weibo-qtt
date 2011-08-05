<?php

include_once('./config.php');

$oauth = new OAuth($appkey, $appsec);
$oauth->setToken($oauth_token, $oauth_sec);
$param = array('user_id' => '2056889875');

$oauth->fetch(API_BASE.'users/show.json', $param);

//$json = json_decode($oauth->getLastResponse(), true);

//print $json[12];

echo '<pre>';
print_r(json_decode($oauth->getLastResponse(), true));
echo '</pre>';

//print_r(array_values($json));

?>