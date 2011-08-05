<?php
require_once('./config.php');
require_once('./func.php');

/*****/
define('TABLE0', 'tweets_jr');
define('TABLE2', 'count_jr_2');
define('TABLE3', 'count_jr_3');
define('TABLE4', 'count_jr_4');

$vars = array(
	'cur_id' => array( 'id' => 1, 'type' => 'int',),
);

/**
 * 2:	<1s
 * 20:	<1s
 * 100:	3.6s
 * 500:	17s
 */
$row_limit = 250;

function add($c, $date, $table) {
	global $mysqli;
	$query = "insert into `$table` (`term`, `date`, `count`)
		values ('$c', '$date', 1)
		on duplicate key update `count` = `count` + 1";
	if($mysqli->query($query) !== TRUE)
		die("$query<br />Error updating DB($mysqli->errno): $mysqli->error .");
}
//function add($c, $date, $table) {
//	global $mysqli;
//	$query = "update `$table` set `count` = `count` + 1
//		where `term` = '$c' && `date` = '$date'";
//	if($mysqli->query($query) !== TRUE)
//		die("$query<br />Error updating DB($mysqli->errno): $mysqli->error .");
//	if($mysqli->affected_rows == 0) {
//		$query = "insert into `$table` (`term`, `date`, `count`)
//			values ('$c', '$date', 1)";
//		if($mysqli->query($query) !== TRUE)
//			die("$query<br />Error updating DB($mysqli->errno): $mysqli->error .");
//	}
//}

function parse($text, $date) {
	global $cur_id;
	$c1 = $c2 = $c3 = $c4 = null;
	for($i=0; $i < mb_strlen($text); $i++) {
		$c4 = mb_substr($text, $i, 1);
		if(test_char_cjk($c4)) {
			if($c3 !== null) {
				if($c2 !== null) {
					if($c1 !== null)
						add($c1.$c2.$c3.$c4, $date, TABLE4);
					add($c2.$c3.$c4, $date, TABLE3);
					$c1 = $c2;
				} else
					$c1 = null;
				add($c3.$c4, $date, TABLE2);
				$c2 = $c3;
			} else
				$c2 = null;
			$c3 = $c4;
		} else
			$c3 = null;
	}
	$cur_id++;
}

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error)
	die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
if (!$mysqli->set_charset("utf8"))
	echo "Error loading character set utf8: $mysqli->error<br />";

$cur_id = get_var('cur_id');
echo "starting with entry: ".$cur_id."<br />\n";

$query = "select `date`, `text`, `rt_text` from `".TABLE0."` limit $cur_id, $row_limit";
if($result = $mysqli->query($query)) {
	while ($row = $result->fetch_assoc()) {
		parse($row['text'].' '.$row['rt_text'], $row['date']);
	}
	$result->free();
} else
	die("$query<br />Error querying DB($mysqli->errno): $mysqli->error .");

if(set_var('cur_id',$cur_id) !== true)
	echo 'updating $cur_id failed.<br />';

echo "finished. last entry: ".$cur_id."<br />\n";

$mysqli->close();

?>
