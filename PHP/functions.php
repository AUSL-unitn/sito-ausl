<?php
/* **********************************************************************************
										Securely start a PHP session.
It important not to just put "session_start();" on the top of every page that you want to use php sessions, If you're really concerned about security then this is how you should do it.
We are going to create a function called "sec_session_start", this will start a php session in a secure way. You should call this function at the top of any page you wish to access a php session variable.
If you are really concerned about security and the privacy of your cookies, have a look at this article:
http://www.wikihow.com/Create-a-Secure-Session-Managment-System-in-Php-and-Mysql.
*/

function sec_session_start($regen=false) {
	ini_set('session.gc_maxlifetime', 604800);
	$session_name = 'sec_session_id'; // Set a custom session name
	$secure = false; // Set to true if using https.
	$httponly = true; // This stops javascript being able to access the session id.
	
	ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.
	$cookieParams = session_get_cookie_params(); // Gets current cookies params.
	session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
	session_name($session_name); // Sets the session name to the one set above.
	session_start(); // Start the php session
	if ($regen) session_regenerate_id(true); // regenerated the session, delete the old one. 
}







/* **********************************************************************************
											Create Login Function.
This function will check the username and password against the database, it will return true if there is a match.
*/

function login($email, $password, $mysqli) {
	// Using prepared Statements means that SQL injection is not possible. 
	if ($stmt = $mysqli->prepare("SELECT id, nome, password, salt, preferences FROM users WHERE email = ? LIMIT 1")) {
		$stmt->bind_param('s', $email); // Bind "$username" to parameter. i=integer, d=double, s=string, b=blob and will be sent in packets
		$stmt->execute(); // Execute the prepared query.
		$stmt->store_result();
		$stmt->bind_result($user_id, $username, $db_password, $salt, $prefer); // get variables from result.
		$stmt->fetch();
		$password = hash('sha512', $password.$salt); // hash the password with the unique salt.
 
		if($stmt->num_rows == 1) { // If the user exists
			if($db_password == $password) { // Check if the password in the database matches the password the user submitted. 
				// Password is correct!
				$user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.
				$user_id = preg_replace("/[^0-9]+/", "", $user_id); // XSS protection as we might print this value
				$_SESSION['user_id'] = $user_id;
				//$username = preg_replace("/[^a-zA-Z0-9_\- ]+/", "", $username); // XSS protection as we might print this value
				$_SESSION['username'] = utf8_encode($username);
				$_SESSION['login_string'] = hash('sha512', $password.$user_browser);
				
				$_SESSION['prefer'] = $prefer;
				// Login successful.
				$stmt->close();
				if ($mysqli->query("UPDATE users SET lastLog = ".time()." WHERE id = ".$user_id)) return true;
				else return false;
			} else {
				// Password is not correct
				return false;
			}
		} else {
			// No user exists. 
			return false;
		}
	}
	else return false;
}


/* **********************************************************************************
											Check logged in status.
We do this by checking the "user_id" and the "login_string" SESSION variables. The "login_string" SESSION variable has the users Browser Info hashed together with the password. We use the Browser Info because it is very unlikely that the user will change their browser mid-session. Doing this helps prevent session hijacking
*/
function is_logged($mysqli) {
	// Check if all session variables are set
	if(isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
		$user_id = $_SESSION['user_id'];
		$login_string = $_SESSION['login_string'];

		$user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.

		if ($stmt = $mysqli->prepare("SELECT password FROM users WHERE id = ? LIMIT 1")) {
			$stmt->bind_param('i', $user_id); // Bind "$user_id" to parameter.
			$stmt->execute(); // Execute the prepared query.
			$stmt->store_result();

			if($stmt->num_rows == 1) { // If the user exists
				$stmt->bind_result($password); // get variables from result.
				$stmt->fetch();
				$login_check = hash('sha512', $password.$user_browser);
				if($login_check == $login_string) {
					// Logged In!!!!
					return true;
				} else {
					// Not logged in
					return false;
				}
			} else {
				// Not logged in * no user exist
				return false;
			}
		} else {
			// Not logged in * error preparing query
			return false;
		}
	} else {
		// Not logged in * no session active
		return false;
	}
}

?>
