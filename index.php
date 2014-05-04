<?php
include 'PHP/database_connection.php';
include 'PHP/functions.php';

sec_session_start();

$imlogged = is_logged($mysqli);

?>
<!DOCTYPE HTML>

<html>


<head>
<title>A.U.S.L. | Arrampicata Universitaria Sociale Libera Trento</title>
<meta charset="UTF-8">
<meta name="author" content="Costacurta Nereo | MBC">
<meta name="viewport" content="width=device-width">
<meta name="description" content="Dedicata a tutti gli universitari amanti del climbing su roccia - e non solo! - per formare gruppi di amatori e professionisti">
<meta name="language" content="italian">
<meta name="keywords" content="scalata Trento, climbing trento, università trento, arrampicata trento, arrampicata unitn, alpinismo">

<link rel="stylesheet" href="CSS/main.css" type="text/css">
<link rel="stylesheet" media="screen and (max-width:719px)" href="CSS/main.phone.css" type="text/css">

<style type="text/css">
</style>

<script src="JS/jquery/1/jquery-1.10.2.min.js"></script>
<script>
$(function(){
	$('#rightComment .compressed').removeClass("compressed");
});
</script>

</head>


<body>

<table id="ausl">
<tr>




<!-- ruler -->
<td>

<div id="leftComment" class="nastro">
<div id="logo"></div>
<h1>Arrampicata Universitaria Sociale e Libera<br><br>Trento</h1>

<?php
if ($imlogged)
	echo "<h3 class='welcome'><a href='PAGES/ACCOUNTS/account.php'>Ciao ".$_SESSION['username']."</a>!</h3>".
		'<h3 class="cent"><a href="logs.php">Logout</a></h3>';
else
	echo '<h3 class="cent"><a href="logs.php">Login/<br>Registrati</a></h3>';
?>

<div class="infosss">
<p>Arrampicate, sciate, camminate liberi.</p>
<p>Nessuna tessera associativa.</p>
<p>Abbiate passione, abbiate desiderio, abbiate pazienza.</p>
<p>Condividete le vostre emozioni, siate sociali.</p>
<p>Andate oltre le apparenze, siate critici.</p>
<p>Siate testimoni del matrimonio tra le parole e la montagna.</p>
<p>Sappiate stare in silenzio quando nulla vi è da aggiungere alla vista.</p>
<p>Divertitevi.</p>
</div>

<br>
<h3 class="cent"><a href="PAGES/BLOG/blog.php?b=4&w=0">Development e Richieste</a></h3>

<h3 class="cent"><a href="PAGES/CREDIT/mbc.php">Credits</a></h3>

<a id="mbc" href="http://www.cambiamentico.altervista.org" target="_blank">MadeByCambiamentico</a>
</div>

</td>




<!-- buttons -->
<td id="rightComment">


<table class="fumetto red compressed">
<tr>
<th><div></div></th>
	<td style="background:url(IMAGES/homepage/arramp.jpg) left center">
	<h3 class="big"><a href="PAGES/BLOG/blog.php?b=1&w=0">Arrampicata</a></h3>
	</td>
</tr>
</table>


<table class="fumetto blue">
<tr>
<th><div></div></th>
	<td style="background:url(IMAGES/homepage/neve.jpg) left center">
	<h3 class="big"><a href="PAGES/BLOG/blog.php?b=2&w=0">Neve</a></h3>
	</td>
</tr>
</table>


<table class="fumetto green">
<tr>
<th><div></div></th>
	<td style="background:url(IMAGES/homepage/escurs.jpg) right center">
	<h3 class="big"><a href="PAGES/BLOG/blog.php?b=3&w=0">Escursionismo</a></h3>
	</td>
</tr>
</table>


</td>




</tr>
</table>


</body>


</html>
