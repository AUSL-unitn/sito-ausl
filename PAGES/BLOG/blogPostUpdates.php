<?php
include '../../PHP/database_connection.php';
include '../../PHP/functions.php';


header("Content-type: application/json");

//print error in json way
function mbcError($s="errore sconosciuto"){
	$e = array("error" => $s);
	exit(json_encode($e));
}

//controllo variabili: id ultimo post noto (i), blog (b)
if (empty($_GET['i'])) mbcError("[a] Nessun nuovo post da mostrare");
if (empty($_GET['b'])) mbcError("[b] Nessun nuovo post da mostrare");
//if (empty($_GET['b']) || empty($_GET['i'])) mbcError("Dati insufficienti per la ricerca nuovi post");

$json = array();
$posters = array();

if($stmt = $mysqli->prepare("SELECT id,tipo,data,dataevento,autore,titolo,content FROM blog WHERE blog=? AND id>? AND expire>".time()." ORDER BY id ASC")){//ASC perchè javascript impila uno sopra l'altro
	$stmt->bind_param("ii",$_GET['b'],$_GET['i']);
	$stmt->execute();
	$stmt->store_result();
	if ($stmt->num_rows==0){
		$stmt->close();
		exit(json_encode($json));
	}
	$stmt->bind_result($id,$t,$d,$de,$a,$ti,$c);
	while($stmt->fetch()){
		$json['post'][$id] = array(
			"t" => $t,
			"d" => date("d/m/Y",$d),
			"de" => htmlentities(utf8_encode($de),NULL,'UTF-8'),
			"a" => $a,
			"ti" => htmlentities(utf8_encode($ti),NULL,'UTF-8'),
			"c" => utf8_encode($c),
		);
		if (!isset($posters[$a])) $posters[$a] = $a;
	}
	$stmt->close();
}
else mbcError("impossibile cercare nuovi post");

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
