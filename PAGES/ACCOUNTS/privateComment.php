<?php

header("Content-type: application/json");

include '../../PHP/database_connection.php';
include '../../PHP/functions.php';
require_once '../../PHP/htmlpurifier-4.6.0/library/HTMLPurifier.auto.php';

sec_session_start();

//print error in json way
function mbcError($s="errore sconosciuto"){
	$e = array("error" => $s);
	exit(json_encode($e));
}

if (!is_logged($mysqli)) mbcError("Oooops! Non sei più loggato...");


//controllo variabili: id post, commento
if (empty($_GET['id']) || empty($_GET['c'])) mbcError("Dati insufficienti...");

//control id post exist:
if ($stmt = $mysqli->prepare("SELECT id FROM gallery WHERE id=?")){
	$stmt->bind_param("i",$_GET['id']);
	$stmt->execute();
	if (!$stmt->fetch()) mbcError("Questo post non esiste più");
	$stmt->close();
}


//ridimensiono secondo numero caratteri disponibili nel database
$_GET['c'] = substr($_GET['c'],0,1950);//max 2000 char

$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$content = $purifier->purify($_GET['c']);

if ($stmt = $mysqli->prepare("INSERT INTO gallery_comment (autore,post,data,content) VALUES (?,?,?,?)")){
	$stmt->bind_param("iiis",$_SESSION['user_id'],$_GET['id'],time(),utf8_decode($content));
	if ($stmt->execute()){
		$json = array(
		'id' => $stmt->insert_id,
		'n' => $_SESSION['username'],
		'i' => $_SESSION['user_id'],
		'd' => date("H:i"),
		'c' => $content
		);
		exit(json_encode($json));
	}
	else mbcError("impossibile inserire nuovi commenti");
}
else mbcError("impossibile inserire nuovi commenti msq");

?>
