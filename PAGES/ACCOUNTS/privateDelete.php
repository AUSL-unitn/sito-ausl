<?php
include '../../PHP/database_connection.php';
include '../../PHP/functions.php';

sec_session_start();

if (!is_logged($mysqli)) exit("Oooops! Non sei più loggato...");


//controllo variabili: id post, commento
if (empty($_GET['id'])) exit("Dati insufficienti...");

//control id post exist:
$id = 0;
if ($stmt = $mysqli->prepare("SELECT id,owner FROM gallery WHERE id=?")){
	$stmt->bind_param("i",$_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($gid,$gow);
	if ($stmt->fetch()){
		if ($gow != $_SESSION['user_id']) exit("Sei proprio un minchione...");
		$id = $gid;
	}
	else exit("Questo post non esiste più");
	$stmt->close();
}


if (!$mysqli->query("DELETE FROM gallery WHERE id=".$id." LIMIT 1")) exit("impossibile cancellare il post msq");


if ($mysqli->query("DELETE FROM gallery_comment WHERE post=".$id)) exit("ok");
else exit("impossibile cancellare i commenti del post msq");

?>
