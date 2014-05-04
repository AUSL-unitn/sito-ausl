<?php
//header("Content-type: application/json");

require '../../PHP/database_connection.php';
require '../../PHP/functions.php';

sec_session_start();

if (!is_logged($mysqli)) exit('{"error":"Non sei più loggato"}');

if (!isset($_FILES["f"])){
	exit('{"error":"Dati insufficienti"}');
}

$extensions = array(
	"jpg",	//1
	"jpeg",	//2
	"png",	//3
	"bmp",	//4
	"gif"		//5
);
$application = array(
	array("image/jpeg","image/pjpeg"),		//1
	array("image/jpeg","image/pjpeg"),		//2
	array("image/png"),							//3
	array("image/bmp"),							//4
	array("image/gif")							//5
);

function decodeFileError($code){
	switch ($code) {
		case UPLOAD_ERR_INI_SIZE:
			$message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
			break;
		case UPLOAD_ERR_FORM_SIZE:
			$message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
			break;
		case UPLOAD_ERR_PARTIAL:
			$message = "The uploaded file was only partially uploaded";
			break;
		case UPLOAD_ERR_NO_FILE:
			$message = "No file was uploaded";
			break;
		case UPLOAD_ERR_NO_TMP_DIR:
			$message = "Missing a temporary folder";
			break;
		case UPLOAD_ERR_CANT_WRITE:
			$message = "Failed to write file to disk";
			break;
		case UPLOAD_ERR_EXTENSION:
			$message = "File upload stopped by extension";
			break;

		default:
			$message = "Unknown upload error";
			break;
	}
	return $message;
}


function image_resize($src, $dst, $width, $height, $crop=0){
	if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

	$type = strtolower(substr(strrchr($src,"."),1));
	if($type == 'jpeg') $type = 'jpg';
	switch($type){
		case 'bmp': $img = imagecreatefromwbmp($src); break;
		case 'gif': $img = imagecreatefromgif($src); break;
		case 'jpg': $img = imagecreatefromjpeg($src); break;
		case 'png': $img = imagecreatefrompng($src); break;
		default : return "Unsupported picture type!";
	}

	// resize
	if($crop){
		if($w < $width || $h < $height) return "Picture is too small!";
		$ratio = max($width/$w, $height/$h);
		$h = $height / $ratio;
		$x = ($w - $width / $ratio) / 2;
		$w = $width / $ratio;
	}
	else{
		if($w < $width && $h < $height) return "Picture is too small!";
		$ratio = min($width/$w, $height/$h);
		$width = $w * $ratio;
		$height = $h * $ratio;
		$x = 0;
	}

	$new = imagecreatetruecolor($width, $height);
	// preserve transparency
	if($type == "gif" || $type == "png"){
		imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
		imagealphablending($new, false);
		imagesavealpha($new, true);
	}

	imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

	switch($type){
		case 'bmp': imagewbmp($new, $dst); break;
		case 'gif': imagegif($new, $dst); break;
		case 'jpg': imagejpeg($new, $dst); break;
		case 'png': imagepng($new, $dst); break;
	}
	
	//Free memory
	imagedestroy($new);
	return true;
}



$mega = 1.5;

//controllo caricamento
if (is_uploaded_file($_FILES['f']['tmp_name'])){

	//posizione dell'estensione nell'array extensions
	$estensione = mb_strtolower(end(explode(".", $_FILES['f']["name"])),'UTF-8');
	$pos = array_search($estensione,$extensions);//posizione == tipo file
	if ($pos===false) exit('{"error":"file type not allowed ('.$estensione.')"}');
	
	//controllo file con MIME-type
	if (!in_array($_FILES["f"]["type"],$application[$pos])) exit('{"error":"Tipo file non permesso"}');
	
	//controllo dimensione file
	if ($_FILES['f']["size"] > $mega*1048576) exit('{"error":"Dimensioni eccessive"}');
	
	//eventuali errori
	if ($_FILES['f']["error"]>0){
		$error = array("error" => decodeFileError($_FILES['f']["error"]));
		exit(json_encode($error));
	}
}
else exit('{"error":"file non caricato"}');

//exit('{"ok":"ok"}');

//*******************







$pic_type = strtolower(strrchr($_FILES['f']['name'],"."));
$pic_name = "./temp/temp".$pic_type;
if (!move_uploaded_file($_FILES['f']['tmp_name'], $pic_name)) exit('{"error":"Impossibile muovere il file immagine"}');

$pic_error = image_resize($pic_name, "../../IMAGES/account/allpeoples/user".$_SESSION['user_id'].$pic_type, 224, 224, 1);
if ($pic_error !== true) {
	unlink($pic_name);
	exit('{"error":"'.$pic_error.'"}');
}
else{
	unlink($pic_name);
	exit('{"ok":"file caricato"}');
}



/*

// Output
$output_path = NULL; //direct stream
if (isset($_GET['write'])) $output_path = './test-GD-image/resized_t-'.$test.'_r-'.$res.'_p-'.$percent.'.jpg'; //write to file
imagejpeg($thumb,$output_path,$res);

//Free memory
imagedestroy($thumb);
*/
?>
