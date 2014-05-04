<?php
header("Content-type: application/json");

include '../../PHP/database_connection.php';
include '../../PHP/functions.php';

sec_session_start();


//print error in json way
function mbcError($s="errore sconosciuto"){
	$e = array("error" => $s);
	exit(json_encode($e));
}


//controllo login
if (!is_logged($mysqli)) mbcError("Ooops! Non sei più loggato...");


//get preference:
$default = '000';
if (isset($_GET['mark'])) $default[0] = '1';
if (isset($_GET['list'])) $default[1] = '1';
if (isset($_GET['news'])) $default[2] = '1';


if ($stmt = $mysqli->query("UPDATE users SET preferences='$default' WHERE id=".$_SESSION['user_id']." LIMIT 1")){
	//update session
	$_SESSION['prefer'] = $default;
	exit('{"ok":"Preferenze salvate."}');
}
else mbcError("Ooops! Non sono riuscito a salvare le preferenze!!");

?>
