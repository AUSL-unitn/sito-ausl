<?php

header("Content-type: application/json");

include '../../PHP/database_connection.php';
include '../../PHP/functions.php';

sec_session_start();

if (!is_logged($mysqli)) mbcError("Oooops! Non sei più loggato...");

//print error in json way
function mbcError($s="errore sconosciuto"){
	$e = array("error" => $s);
	exit(json_encode($e));
}


//controllo variabili: id post = id commento
if (empty($_GET['p'])) mbcError("Dati insufficienti...");

$json = array();
$posters = array();


foreach ($_GET['p'] as $postId => $lastCommentId){
	if($stmt = $mysqli->prepare("SELECT id,autore,data,content FROM commenti WHERE post=? AND id>? ORDER BY id ASC")){//ASC perchè javascript impila uno sopra l'altro
		$stmt->bind_param("ii",$postId,$lastCommentId);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows==0){
			$stmt->close();
		}
		else{
			$stmt->bind_result($id,$a,$d,$c);
			while($stmt->fetch()){
				$json['comm'][$id] = array(
					"p" => $postId,
					"d" => date("d M - H:i",$d),
					"a" => $a,
					"c" => utf8_encode($c)
				);
				if (!isset($posters[$a])) $posters[$a] = $a;
			}
			$stmt->close();
		}
	}
	else mbcError("impossibile cercare nuovi post");
}

//search name of poster!!!

$posters = 'id='.implode(" OR id=",$posters);
if ($res = $mysqli->query("SELECT id,nome FROM users WHERE ".$posters)){
	while($r = $res->fetch_assoc()){
		$json['auth'][$r['id']] = htmlentities(utf8_encode($r['nome']),NULL,'UTF-8');
	}
	mysqli_free_result($res);
}

exit(json_encode($json));
?>
