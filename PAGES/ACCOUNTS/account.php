<?php
include '../../PHP/database_connection.php';
include '../../PHP/functions.php';

sec_session_start();

$imlogged = is_logged($mysqli);
//exit if not logged
if (!$imlogged){
	header("Location: ../../not_found.php");
	exit();
}

//controllo variabili vedo se richiesto un id.
$id=0;
if (isset($_GET['id']) && is_numeric($_GET['id'])) $id=$_GET['id'];
elseif ($imlogged) $id=$_SESSION['user_id'];
else{
	header("Location: ../../not_found.php");
	exit();
}

//ricerca dati
$account = array();
if ($res = $mysqli->query("SELECT * FROM users WHERE id=".$id." LIMIT 1")){
	if ($res->num_rows==0){
		header("Location: ../../not_found.php");
		exit();
	}
	$account = $res->fetch_assoc();
}


//ricerca album pubblici / privati
if (!isset($_GET['code'])) $_GET['code']='';

$posts = array();
//show all results
if ($id == $_SESSION['user_id']){
	if ($stmt = $mysqli->prepare("SELECT id,link,title,content,private,code FROM gallery WHERE owner=? ORDER BY data DESC")){
		$stmt->bind_param('i',$id);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($i,$l,$t,$c,$p,$code);
		while ($stmt->fetch()){
			$posts[$i] = array(
				'i' => $i,
				'l' => $l,
				't' => htmlentities(utf8_encode($t),NULL,'UTF-8'),
				'c' => utf8_encode($c),
				'p' => $p,
				'code' => $code
			);
		}
		$stmt->close();
	}
}
//show only public results or private if matched code
elseif ($stmt = $mysqli->prepare("SELECT id,link,title,content,private,code FROM gallery WHERE owner=? ORDER BY data DESC")){
	$stmt->bind_param('i',$id);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($i,$l,$t,$c,$p,$code);
	while ($stmt->fetch()){
		if ($p==1 && $code!=$_GET['code']) continue;
		$posts[$i] = array(
			'i' => $i,
			'l' => $l,
			't' => htmlentities(utf8_encode($t),NULL,'UTF-8'),
			'c' => utf8_encode($c),
			'p' => $p,
			'code' => $code
		);
	}
	$stmt->close();
}

//utenti temporanei da cercare
$toFindUsers = array();

//autori o commentatori
$users = array();

foreach($posts as $k => $p){
	//search comments for this post
	if ($res = $mysqli->query("SELECT id,autore,data,content FROM gallery_comment WHERE post=".$k." ORDER BY id DESC")){
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
<title>A.U.S.L. | Account :: <?php echo htmlentities(utf8_encode($account['nome']),NULL,'UTF-8') ?></title>
<meta charset="UTF-8">
<meta name="author" content="Costacurta Nereo | MBC">
<meta name="viewport" content="width=device-width">
<meta name="description" content="Dedicata a tutti gli universitari amanti del climbing su roccia per formare gruppi di amatori e professionisti">
<meta name="language" content="italian">

<link rel="stylesheet" href="../../CSS/main.css" type="text/css">
<link rel="stylesheet" href="../../CSS/blog.css" type="text/css">
<link rel="stylesheet" href="../../CSS/markdown.css" type="text/css">
<link rel="stylesheet" media="screen and (max-width:719px)" href="../../CSS/main.phone.css" type="text/css">

<style type="text/css">
.albums{
	margin:0 auto;
	height:500px;
	border:none;
	width:100%;
	background:#000;
}
#albumIframe{
	display:none;
}
#albumPreview, #albumInvia{
	display:inline-block
}
.privates {
	font-size: 11px;
	background: rgba(0, 0, 0, 0.1);
	font-style: italic;
	padding:2px 5px;
}
input[type=button].deletePost, input[type=button].inlbtn{
	display:inline-block;
}
</style>

<script src="../../JS/jquery/1/jquery-1.10.2.min.js"></script>
<script src="../../JS/form/iframe-post-form.min.js"></script>
<script src="../../JS/account/account.js"></script>
<script src="../../JS/pagedown/Markdown.Converter.mbc.min.js"></script>
<script src="../../JS/pagedown/Markdown.Sanitizer.mbc.min.js"></script>
<script src="../../JS/pagedown/Markdown.Editor.mbc.min.js"></script>

</head>


<body class="blog1">

<table id="ausl">
<tr>




<!-- ruler -->
<td>

<div id="leftComment">
<div id="logo"></div>
<img class="btn" id="myWonderProfile" data-id="<?php echo $_SESSION['user_id'] ?>" style="border:1px solid #000" src="../../IMAGES/account/<?php
	if(!empty($account['iHaveImage'])) echo 'allpeoples/user'.$id.'.jpg';
	else echo 'noImage.png';
?>">
<h3 class="cent"><?php echo htmlentities(utf8_encode($account['nome']),NULL,'UTF-8') ?></h3>
<?php if ($imlogged) echo '<h3 class="cent"><a href="../../logs.php">logout</a></h3>'; ?>
<br>
<h3 class="cent"><a href="../../">homepage</a></h3>
<h3 class="cent"><a href="../BLOG/blog.php?b=1&w=0">Arrampicata</a></h3>
<h3 class="cent"><a href="../BLOG/blog.php?b=2&w=0">Neve</a></h3>
<h3 class="cent"><a href="../BLOG/blog.php?b=3&w=0">Escursionismo</a></h3>
<h3 class="cent"><a href="../BLOG/blog.php?b=4&w=0">Development</a></h3>
<h3 class="cent"><a href="account-list.php">Lista degli accounts</a></h3>

