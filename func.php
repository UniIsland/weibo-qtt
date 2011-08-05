<?php

/** gi */
define('DB_VARS', 'var');

function get_var($k) {
	if(($mysqli = $GLOBALS['mysqli']) == null) die('$mysqli undefined');
	if(($var = $GLOBALS['vars'][$k]) == null) die("$k undefined");
	$query = "select `{$var['type']}` from `".DB_VARS."`
		where `id` = {$var['id']}";
	if($result = $mysqli->query($query)) {
		if($result->num_rows === 0)
			return null;
		$row = $result->fetch_assoc();
		$result->free();
		return $row[$var['type']];
	}
	die("$query<br />Error querying DB($mysqli->errno): $mysqli->error .");
}
function set_var($k, $v) {
	if(get_var($k) == $v)
		return true;
	if(($mysqli = $GLOBALS['mysqli']) == null) die('$mysqli undefined');
	if(($var = $GLOBALS['vars'][$k]) == null) die("$k undefined");
	if(is_string($v)) $v = "'".$v."'";
	$query = "update `".DB_VARS."` set `{$var['type']}` = $v
		where `id` = {$var['id']}";
	if($mysqli->query($query) !== TRUE)
		die("$query<br />Error updating DB($mysqli->errno): $mysqli->error .");
	if($mysqli->affected_rows == 0) {
		$query = "insert into `".DB_VARS."` (`{$var['type']}`)
			values ($v)";
		if($mysqli->query($query) !== TRUE)
			die("$query<br />Error updating DB($mysqli->errno): $mysqli->error .");
		return $mysqli->insert_id;
	}
	return true;
}
function test_char_cjk($char) {
	return $char >= "一" && $char <= "鿋" ? true : false;
}

?>
