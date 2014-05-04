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


//controllo variabili: pass vecchia e nuova
if (!isset($_POST['p1']) || !isset($_POST['p2'])) mbcError("Dati insufficienti...");
if (
	$_POST['p1'] == 'cf83e1357eefb8bdf1542850d66d8007d620e4050b5715dc83f4a921d36ce9ce47d0d13c5d85f2b0ff8318d2877eec2f63b931bd47417a81a538327af927da3e' ||
	$_POST['p2'] == 'cf83e1357eefb8bdf1542850d66d8007d620e4050b5715dc83f4a921d36ce9ce47d0d13c5d85f2b0ff8318d2877eec2f63b931bd47417a81a538327af927da3e')
	mbcError("Dati insufficienti...");


//controllo quella vecchia == database
if ($stmt = $mysqli->prepare("SELECT password,salt FROM users WHERE id = ? LIMIT 1")) {
	$stmt->bind_param('i', $_SESSION['user_id']);
	$stmt->execute();
	$stmt->store_result();
	if($stmt->num_rows == 1) {
		$stmt->bind_result($password,$salt);
		$stmt->fetch();
		$login_check = hash('sha512', hash('sha512', $_POST['p1'].$salt).$_SERVER['HTTP_USER_AGENT']);
		if($login_check !== $_SESSION['login_string']) mbcError("Vecchia password errata");
	}
	else mbcError("Sei sicuro che esisti?");
}
else mbcError("Ooops! Non sono riuscito a verificare la password!");


//aggiorno password
$password = $_POST['p2'];//already hashed by js
$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
$password = hash('sha512', $password.$random_salt);

if ($stmt = $mysqli->query("UPDATE users SET password='$password', salt='$random_salt' WHERE id=".$_SESSION['user_id']." LIMIT 1")){
	//update session
	$_SESSION['login_string'] = hash('sha512', $password.$_SERVER['HTTP_USER_AGENT']);
	exit('{"ok":"Password correttamente aggiornata! Oh, ricordatela però"}');
}
else mbcError("Ooops! Non sono riuscito a modificare la password!");

?>
