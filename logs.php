<?php
include 'PHP/database_connection.php';
include 'PHP/functions.php';

sec_session_start();

$imlogged = is_logged($mysqli);

?>
<!DOCTYPE HTML>

<html>


<head>
<title>A.U.S.L. | Log-in-out</title>
<meta charset="UTF-8">
<meta name="author" content="Costacurta Nereo | MBC">
<meta name="viewport" content="width=device-width">
<meta name="description" content="Dedicata a tutti gli universitari amanti del climbing su roccia per formare gruppi di amatori e professionisti">
<meta name="language" content="italian">

<link rel="stylesheet" href="CSS/main.css" type="text/css">
<link rel="stylesheet" href="CSS/blog.css" type="text/css">
<link rel="stylesheet" media="screen and (max-width:719px)" href="CSS/main.phone.css" type="text/css">

<style type="text/css">
.inactive{
	display:none!important;
}
</style>

<script src="JS/jquery/1/jquery-1.10.2.min.js"></script>
<script src="JS/log/log.js?v=1.2"></script>
<script src="JS/hash/sha512.js"></script>

</head>


<body class="blog1">

<table id="ausl">
<tr>




<!-- ruler -->
<td>

<div id="leftComment">
<div id="logo"></div>
<h1>Arrampicata Universitaria Sociale e Libera<br><br>Trento</h1>

<?php if ($imlogged) echo "<h3 class='welcome'><a href='PAGES/ACCOUNTS/account.php'>Ciao ".$_SESSION['username']."</a>!</h3>" ?>

<h3 class="cent"><a href="./">homepage</a></h3>
<h3 class="cent"><a href="PAGES/BLOG/blog.php?b=1&w=0">Arrampicata</a></h3>
<h3 class="cent"><a href="PAGES/BLOG/blog.php?b=2&w=0">Neve</a></h3>
<h3 class="cent"><a href="PAGES/BLOG/blog.php?b=3&w=0">Escursionismo</a></h3>
<h3 class="cent"><a href="PAGES/BLOG/blog.php?b=4&w=0">Development</a></h3>
<h3 class="cent"><a href="PAGES/ACCOUNTS/account-list.php">Lista degli accounts</a></h3>

<div class="infosss">
<p>Registrazione free. Ma free sul serio!</p>

<p>Per essere in linea con le leggi italiane, ti assicuriamo che i tuoi dati non saranno distribuiti a nessuno che non sia dello staff dell'AUSL secondo la norma vigente n. 196/2003. La tua mail rimarrà sempre invisibile a tutti i membri della community. Gli unici dati visibili saranno il tuo nome e le eventuali immagini che vorrai caricare sul sito.</p>

<p>Registrandoti acconsenti a tutto quello che sopra è descritto, sollevandoci da ogni responsabilità derivante da un uso improprio delle risorse della community.</p>
</div>

<a id="mbc" href="http://www.cambiamentico.altervista.org" target="_blank">MadeByCambiamentico</a>
</div>

</td>




<!-- buttons -->
<td id="rightComment">


<table class="fumetto red compressed<?php if ($imlogged) echo " inactive" ?>" id="login">
<tr>
<th><div></div></th>
	<td>
	<h3>Login</h3>
	<form id="loggati" method="post" action="PHP/gZipped.php" autocomplete="on">
	<input type="hidden" name="html" value="true">
	<p>inserisci la tua email</p>
	<input type="text" name="e" id="emaillog">
	<p>inserisci la tua password</p>
	<input type="password" id="pl">
	<input type="submit" value="LOGGAMI!">
	</form>
	<div id="resLogin"></div>
	</td>
</tr>
</table>


<table class="fumetto blue compressed<?php if (!$imlogged) echo " inactive" ?>" id="logout">
<tr>
<th><div></div></th>
	<td>
	<h3>Logout</h3>
	<form id="sloggati" method="get" action="PHP/helloW.php">
	<p>In questo momento risulti loggato.</p>
	<p>Per effettuare il logout (ed impedire a qualcuno che potrebbe usare il tuo computer di scrivere firmandosi a tuo nome) clicca il bottone qui di seguito...</p>
	<input type="submit" value="VOGLIO USCIRE!">
	</form>
	</td>
</tr>
</table>


<table class="fumetto yellow">
<tr>
<th><div></div></th>
	<td>
	<h3>Ho perso la password</h3>
	<p>Se ti sei dimenticato la password, non creare altri account inutilmente.<br>
	Qui puoi spedire alla mail del tuo account una nuova password generata dal server.</p>
	<form id="lostpass" method="get" action="PHP/lostpass.php">
	<p>inserisci la tua mail di registrazione</p>
	<input type="text" name="e">
	<input type="submit" value="RECUPERA ACCOUNT!">
	</form>
	<div id="resLostPass"></div>
	</td>
</tr>
</table>


<table class="fumetto green">
<tr>
<th><div></div></th>
	<td>
	<h3>Registrati</h3>
	<p>La registrazione è completamente free.</p>
	<form id="iscriviti" method="post" action="PHP/7Zip.php">
	<p>inserisci il tuo nome vero e/o soprannomi. Ricordati che il gruppo deve riconoscerti.</p>
	<input type="text" name="n">
	<p>inserisci la tua email... rimarrà sempre segreta, don't worry!</p>
	<input type="text" name="e" id="emailreg">
	<p>inserisci una password... e ricordatela!</p>
	<input type="password" id="p1">
	<p>reinserisci la password</p>
	<input type="password" id="p2">
	<input type="submit" value="REGISTRAMI!">
	</form>
	<div id="resRegistra"></div>
	</td>
</tr>
</table>


</td>




</tr>
</table>


</body>


</html>
