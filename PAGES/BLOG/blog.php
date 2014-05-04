<?php
include '../../PHP/database_connection.php';
include '../../PHP/functions.php';

sec_session_start();

$imlogged = is_logged($mysqli);

//controllo variabili
if (!isset($_GET['w']) || !is_numeric($_GET['w'])) $_GET['w']=0;
if (!isset($_GET['b']) || !is_numeric($_GET['b'])) $_GET['b']=1;
if ($_GET['b']<1) $_GET['b']=1;
if ($_GET['b']>4) $_GET['b']=4;

function getPageTitle(){
	switch($_GET['b']){
		case 1: return 'Arrampicata'; break;
		case 2: return 'Neve'; break;
		case 3: return 'Escursionismo'; break;
		case 4: return 'Development'; break;
		default: return 'Boh';
	}
}

function getPageImage(){
	switch($_GET['b']){
		case 1: return '<img class="btn" src="../../IMAGES/rock/mano.jpg">'; break;
		case 2: return '<img class="btn" src="../../IMAGES/sci/scialp.jpg">'; break;
		case 3: return '<img class="btn" src="../../IMAGES/escursioni/escurs.jpg">'; break;
		case 4: return ''; break;
		default: return '';
	}
}


//date di ricerca
$maxexpire = time();

//utenti temporanei da cercare
$toFindUsers = array();

//autori o commentatori
$users = array();

//ricerca posts
$posts = array();
if ($stmt = $mysqli->prepare("SELECT id,tipo,data,dataevento,autore,titolo,content,expire FROM blog WHERE blog=? AND expire>? ORDER BY id DESC")){
	$stmt->bind_param("ii",$_GET['b'],$maxexpire);
	$stmt->execute();
	$stmt->bind_result($id,$tipo,$data,$dataevento,$autore,$titolo,$content,$expire);
	while($stmt->fetch()){
		$posts[$id] = array(
			'type' => $tipo,
			'd' => $data,
			'de' => htmlentities(utf8_encode($dataevento),NULL,'UTF-8'),
			'a' => $autore,
			't' => htmlentities(utf8_encode($titolo),NULL,'UTF-8'),
			'c' => utf8_encode($content),
			'end' => date('d/m/Y',$expire-86400)
		);
		if (!isset($users[$autore])) $toFindUsers[] = $autore;
	}
}


foreach($posts as $k => $p){
	//search comments for this post
	if ($res = $mysqli->query("SELECT id,autore,data,content FROM commenti WHERE post=".$k." ORDER BY id DESC")){
		while($r = $res->fetch_assoc()){
			$posts[$k]['comments'][] = array(
				'i' => $r['id'],
				'a' => $r['autore'],
				'd' => date('d M \a\l\l\e H:i',$r['data']),
				'c' => utf8_encode($r['content'])
			);
			//store not known users' name
			if (!isset($users[$r['autore']])) $toFindUsers[] = $r['autore'];
		}
		mysqli_free_result($res);
	}
	else echo '-error comment-';
}


if (count($toFindUsers)){
	$toFindUsers = 'id = '.implode(' OR id = ',$toFindUsers);
	if ($res = $mysqli->query("SELECT id, nome FROM users WHERE ".$toFindUsers)){
		while($r = $res->fetch_assoc()){
			$users[$r['id']] = htmlentities(utf8_encode($r['nome']),NULL,'UTF-8');
		}
		mysqli_free_result($res);
	}
	else echo '-error user-';
}

?>
<!DOCTYPE HTML>

<html>


<head>
<title>A.U.S.L. | Blog :: <?php echo getPageTitle() ?></title>
<meta charset="UTF-8">
<meta name="author" content="Costacurta Nereo | MBC">
<meta name="viewport" content="width=device-width">
<meta name="description" content="Dedicata a tutti gli universitari amanti del climbing su roccia per formare gruppi di amatori e professionisti">
<meta name="language" content="italian">
<meta name="keywords" content="scalata Trento, climbing trento, università trento, arrampicata trento, arrampicata unitn, alpinismo">

<link rel="stylesheet" href="../../CSS/main.css" type="text/css">
<link rel="stylesheet" href="../../CSS/blog.css" type="text/css">
<link rel="stylesheet" href="../../CSS/markdown.css" type="text/css">
<link rel="stylesheet" href="../../JS/datepicker/css/datepicker.large.min.css" type="text/css">
<link rel="stylesheet" media="screen and (max-width:719px)" href="../../CSS/main.phone.css" type="text/css">

