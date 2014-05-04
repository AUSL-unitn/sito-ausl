<?php

/* **********************************************************************************
									 Create Database Connection Page.
This is the php code that we will use to connect to our mySQL database. Create a new
php file called "db_connect.php" and add the code below. You can then include the file
onto any page you wish to connect to the database.
*/

/*
define("HOST", "localhost"); // The host you want to connect to. [altervista: leave it empty or use "localhost"]
define("USER", "username"); // The database username. [altervista: leave it empty]
define("PASSWORD", "password"); // The database password. [altervista: leave it empty]
define("DATABASE", "database"); // The database name.
*/


if ($_SERVER['HTTP_HOST']=='www.ausl.altervista.org' || $_SERVER['HTTP_HOST']=='ausl.altervista.org'){
	// SOLO PER ALTERVISTA
	define("HOST", "localhost");
	define("USER", "");
	define("PASSWORD", "");
	define("DATABASE", "my_ausl");
}
else{
	// SOLO PER EASYPHP
	define("HOST", "127.0.0.1");
	define("USER", "root");
	define("PASSWORD", "");
	define("DATABASE", "ausl");
}

$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
// If you are connecting via TCP/IP rather than a UNIX socket remember to add the port number as a parameter.
?>
