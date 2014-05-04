<?php
require 'class.meteotrentino.2.0.php';
// madebycambiamentico
// open source
?>

<!DOCTYPE HTML>

<html>


<head>
<title>A.U.S.L. | Meteo App</title>
<meta charset="UTF-8">
<meta name="author" content="Costacurta Nereo | MBC">
<meta name="viewport" content="width=device-width">
<meta name="description" content="Applicazione meteo trentino - AUSL developer">
<meta name="language" content="italian">
<meta name="keywords" content="scalata Trento, climbing trento, università trento, arrampicata trento, arrampicata unitn">

<style type="text/css">
*{padding:0;margin:0}

body{
	font:13px 'Open Sans',Helvetica,Arial,sans serif;
	color:#666;
	background:none;
}


div.meteotrentino{
	max-width:300px;
	word-break:break-word;
	margin:10px auto;
	padding-bottom:50px;
	background:url(../../../IMAGES/meteo/opendata-trentino-s.png) no-repeat no-repeat center bottom;
}

div.meteoday{
	margin-bottom:30px;
	border-top: 1px solid #CCC;
	padding-top:8px;
}

div.meteotrentino p{
	margin-bottom:5px;
	padding:0 10px;
	text-transform:lowercase;
}

div.meteotrentino p.day{
	font-size:15px;
	text-align:center;
	color:#333;
	text-transform:uppercase;
}
div.meteotrentino p.pubbdata, div.meteotrentino p.genprev{
	font-size: 16px;
	color:#8494B6;
}
div.meteotrentino p.pubbdata{
	font-size: 12px;
	font-wieght:bold;
	text-align:center;
	text-transform:uppercase;
}
div.meteotrentino p.genprev{
	margin-bottom:30px;
	text-transform: none;
}
div.meteotrentino p.title {
	font-size: 12px;
	color: #000;
	border-left: 2px solid #666;
	margin: 5px 0 0 10px;
	padding-left: 5px;
	text-transform: uppercase;
}

div.meteotrentino img.trentino{
	display:block;
	margin:auto;
	width:236px;
	height:241px;
}

</style>

</head>


<body>
<?php

	$previsioni = new meteoBaseGeneral();
	
	$quick = true;
	if (isset($_GET['quick'])){
		if ($_GET['quick']==0) $quick = false;
	}
	
	$day = 'oggi';
	if (isset($_GET['day'])){
		$day = $_GET['day'];
		if ($quick) $previsioni->giorno_quick($day);
		else $previsioni->giorno($day);
	}
	else{
		if ($quick) $previsioni->html_quick();
		else $previsioni->html();
	}

?>
</body>


</html>