<style type="text/css">
<?php
if ($imlogged){
	if ($_SESSION['prefer'][0]!=1){
?>
.fumetto ul.wmd-button-row{
	display:none;
}
<?php
	}
}
?>
</style>

<script src="../../JS/jquery/1/jquery-1.10.2.min.js"></script>
<script src="../../JS/blog/blog.js"></script>
<script src="../../JS/pagedown/Markdown.Converter.mbc.min.js"></script>
<script src="../../JS/pagedown/Markdown.Sanitizer.mbc.min.js"></script>
<script src="../../JS/pagedown/Markdown.Editor.mbc.min.js"></script>
<script src="../../JS/datepicker/js/datepicker_ita.min.js"></script>
<script>
$(function(){
	$('#ilmeteo').click(function(){
		window.open("app-meteo/ausl-meteo-app-direct.2.0.php?quick=0","ausl-meteo-app","width=300,height=650,top=0,left=0").focus();
	});
});
</script>

</head>


<body class="blog<?php echo $_GET['b'] ?>">

<table id="ausl">
<tr>




<!-- ruler -->
<td>

<div id="leftComment" class="nastro">
<div id="logo"></div>
<?php
if ($imlogged)
	echo "<h3 class='welcome'><a href='../ACCOUNTS/account.php'>Ciao ".$_SESSION['username']."</a>!</h3>".
		'<p id="newpost" class="leftbuts">INSERISCI NUOVO POST</p>'.
		'<p id="ilmeteo" class="leftbuts">VEDI METEO</p>';
?>
<div id="mylogs"></div>
<h1><?php echo getPageTitle().getPageImage() ?></h1>

<h3 class="cent"><a href="../../">homepage</a></h3>

<?php
if ($imlogged)
	echo '<h3 class="cent"><a href="../../logs.php">Logout</a></h3>';
else
	echo '<h3 class="cent"><a href="../../logs.php">Login/<br>Registrati</a></h3>';
?>

<div class="infosss">
<p>Per creare un evento o commentarlo devi essere registrato e ovviamente loggato.</p>
<p>Attenzione! Dopo il loro invio, i post e commenti non sono più modificabili.<br>Suggerimento: scrivi bene e controlla un par di volte.</p>
<p>NB - il blog è pubblico</p>
<div>

<br><br><br>
<div class="infosss jumper">
<h4>Link veloci</h4>
<?php
foreach ($posts as $k => $p){
	echo '<a href="#post'.$k.'">'.$p['t'].'</a>';
}
?>
</div>

<?php if ($_GET['b']==1){ ?>
<br><br><br>
<div class="infosss">
<p>DOWNLOADS</p>
<a href="../../FILES/montipo/CINEMATICA_E_RISCALDAMENTO.pdf" target="_blank">Arte del Riscaldamento</a><br>
<a href="../../FILES/montipo/ARRAMPICARE.pdf" target="_blank">L'Arrampicata</a><br>
<a href="../../FILES/montipo/TECNOLOGIA.pdf" target="_blank">Tecnologie</a>
</div>
<?php } ?>

<?php if ($_GET['b']==4){ ?>
<br><br><br>
<div class="infosss">
<p>DOWNLOADS</p>
<a href="../../FILES/nereo/A.U.S.L-A4.pdf" target="_blank">Locandina A.U.S.L.</a><br>
<a href="../../FILES/nereo/A.U.S.L-2_in_A4.pdf" target="_blank">Volantini A.U.S.L.</a><br>
</div>
<?php } ?>


<a id="mbc" href="http://www.cambiamentico.altervista.org" target="_blank">MadeByCambiamentico</a>
</div>

</td>




<!-- buttons -->
<td id="rightComment">


