<?php
/***************************************
					LOGIN
***************************************/

if (!isset($_POST['html'])) header("Content-type: application/json");

include 'database_connection.php';
include 'functions.php';

sec_session_start();

//print error in json way
function mbcError($s="errore sconosciuto"){
	if (!isset($_POST['html'])){
		$e = array("error" => $s);
		exit(json_encode($e));
	}
	else makeHtml($s,false);
}

//print error in json way
function mbcSuccess($s="ok"){
	if (!isset($_POST['html'])){
		$e = array("ok" => $s);
		exit(json_encode($e));
	}
	else makeHtml($s,true);
}

function makeHtml($message='',$success=true){
	?>
<!DOCTYPE HTML>
<html>

<head>
<title>A.U.S.L.</title>
<meta charset="UTF-8">
<meta name="author" content="Costacurta Nereo | MBC">
<meta name="viewport" content="width=device-width">

<style type="text/css">
*{margin:0;padding:0}
body {
	line-height:1.1;
	font:13px 'Open Sans',Helvetica,Arial,sans-serif;
	word-break: break-word;
}
.ausl{
	display:block;
	width:126px;
	height:100px;
	margin:30px auto;
}
.error{
	display:block;
	width:80px;
	height:80px;
	margin:10px auto;
}
.msg{
	text-align:center;
}
a{
	text-align:center;
	display:block;
	margin:5px auto;
	width:200px;
	padding:5px;
	text-transform:uppercase;
	text-decoration:none;
	
}
</style>

</head>

<body>

<img src="../IMAGES/LOGO/logomain.png" class="ausl"></img>
<?php echo '<p class="msg">'.$message.'</p>'; ?>
<br>

<?php if ($success) { ?>
<a href="../">homepage</a>
<a href="../PAGES/BLOG/blog.php?b=1&w=0">Arrampicata</a>
<a href="../PAGES/BLOG/blog.php?b=2&w=0">Sci alpinismo</a>
<a href="../PAGES/BLOG/blog.php?b=3&w=0">Escursionismo</a>
<a href="../PAGES/BLOG/blog.php?b=4&w=0">Development</a>
<?php } else { ?>
<img src="../IMAGES/log/error.png" class="error"></img>
<a href="../logs.php">torna al login</a>
<?php } ?>

</body>
</html>
	<?php
}

//controllo variabili
if (empty($_POST['e']) || empty($_POST['p'])) mbcError("Dati insufficienti...");

$email = utf8_decode(trim($_POST['e']));
$password = utf8_decode(trim($_POST['p']));

//attempt to login
if(login($email, $password, $mysqli) === true){
	mbcSuccess("Ohilà! Benvenuto ".$_SESSION['username']."!");
}
else{
	mbcError("Mi sa che hai proprio sbagliato password... o l'account");
}

?>