<br><br><br>
<div class="infosss jumper">
<h4>Link veloci</h4>
<?php
foreach ($posts as $k => $p){
	echo '<a href="#post'.$k.'">'.$p['t'].'</a>';
}
?>
</div>

<a id="mbc" href="http://www.cambiamentico.altervista.org" target="_blank">MadeByCambiamentico</a>
</div>

</td>




<!-- buttons -->
<td id="rightComment">


<?php if ($id == $_SESSION['user_id']){ ?>
<table class="fumetto blue compressed">
<tr>
<th><div></div></th>
	<td>
	<h3>Il tuo profilo</h3>
	
	<h4>Immagine di profilo</h4>
	<p>Se vuoi puoi inserire qui un'immagine per il tuo profilo. Se ti è possibile preparati un ritaglio quadrato di dimensione 224x224px o maggiore in formato jpg/png/gif/bmp. 
	Nel caso avessi un'immagine non quadrata, AUSL provvederà per te a ritagliarla e ridimensionarla, ma non garantiamo il risultato ;)</p>
	<form id="postImage" action="myImage.php" method="POST" enctype="multipart/form-data">
		<input type="file" id="postFile" name="f">
		<input type="submit" value="INVIA FOTO!">
	</form>
	
	<br>
	<hr>
	<h4>Modifica password</h4>
	<form id="changePass">
		<p>vecchia password:</p>
		<input type="password" id="vp">
		<p>nuova password:</p>
		<input type="password" id="np">
		<input type="submit" value="CAMBIA!">
	</form>
	
	<br>
	<hr>
	<h4>Preferenze</h4>
	<form id="prefer" action="preference.php" method="GET">
		<p><input type="checkbox" id="preferMark" name="mark" value="1" <?php echo ($_SESSION['prefer'][0] == 1 ? 'checked' : '') ?>> <label for="preferMark">Aiuti al markdown</label></p>
		<p><input type="checkbox" id="preferListMe" name="list" value="1" <?php echo ($_SESSION['prefer'][1] == 1 ? 'checked' : '') ?>> <label for="preferListMe">Mostrami nella lista accounts</label></p>
		<p><input type="checkbox" id="preferNews" name="news" value="1" <?php echo ($_SESSION['prefer'][2] == 1 ? 'checked' : '') ?>> <label for="preferNews">Ricevi newsletter (non succederà comunque)</label></p>
		<input type="submit" value="SALVA PREFERENZE">
	</form>

	</td>
</tr>
</table>




<table id="profileEdit" class="fumetto yellow">
<tr>
<th><div></div></th>
	<td>
	<h3>Post personali</h3>
	<form id="postGallery" action="myGallery.php" method="GET">
		<p>titolo:</p>
			<input id="albumTitle" type="text" name="t">
		<p>descrizione:</p>
			<div class="wmd-panel">
				<div id="wmd-button-bar0"></div>
				<textarea name="c" class="wmd-input" id="wmd-input0"></textarea>
			</div>
			<div id="wmd-preview0" class="wmd-panel wmd-preview"></div>
		<p>
		<br>
		<input type="radio" name="private" value="1" id="privato"> <label for="privato">Privato: solo chi possiede il link diretto può visualizzare questo post</label>
		<br>
		<input type="radio" name="private" value="0" id="pubblico" checked> <label for="pubblico">Pubblico: chiunque acceda al tuo profilo potrà vedere questo post</label>
		</p>
		<input type="submit" id="albumInvia" value="INVIA!">
	</form>
	<iframe id="albumIframe" src="" class="albums"></iframe>

	</td>
</tr>
</table>

<?php } ?>



<?php

//stampa album
foreach ($posts as $k => $p){

?>

<table class="fumetto green" id="post<?php echo $k ?>"><tr>
	<th><div></div></th>
	<td>

<?php
	//link espresso
	if ($id == $_SESSION['user_id']){
		//id = account, code = codice, postXXX = navigate to post
		if ($p['p']==1) echo "<p class='privates'><b>link al post privato:</b><br><a name='post".$k."'>http://ausl.altervista.org/PAGES/ACCOUNTS/account.php?id=".$id."&code=".$p['code']."#post".$k."</a></p>";
		else echo "<p class='privates'><b>link al post pubblico:</b><br><a name='post".$k."'>http://ausl.altervista.org/PAGES/ACCOUNTS/account.php?id=".$id."#post".$k."</a></p>";
	}
	else echo "<a name='post".$k."'></a>";
	
	//titolo e commenti
	echo '<h3>'.$p['t'].'</h3><p>'.$p['c'].'</p>';
	
	//iframe per slideshow
	if ($p['l'] !== '') echo '<iframe id="album'.$k.'" class="albums" src="'.$p['l'].'"></iframe>';
?>
	<h4>Comment!</h4>
	<form class="join" method="get" action="blogComment.php">
		<input type="hidden" name="id" value="<?php echo $k ?>">
		<div class="wmd-panel">
			<div id="wmd-button-bar<?php echo $k ?>"></div>
			<textarea name="c" class="wmd-input" id="wmd-input<?php echo $k ?>"></textarea>
		</div>
		<div id="wmd-preview<?php echo $k ?>" class="wmd-panel wmd-preview"></div>
		<?php if ($id == $_SESSION['user_id']) echo '<input type="button" class="deletePost" data-id="'.$k.'" value="CANCELLA POST!">'; ?>
		<input type="submit" value="COMMENTA!"><i class="loader" data-id="<?php echo $k ?>"></i>
	</form>
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
	</td>
</tr></table>

<?php } ?>


</td>



</tr>
</table>


</body>


</html>