<table class="fumetto yellow" id="inserimento">
<tr>
<th><div></div></th>
	<td>
	<h3>Inserimento post</h3>
	<form id="evento" method="post" action="event.php">
	<input type="hidden" name="b" value="<?php echo $_GET['b'] ?>">
	<select name="type" id="evTipo">
	<option value="1">Evento</option>
	<option value="2">Proposta</option>
	<option value="3">Discussione</option>
	<option value="0">Altro</option>
	</select>
	<p>Titolo</p>
	<input type="text" name="title" id="evTitle">
	<p>Data inizio evento (testuale)</p>
	<input type="text" name="data" id="evData">
	<p>Data fine evento (formato obbligatorio: d/m/YYYY)</p>
	<input type="text" name="expire" id="eventDatePick" maxlength="10">
	<p>Descrizione completa</p>
	<div class="wmd-panel">
		<div id="wmd-button-bar0"></div>
		<textarea name="desc" class="wmd-input" id="wmd-input0"></textarea>
	</div>
	<div id="wmd-preview0" class="wmd-panel wmd-preview"></div>
	<input type="submit" id="inviaSubmit" value="INVIA!"><b id="inviaWait" class="loader"></b>
	</form>
	<p>I post non sono soggetti a censura.</p>
	<p>Per questione di spazio siate parsimoniosi.</p>
	<p>Per le discussioni inserire "-" nella data inizio evento.</p>
	</td>
</tr>
</table>



<?php

/*
post:
	- GIALLO * inserimento nuovo post
	- green = evento noto
	- blue = proposta/sondaggo/richiesta evento
	- red = ritrovo
	- ... = altro

variabili:
	- b = gruppo blog (arrampicata / sci alpinismo...)
	- w = numero settimane indietro da guardare. il periodo si conta da oggi-(w+2)*settimana fino a oggi-w*settimana
		(ultime due settimane a partire da w*settimane fa)
*/


//stampa posts
$first = true;
foreach ($posts as $k => $p){
		//print post
?>
<table class="fumetto<?php
	if($first===true){
		echo ' compressed';
		$first = false;
	}
	switch($p['type']){
		case 1: echo ' green'; break;
		case 2: echo ' blue'; break;
		case 3: echo ' red'; break;
		default:
	}
?>" id="post<?php echo $k ?>">
<tr>
<th><a name="post<?php echo $k ?>"></a><div>
<p class="users" <?php echo 'data-id="'.$p['a'].'"' ?>><?php echo $users[$p['a']].'<br>'.date('d/m/Y',$p['d']) ?></p>
</div></th>
<td>
	<h3><?php
	switch($p['type']){
		case 1: echo 'EVENTO: '; break;
		case 2: echo 'PROPOSTA: '; break;
		case 3: echo 'DISCUSSIONE: '; break;
		default:
	}
	echo $p['t'];
	?></h3>
	<?php echo ($p['de']!='-' ? '<h4>Data prevista: '.$p['de'].'</h4>' : '') ?>
	<h4>Data fine evento: <?php echo $p['end'] ?></h4>
	<p><?php echo $p['c'] ?></p>

<?php
if ($imlogged){
?>
	<h4>Join or Comment!</h4>
	<form class="join" method="get" action="blogComment.php">
		<p>Inserisci un tuo commento o la tua sottoscrizione all'evento:</p>
		<input type="hidden" name="id" value="<?php echo $k ?>">
		<input type="hidden" name="b" value="<?php echo $_GET['b'] ?>">
		<div class="wmd-panel">
			<div id="wmd-button-bar<?php echo $k ?>"></div>
			<textarea name="c" class="wmd-input" id="wmd-input<?php echo $k ?>"></textarea>
		</div>
		<div id="wmd-preview<?php echo $k ?>" class="wmd-panel wmd-preview"></div>
		<input type="submit" value="COMMENTA!"><i class="loader" data-id="<?php echo $k ?>"></i>
	</form>
<?php } ?>

	<div class="joincomment" id="jc<?php echo $k ?>">
<?php
if (isset($p['comments'])){
	$first=true;
	foreach ($p['comments'] as $c){
		if ($first=true){
			$first=false;
			echo '<p class="chi" data-id="'.$c['a'].'">'.$users[$c['a']].' - '.$c['d'].'</p>'.
			'<div class="com" data-id="'.$c['i'].'">'.$c['c'].'</div>';
		}
		else
			echo '<p class="chi">'.$users[$c['a']].' - '.$c['d'].'</p>'.
			'<div class="com" data-id="'.$c['i'].'">'.$c['c'].'</div>';
	}
}
?>
	</div>
	<div class="joinflag" id="jf<?php echo $k ?>"></div>
</td>
</tr>
</table>
<?php
}
?>



</td>




</tr>
</table>


</body>


</html>
