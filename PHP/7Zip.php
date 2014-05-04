<?php
/***************************************
				REGISTRATION
***************************************/


header("Content-type: application/json");

include 'database_connection.php';
include 'functions.php';

//print error in json way
function mbcError($s="errore sconosciuto"){
	$e = array("error" => $s);
	exit(json_encode($e));
}

//print error in json way
function mbcSuccess($s="ok"){
	$e = array("ok" => $s);
	exit(json_encode($e));
}

//controllo esistenza variabili
if(empty($_POST['n']) || empty($_POST['p']) || empty($_POST['e'])) mbcError("Dati insufficienti...");
if ($_POST['p'] == 'cf83e1357eefb8bdf1542850d66d8007d620e4050b5715dc83f4a921d36ce9ce47d0d13c5d85f2b0ff8318d2877eec2f63b931bd47417a81a538327af927da3e') mbcError("Dati insufficienti...");

//controllo nome non duplicato
$username = utf8_decode(trim($_POST['n']));
if ($stmt = $mysqli->prepare("SELECT id FROM users WHERE nome=?")){
	$stmt->bind_param('s',$username);
	$stmt->execute();
	if ($stmt->fetch()) mbcError("Nome utente già registrato");
	$stmt->close();
}
else mbcError("Qualcosa è andato storto nel check nome...");

//controllo email vera e non duplicata
$email = utf8_decode(trim($_POST['e']));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) mbcError("Questa roba non è una mail");
if ($stmt = $mysqli->prepare("SELECT id FROM users WHERE email=?")){
	$stmt->bind_param('s',$email);
	$stmt->execute();
	if ($stmt->fetch()) mbcError("Email utente già registrata");
	$stmt->close();
}
else mbcError("Qualcosa è andato storto nel check email...");


//creazione password
$password = $_POST['p'];//already hashed by js
$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
$password = hash('sha512', $password.$random_salt);

$email_sha = hash('sha512', $email);


//*****************************************************************************
//registrazione account
if ($stmt = $mysqli->prepare("INSERT INTO users (data,lastLog,nome,email,password,salt,email_sha) VALUES(?,?,?,?,?,?,?)")){
	$stmt->bind_param('iisssss',
		time(),
		time(),
		$username,
		$email,
		$password,
		$random_salt,
		$email_sha
	);
	if ($stmt->execute()){
		mbcSuccess("Utente registrato. Ora puoi loggarti coi dati che hai appena inserito");
	}
	else mbcError("Qualcosa è andato storto, riprova fra un po' di minuti");
}
else mbcError("Qualcosa è andato storto nell'inserimento account...");
?>
