<?php
include 'database_connection.php';

require_once("PHPMailer_5.2.4/class.phpmailer.php");
require_once("PHPMailer_5.2.4/extras/class.html2text.inc");

if (empty($_GET['e'])) exit("Nessun dato inserito...");


function sendMessage($name,$newpassword){

	//PREPARAZIONE MESSAGGIO
	$HTML = '<html><body style="font:12px \'Open Sans\',Helvetica,Arial">'.
		'<p>Caro utente "'.$name.'"</p>'.
		'<p>Hai richiesto la generazione di una nuova password per il sito <a href="http://www.ausl.altervista.org/logs.php">AUSL - Arrampicata Universitaria Sociale Libera (Trento)</a></p>'.
		'<p>La nuova password è: <b>'.$newpassword.'</b></p>'.
		"<p>Ti ricordiamo che per l'accesso il nome utente è il tuo indirizzo email.</p>".
		"<p>Se non hai effettuato tu la richiesta, informaci al più presto di eventuali abusi: provvederemo a modificare la modalità di recupero password entro breve.</p>".
		'<p>Buona giornata, a presto.</p>'.
		'</body></html>';


	//PRAPARAZIONE MAIL
	date_default_timezone_set('Europe/Rome');

	$mail = new PHPMailer;

	//ini
	$mail->IsMail();

	//from
	$mail->From = 'ausl@altervista.org';
	$mail->FromName = 'AUSL Climbing TN';
	$mail->AddReplyTo('ausl@altervista.org', 'AUSL Climbing TN');

	//to
	$mail->AddAddress($_GET['e'], $_GET['e']);

	//message
	$mail->WordWrap = 50;
	$mail->IsHTML(true); // Set email format to HTML
	$mail->CharSet = 'UTF-8';

	$mail->Subject = "Modifica dati dell'account";
	$mail->Body    = $HTML;
		$h2t = new html2text($HTML);
	$mail->AltBody = $h2t->get_text();

	if(!$mail->Send()) exit("Purtroppo non siamo riusciti a spedirti la mail.<br>Riprova fa qualche minuto.");
	else exit("La mail contenente i nuovi dati è stata spedita.<br>Controlla eventualmente la cartella spam se non ricevi l'email entro breve.");
}



//controllo esistenza account
$name='';
if($stmt = $mysqli->prepare("SELECT nome FROM users WHERE email = ?")){
	$stmt->bind_param("s",$_GET['e']);
	$stmt->execute();
	$stmt->bind_result($n);
	if ($stmt->fetch()) $name = htmlentities(utf8_encode($n),NULL,'UTF-8');
	else exit("Ooops! Non ci risulta questa mail nel database!");
	$stmt->close();
}


//generazione nuova password
$newpassword = substr(md5(rand()), 0, 8);
$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
$password = hash('sha512', hash('sha512', $newpassword).$random_salt);


//edit account
if($stmt = $mysqli->prepare("UPDATE users SET password=?,salt=? WHERE email=?")){
	$stmt->bind_param('sss',$password,$random_salt,$_GET['e']);
	if ($stmt->execute()) sendMessage($name,$newpassword);
	$stmt->close();
}

?>
