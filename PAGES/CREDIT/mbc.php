<?php
include '../../PHP/database_connection.php';
include '../../PHP/functions.php';

sec_session_start();

$imlogged = is_logged($mysqli);

//controllo variabili
if (!isset($_GET['w']) || !is_numeric($_GET['w'])) $_GET['w']=0;
if (!isset($_GET['b']) || !is_numeric($_GET['b'])) $_GET['b']=1;

?>
<!DOCTYPE HTML>

<html>


<head>
<title>A.U.S.L. | Credits</title>
<meta charset="UTF-8">
<meta name="author" content="Costacurta Nereo | MBC">
<meta name="viewport" content="width=device-width">
<meta name="description" content="Dedicata a tutti gli universitari amanti del climbing su roccia per formare gruppi di amatori e professionisti">
<meta name="language" content="italian">
<meta name="keywords" content="madebycambiamentico, credits">

<link rel="stylesheet" href="../../CSS/main.css" type="text/css">
<link rel="stylesheet" href="../../CSS/blog.css" type="text/css">
<link rel="stylesheet" media="screen and (max-width:719px)" href="../../CSS/main.phone.css" type="text/css">

<style type="text/css">
</style>

<script src="../../JS/jquery/1/jquery-1.10.2.min.js"></script>
<script>
$(function(){
	$('#rightComment .compressed').removeClass("compressed");
});
</script>

</head>


<body class="blog1">

<table id="ausl">
<tr>




<!-- ruler -->
<td>

<div id="leftComment">
<div id="logo"></div>
<h1>A.U.S.L.<br>Credits</h1>

<?php if ($imlogged) echo "<h3 class='welcome'><a href='../ACCOUNTS/account.php'>Ciao ".$_SESSION['username']."</a>!</h3>" ?>

<h3 class="cent"><a href="../../">homepage</a></h3>
<h3 class="cent"><a href="../BLOG/blog.php?b=1&w=0">Arrampicata</a></h3>
<h3 class="cent"><a href="../BLOG/blog.php?b=2&w=0">Sci alpinismo</a></h3>
<h3 class="cent"><a href="../BLOG/blog.php?b=3&w=0">Escursionismo</a></h3>
<h3 class="cent"><a href="../BLOG/blog.php?b=4&w=0">Development</a></h3>

<a id="mbc" href="http://www.cambiamentico.altervista.org" target="_blank">MadeByCambiamentico</a>
</div>

</td>




<!-- buttons -->
<td id="rightComment">


<table class="fumetto blue compressed">
<tr>
<th><div></div></th>
	<td>
	<h3>Coding</h3>
	<p><a href="http://www.cambiamentico.altervista.org">Nereo Costacurta [MBC &copy; 2014]</a></p>
	<p>Tutto extra professionale. Dalla prima all'ultima riga di codice è tutto pensato per ottenere il massimo col minimo delle risorse.</p>
	<p>Una nota sull'host mitico Altervista: è gratuito, perciò per ora (febbraio 2014) abbiamo 1GB di spazio disponibile compreso il database, e 10GB di dati di trasferimento</p>
	<p>Il sito è strutturato per essere completamente libero a chiunque volesse accedervi. Non ci sono restrizioni nè controlli in sintonia con gli ideali politici del cliente Montipò & Co., spero non diventi un casino incontrollato...! Tradotto: comportatevi bene e pensate anche agli altri.</p>
	</td>
</tr>
</table>


<table class="fumetto red">
<tr>
<th><div></div></th>
	<td>
	<h3>Grafica</h3>
	<p><a href="http://www.cambiamentico.altervista.org">Nereo Costacurta [MBC &copy; 2014]</a></p>
	<p>Un paio di parole per chi pensasse sia tutto troppo spoglio:<br>
	- Minimalismo.<br>
	- Velocità.<br>
	- Efficienza.</p>
	<p>Non chiedete di mettere immagini di sfondo, appesantirebbero solo il sito, che vuol essere ultra-leggero sin dal primo accesso.</p>
	<p>Ho detto tutto ;)</p>
	</td>
</tr>
</table>


<table class="fumetto green">
<tr>
<th><div></div></th>
	<td>
	<h3>Colori</h3>
	<p>Nereo Costacurta e Riccardo Montipò</p>
	<p>Per ora la community non ha ancora avuto modo di discutere, ma siamo felici di avere un feedback da chiunque volesse dare il proprio contributo e opinione.</p>
	</td>
</tr>
</table>


<table class="fumetto blue compressed">
<tr>
<th><div></div></th>
	<td>
	<h3>Plugin: meteo trentino</h3>
	<p><a href="http://www.cambiamentico.altervista.org">Nereo Costacurta [MBC &copy; 2014]</a></p>
	<p><a href="../ACCOUNTS/account.php?id=7">Matteo Ragni [PenguinKowalski]</a></p>
	<p>Mitica app extra leggera per ottenere informazioni sul meteo nella Provincia Autonoma di Trento, basato sui dati giornalieri XML messi a disposizione dalla Regione nel sito meteotrentino.it; PROGETTO OPENSOURCE aperto a chiunque voglia apportare miglioramenti!</p>
	<p>Link al sorgente: <a target="_blank" href="https://gist.github.com/madebycambiamentico/9259309">classe php</a> | <a target="_blank" href="https://gist.github.com/madebycambiamentico/9259261">application php</a></p>
	<p>Link all'applicazione: <a target="_blank" href="http://ausl.altervista.org/PAGES/BLOG/app-meteo/ausl-meteo-app-direct.2.0.php">meteotrentino AUSL app 2.0</a></p>
	</td>
</tr>
</table>


</td>




</tr>
</table>


</body>


</html>
