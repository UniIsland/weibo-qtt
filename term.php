<?php

require_once('./config.php');
require_once('./func.php');

$termUri = 'term.php';
$term = $_GET['t'];
$termLen = mb_strlen($term);
$table = array(
	2 => 'count_jr_2',
	3 => 'count_jr_3',
	4 => 'count_jr_4', );
$range = 90;

function fetch ($term, $table, $since) {
	global $mysqli;
	$date = $since === 0 ? '' : "&& `date` > '"
		. date_create()->modify("-$since day")->format('Y-m-d') . "'";
	$query = "select `date`, `count` from `$table`
		where `term` = '$term' $date order by `date` asc";
	if($result= $mysqli->query($query)) {
		$rows = array();
		while($row = $result->fetch_assoc())
			$rows[$row['date']] = $row['count'];
		$result->data_seek(0);
		$row = $result->fetch_assoc();
		$result->free();
		$feed = array();
		$today = date_create('tomorrow');
		for($date = date_create($row['date']); $date < $today; $date->modify('+1 day'))
			$feed[$date->format('Y-m-d')] = $rows[$date->format('Y-m-d')] ? : 0;
//		return $rows;
		return $feed;
	} else
		die("$query<br />Error querying DB($mysqli->errno): $mysqli->error.");
}

if ( $termLen >= 2 && $termLen <= 4) {
	for($i=0; $i < $termLen; $i++) {
		$c = mb_substr($_GET['t'],$i ,1);
		if(test_char_cjk($c) === false)
			die("invalid input");
	}
} else
	die("invalid input");

if($_GET['o'] == 'png') {
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if ($mysqli->connect_error)
		die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	if (!$mysqli->set_charset("utf8"))
		echo "Error loading character set utf8: $mysqli->error<br />";

	$rows = fetch($term, $table[$termLen], $range);
	$mysqli->close();

	$today = date_create('tomorrow');
	$chd = 't:';
	for ( $date = date_create('tomorrow')->modify("-$range day"); $date < $today; $date->modify('+1 day'))
		$chd .= ($rows[$date->format('Y-m-d')] ? : 0) . ',';
	$chd = substr($chd, 0, -1);
	$count_max = max($rows);
	$date->modify("-$range day")->modify('-1 month');
	$chxl = '1:';
	do {
		$month = $date->modify('+1 month')->format('M');
		$chxl .= '|' . $month;
	} while ($month !== date('M'));
	$chart = array(
		'cht' => 'bvs',
		'chbh' => 'r,2',
		'chs' => '800x200',
		'chd' => $chd,
		'chds' => "0,$count_max",
		'chxt' => 'y,x',
		'chxr' => "0,0,$count_max",
		'chxl' => $chxl,
		'chxp' => '1,0',
		'chg' => '0,25',
		'chm' => 'D,0000FF66,0,0,2',
	);
	$context = stream_context_create(array(
		'http' => array(
			'method' => 'POST',
			'content' => http_build_query($chart))));
	header('content-type: image/png');
	fpassthru(fopen(CHART_API, 'r', false, $context));
} elseif ($_GET['o'] == 'csv') {
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if ($mysqli->connect_error)
		die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	if (!$mysqli->set_charset("utf8"))
		echo "Error loading character set utf8: $mysqli->error<br />";

	$rows = fetch($term, $table[$termLen], 0);
	$mysqli->close();

	header('Content-Type: text/csv; charset: utf-8');
	header("Content-Disposition: attachment; filename=\"$term.csv\"");
	echo "Date,$term\n";
	foreach ($rows as $key => $value)
		echo "$key,$value\n";
} else {
	echo <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>$term</title>
</head><body>
<h3>字串頻率統計: <i>$term</i></h3>
<img src="$termUri?t=$term&o=png" />
<p><a href="$termUri?t=$term&o=csv">點擊這裏</a>下載完整數據.</p>
</body></html>
EOD;
	die;

}

?>

