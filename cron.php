<?php
try{
require_once('./config.php');

$uri = 'statuses/friends_timeline.json';
$param = array('count' => 200,);
$pages = intval($_GET['n']);
//if($pages === 0) die('n=0');
if($pages === 0) $pages = 50;

/**
 * create table tweets_jr ( id int primary key auto_increment not null,
 * text_id bigint(11) unique not null, date date, time time, text varchar(512),
 * tuid bigint(11) not null, rt_id biging(11), rt_text varchar(512))
 */
$db_table = 'tweets_jr';

$oauth = new OAuth($appkey, $appsec);
$oauth->setToken($oauth_token, $oauth_sec);

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error)
	die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
if (!$mysqli->set_charset("utf8"))
	echo "Error loading character set utf8: $mysqli->error<br />";

if($result = $mysqli->query("select count(id) from `tweets_jr`")) {
	list($num_rows) = $result->fetch_row();
	$result->free();
} else
	die("Error querying database($mysqli->errno): $mysqli->error .");

//$query = "SELECT max(`text_id`) FROM `tweets_jr`";
$query = "SELECT `text_id` FROM `tweets_jr` ORDER BY `text_id` DESC LIMIT 1";
if($result = $mysqli->query($query)) {
	$row = $result->fetch_assoc();
	$param['since_id'] = $row['text_id'];
//	$param['max_id'] = intval($row['text_id']);
	$result->free();
} else
	die("Error querying database($mysqli->errno): $mysqli->error .");

for ($param['page'] = 1; $param['page'] <= $pages; $param['page']++) {
	$oauth->fetch(API_BASE.$uri, $param, OAUTH_HTTP_METHOD_GET);
	$json = json_decode($oauth->getLastResponse(), true);
	if(empty($json)) break;
	foreach ($json as $value) {
		$datetime = date_parse_from_format("D M d H:i:s T Y", $value['created_at']);
		$date = $datetime['year'].'-'.$datetime['month'].'-'.$datetime['day'];
		$time = $datetime['hour'].':'.$datetime['minute'].':'.$datetime['second'];
		if(isset($value['retweeted_status'])) {
			$rt_id = $value['retweeted_status']['id'];
			$rt_text = "'".$mysqli->real_escape_string($value['retweeted_status']['text'])."'";
		} else {
			$rt_id = 'null';
			$rt_text = 'null';
		}
		$query = "insert ignore $db_table (`text_id`,`date`,`time`,`text`,`tuid`,`rt_id`,`rt_text`) values
			({$value['id']},'$date', '$time', '{$mysqli->real_escape_string($value['text'])}',
			{$value['user']['id']}, $rt_id, $rt_text);";
//		echo "<p>$query</p>";
		if($mysqli->query($query) !== TRUE && $mysqli->errno != 1062 )
			die("Error updating database($mysqli->errno): $mysqli->error .");
//		echo "<p>rows: $mysqli->affected_rows";
	}
	echo "<p>page {$param['page']} contains ".count($json)." records. starting from id: {$param['since_id']}</p>\n";
	if(count($json) < 190) break;
}

if($result = $mysqli->query("select count(id) from `tweets_jr`")) {
	list($num_rows_f) = $result->fetch_row();
	$result->free();
} else
	die("Error querying database($mysqli->errno): $mysqli->error .");

$mysqli->close();
echo "finished fetching. last entry was posted on $date, $time. ($num_rows - $num_rows_f)\n";

} catch (Exception $E) {
	print_r($E);
	die();
}

?>
