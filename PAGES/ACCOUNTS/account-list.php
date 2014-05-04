<?php
include '../../PHP/database_connection.php';
include '../../PHP/functions.php';

sec_session_start();

$imlogged = is_logged($mysqli);

?>
<!DOCTYPE HTML>

<html>


<head>
<title>A.U.S.L. | Lista accounts</title>
<meta charset="UTF-8">
<meta name="author" content="Costacurta Nereo | MBC">
<meta name="viewport" content="width=device-width">
<meta name="description" content="Dedicata a tutti gli universitari amanti del climbing su roccia per formare gruppi di amatori e professionisti">
<meta name="language" content="italian">

<link rel="stylesheet" href="../../CSS/main.css" type="text/css">
<link rel="stylesheet" href="../../CSS/blog.css" type="text/css">
<link rel="stylesheet" media="screen and (max-width:719px)" href="../../CSS/main.phone.css" type="text/css">

<style type="text/css">
.lists a{
	display:inline-block;
	padding:5px 10px;
	margin:2px;
	background: rgba(255, 255, 255, 0.2);
	-webkit-border-radius: 5px;
	border-radius: 5px;
	-webkit-transition:background 0.2s;
	transition:background 0.2s;
}
.lists a:hover {
	background: rgba(255, 255, 255, 0.3);
}
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
<h1>A.U.S.L.<br>Lista Accounts</h1>

<?php if ($imlogged) echo "<h3 class='welcome'><a href='account.php'>Ciao ".$_SESSION['username']."</a>!</h3>" ?>

<h3 class="cent"><a href="../../">homepage</a></h3>
<h3 class="cent"><a href="../BLOG/blog.php?b=1&w=0">Arrampicata</a></h3>
<h3 class="cent"><a href="../BLOG/blog.php?b=2&w=0">Neve</a></h3>
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
	<h3>Tips</h3>
	<h4>Scegli la pagina:</h4>
	<p class="lists">
<?php
	$maxPages = 1;
	$gapPages = 40;
	if ($res = $mysqli->query("SELECT COUNT(id) FROM users WHERE SUBSTRING(preferences,2,1)='1'")){
		if ($r = $res->fetch_array()) $maxPages = $r[0];
		mysqli_free_result($res);
	}
	if ($maxPages>1){
		$pp = ceil($maxPages/$gapPages);
		for ($i=0;$i<$pp;$i++){
			echo '<a href="account-list.php?p='.$i.'">'.($i+1).'</a>';
		}
	}
?>
	</p>
	</td>
</tr>
</table>


<table class="fumetto green">
<tr>
<th><div></div></th>
	<td>
	<h3>Utenti (max <?php echo $gapPages ?> per pagina)</h3>
<?php
	$page = 0;
	if (isset($_GET['p'])) if (is_numeric($_GET['p'])) $page = $_GET['p']*$gapPages;
	if($stmt = $mysqli->prepare("SELECT id,data,nome,email,lastLog FROM users WHERE SUBSTRING(preferences,2,1)='1' ORDER BY lastLog DESC LIMIT ?,".$gapPages)){
		$stmt->bind_param("i",$page);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($i,$d,$n,$e,$log);
		if ($stmt->num_rows==0) echo "<p>Purtroppo non ci sono risultati per questa pagina.</p><p>Inserisci un altro numero per la ricerca, magari sarai più fortunato...</p>";
		else
		while($stmt->fetch()){
			echo '<p class="chi">Registrato il '.date("d/m/Y - H:i",$d).' | ultima attività '.date("d/m/Y - H:i",$log).'</p>'.
				'<p class="com"><a target="_blank" href="account.php?id='.$i.'">'.htmlentities(utf8_encode($n),NULL,"UTF-8").'</a></p>';
		}
		$stmt->close();
	}
?>
	</td>
</tr>
</table>


</td>




</tr>
</table>


</body>


</html>
