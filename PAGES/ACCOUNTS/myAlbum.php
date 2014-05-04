<?php
header("Content-type: application/json");

require_once '../../PHP/database_connection.php';
require_once '../../PHP/functions.php';
require_once '../../PHP/htmlpurifier-4.6.0/library/HTMLPurifier.auto.php';


sec_session_start();

if (!is_logged($mysqli)) exit('{"error":"Non sei più loggato"}');


//controllo esistenza variabili
if (!isset($_GET["private"],$_GET["t"],$_GET["c"])) exit('{"error":"Dati insufficienti"}');
if (empty($_GET["t"])) exit('{"error":"Manca il titolo (mona)"}');
if (empty($_GET["c"])) exit('{"error":"Devi inserire la descrizione (imbriago)"}');


//ridimensiono secondo numero caratteri disponibili nel database
$_GET['t'] = substr($_GET['t'],0,1000);//max 100 char

$dirty_html = substr($_GET['c'],0,4950);//max 5000 char
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$content = $purifier->purify($dirty_html);


//post privato o pubblico
$private = ($_GET["private"]?true:false);
$code = '';
if ($private) $code = substr(md5(rand()), 0, 8);

if ($stmt = $mysqli->prepare("INSERT INTO gallery (data,title,content,owner,private,code) VALUES (?,?,?,?,?,?)")){
	$stmt->bind_param("issiis",time(),utf8_decode($_GET["t"]),utf8_decode($content),$_SESSION['user_id'],$private,$code);
	if ($stmt->execute()) exit('{"id":'.$stmt->insert_id.',"user":'.$_SESSION['user_id'].($code!=='' ? ',"code":"'.$code.'"' : '').'}');
	else exit('{"error":"Errore"}');
}
else exit('{"error":"Errore query"}');

?>
