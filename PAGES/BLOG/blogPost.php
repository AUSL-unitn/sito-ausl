<?php

header("Content-type: application/json");

require_once '../../PHP/database_connection.php';
require_once '../../PHP/functions.php';
require_once '../../PHP/htmlpurifier-4.6.0/library/HTMLPurifier.auto.php';

sec_session_start();

if (!is_logged($mysqli)) mbcError("Oooops! Non sei più loggato...");

//print error in json way
function mbcError($s="errore sconosciuto"){
	$e = array("error" => $s);
	exit(json_encode($e));
}


//controllo variabili: titolo, data, contenuto, tipo
if (empty($_POST['title']) || empty($_POST['data']) || empty($_POST['expire']) || empty($_POST['b']) || empty($_POST['desc']) || !isset($_POST['type'])) mbcError("Dati insufficienti...");
if ($_POST['b']>4 || $_POST['b']<1) mbcError("Dati insufficienti...");

//control expiration date
$expire = explode('/',$_POST['expire']);
if (count($expire)!==3) mbcError("Errore nella data.");
//d - m - YYYY
if (!is_numeric($expire[0]) || !is_numeric($expire[1]) || !is_numeric($expire[2])) mbcError("Errore nel formato data.");
$expire = strtotime($expire[2].'-'.$expire[1].'-'.$expire[0]);
if ($expire === false) mbcError("Questa roba non è una data...");
if ($expire < time()) mbcError("Non puoi far scadere l'evento in così poco tempo!!!");
$expire += 86400; // la data inizia alle 00:00, voglio che venga mostrata fino alla fine del giorno


//ridimensiono secondo numero caratteri disponibili nel database
$_POST['title'] = substr($_POST['title'],0,500);//max 500 char
$_POST['data'] = substr($_POST['data'],0,300);//max 300 char
$_POST['desc'] = substr($_POST['desc'],0,4950);//max 5000 char

//purify HTML
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$content = $purifier->purify($_POST['desc']);


if ($stmt = $mysqli->prepare("INSERT INTO blog (blog,tipo,data,expire,dataevento,autore,titolo,content) VALUES (?,?,?,?,?,?,?,?)")){
	$stmt->bind_param("iiiisiss",$_POST['b'],$_POST['type'],time(),$expire,utf8_decode($_POST['data']),$_SESSION['user_id'],utf8_decode($_POST['title']),utf8_decode($content));
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
	else{
		mbcError("Impossibile inserire nuovi post");
	}
}
else mbcError($s="Ooops! Non sono risucito a salvare il post...")

?>
