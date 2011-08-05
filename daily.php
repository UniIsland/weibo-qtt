<?php

require_once('./config.php');

define('TABLE2', 'count_jr_2');
define('TABLE3', 'count_jr_3');
define('TABLE4', 'count_jr_4');

$termUri = 'term.php';
$selfUri = 'daily.php';

if(($date = date_create($_GET['date'])) === false)
	$date = date_create();
$today = $date->format('Y-m-d');
if(($n = intval($_GET['n'])) < 1 || $n > 100)
	$n = 20;

function read($table) {
	global $mysqli, $n, $today;
	$query = "select `term` from `$table` where `date` = '$today'
		order by `count` desc limit $n";
	if($result = $mysqli->query($query)) {
		while ($row = $result->fetch_row())
			$rows[] = $row[0];
		$result->free();
		return $rows;
	} else
		die("$query<br />Error querying DB($mysqli->errno): $mysqli->error .");
}

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error)
	die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
if (!$mysqli->set_charset("utf8"))
	echo "Error loading character set utf8: $mysqli->error<br />";

$c2 = read(TABLE2);
if($c2[0]) {
	$c3 = read(TABLE3);
	$c4 = read(TABLE4);
}

$mysqli->close();

echo "<h3>$today 出現最多的字串.</h3>";
$yesterday = $date->modify('-1 day')->format('Y-m-d');
$tomorrow = $date->modify('+2 day')->format('Y-m-d');
echo "<p><a href='$selfUri?date=$yesterday&n=$n'>$yesterday</a>
	| <a href='$selfUri?date=$tomorrow&n=$n'>$tomorrow</a></p>
	<p><a href='$selfUri?date=$today&n=".($n+10)."'>顯示更多(+)</a>
	| <a href='$selfUri?date=$today&n=". ($n-10)."'>更少(-)</a></p>";
if($c2[0]) {
	echo "<table border='1'>";
	for ($i = 0; $i < $n; $i++)
		echo "<tr><td>$i</td>
		<td><a href='$termUri?t=$c2[$i]'>$c2[$i]</a></td>
		<td><a href='$termUri?t=$c3[$i]'>$c3[$i]</a></td>
		<td><a href='$termUri?t=$c4[$i]'>$c4[$i]</a></td>
		</tr>";
	echo "</table>";
} else
	echo "<p>No enough data.</p>";

?>
