<?php

include_once('./config.php');

$search_url = 'statuses/search.json';
$friends_create_url = 'friendships/create.json';
$isfriends_url = 'friendships/exists.json';
$oauth = new OAuth($appkey, $appsec);
$oauth->setToken($oauth_token, $oauth_sec);

//$file = fopen("newkeywords.txt", "r") or exit("Unable to open the file!"); 
$param1 = array('count' => 200);
$param2 = array();
$param3 = array('user_a'=> '2056889875');
$error = 0;
$num = 0;

/*
//search test
$param1 = array('q' => '股票', 'count' => 200);
$oauth->fetch(API_BASE.$search_url, $param1);
$json = json_decode($oauth->getLastResponse(), true);

echo '<pre>';
print_r(json_decode($oauth->getLastResponse(), true));
echo '</pre>';

//echo '<pre>';
//print_r($json[8]);
//echo '</pre>';
*/

//8972847097
//8972846379


//isfriend test
/*
$param3['user_b'] = '8972846379';

$oauth->fetch(API_BASE.$isfriends_url, $param3);

echo '<pre>';
print_r(json_decode($oauth->getLastResponse(), true));
echo '</pre>';
*/



//addfriends test
/*
$param2['user_id'] = '8972847097';
$oauth->fetch(API_BASE.$friends_create_url, $param2);

echo '<pre>';
print_r(json_decode($oauth->getLastResponse(), true));
echo '</pre>';
*/



//main
/*
while(!feof($file))
    {
	$param1['q'] = fgets($file);
	echo "read".$param1['q']."from file".'</br>';
	$oauth->fetch(API_BASE.$search_url, $param1);
	$json = json_decode($oauth->getLastResponse(), true);
	if(empty($json)){
	    echo "no one says".$param1['q'].'</br>';
		continue;
		}
		echo '1';
	foreach($json as $value)
	    {
		$user_id = $value['user']['id'];
		echo 'look'.$value['user']['id'].'</br>';
		if($value['user']['followers_count']>50) 
		    {
			echo 'find'.$value['user']['id'];
			$param3['user_b'] = $value['user']['id'];
			$oauth->fetch(API_BASE.$isfriends_url, $param3);
			$json2 = json_decode($oauth->getLastResponse(), true);
			if(!$json2['friends'])
			    $param2['user_id'] = $value['user']['id'];
			    $oauth->fetch(API_BASE.$friends_create_url, $param2);
				
			
			}
		}
	if($error>10 && $num>100) 
	    die("End with add user error, the number of new friends is".$num);
	}

fclose($file);
		
echo("End with keywords exhausted, the number of new friends is".$num);
*/

?>