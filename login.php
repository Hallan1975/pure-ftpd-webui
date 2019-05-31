<?php
$master = "login.php";
include ("blocks/default.php");
include ("blocks/db_connect.php");
$username = null;
$password = null;
if (!isset($_SESSION)) { session_start(); }
$_SESSION["authenticated"] = 'false';

$get_system_language = FALSE;
$get_system_language = mysqli_query($db,"SELECT * FROM settings WHERE name='language';");
if (!$get_system_language) {
	if (($err = mysqli_errno($db)) == 1054) {
		$_SESSION["erro"] = "<p align=\"center\" class=\"table_error\">Your version of Pure-FTPd WebUI users table is not currently supported by current version, please upgrade your database to use miltilanguage support.</p>";
	}
	$language = "english";
	include("lang/english.php");
}
else {
	$language_row = mysqli_fetch_array ($get_system_language, MYSQLI_ASSOC);
	$language = $language_row['value'];
	if ($language == '') {
		$language = "english";
	}
	include("lang/$language.php");
}

if (!file_exists("lang/$language.php")) {
  $_SESSION["erro"] = "<p align=\"center\" class=\"table_error\">SYSTEM Language file not found ! \"$language\"</p>";	
}

if ( isset($_SESSION["timeout"]) && ($_SESSION["timeout"] > time()) ) {
  unset($_SESSION['username']);
  unset($_SESSION["timeout"]);
  unset($_SESSION["authenticated"]);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(!empty($_POST["username"]) && !empty($_POST["password"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
		
        $query = "SELECT pass FROM userlist WHERE user='".$username."'";
        $lst = mysqli_query($db,$query);
	if (mysqli_num_rows($lst) > 0) {
            $pass = mysqli_fetch_array($lst);
	    $password = md5($password);
    	    if ($password == $pass['pass'])
		{
        	$_SESSION["authenticated"] = 'true';
			$_SESSION["erro"] = '';
			$_SESSION["username"] = $username;
			unset($_SESSION["erro"]);
			if ( isset($session) && (is_numeric($session)) && ($session > 0) ){
				$_SESSION["timeout"] = time() + (60 * $session);
			} else {
				$_SESSION["timeout"] = time() + (60 * 5);
				$_SESSION["erro"] = $settings_session_invalid;
			}
        	header('Location: index.php');
        	exit;
    	    }
    	    else {
        	$_SESSION["erro"] = $log_errop;
    	    }
        } else {
    	    $_SESSION["erro"] = $log_errou;        
        }
    }
    header('Location: login.php');
} else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<HEAD>
	<title><?php echo($menu_title." - ".$log_title);?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel='shortcut icon' href='img/favicon.ico' />
	<link href="media/css/stile.css" rel="StyleSheet" type="text/css">
	<link href="media/css/demo_page.css" rel="StyleSheet" type="text/css">
	<link href="media/css/demo_table_jui.css" rel="StyleSheet" type="text/css">
	<link href="media/css/jquery-ui-1.7.2.custom.css" rel="StyleSheet" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script type="text/javascript" language="javascript" src="media/js/password.js"></script>
</head>
<body id="dt_example" class="ex_highlight_row"  onLoad="document.getElementById('username').focus();">
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="main_border">
  <tbody>
  <tr>
    <td class="header" width="100%" height="100"><p align="right" class="user"><?php echo($log_login);?></p></td>
  </tr>
  
  <tr>
      <td><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td valign="top">
			<div id="page">
				<header id="banner">
					<hgroup>
						<h1>> <?php echo($log_login);?></h1>
					</hgroup>        
				</header>
                <section id="content">
                    <form id="login" method="post">
						<table border="0" align="center" cellpadding="0" cellspacing="50">
						<tr><td>
						<table>
						<tr>
                         <td><label for="username"><?php echo($log_username);?></label></td>
						 <td><input id="username" name="username" type="text" required style="height:15px; width:170px" placeholder="<?php echo($log_username);?>"> </td> 
						</tr><tr>
                          <td><label for="password"><?php echo($log_password);?></label></td>
                          <td>
						    <input id="password" name="password" type="password" required style="height:15px; width:170px" placeholder='<?PHP echo($log_password);?>'>
							<i id='pass-status' class='fa fa-eye' aria-hidden='true' onClick='viewPassword("password","pass-status")'></i>
						  </td>
						 </tr>
						<tr><td></td><td align="center"><input type="submit" value="Login" style="height:25px; width:170px"></td></tr>
						</table>
                    </form>
                </section>
			</div>
			</td>
		</tr>
		</td>
	</tr>
	<center><p style="color:red"><b><?php if (!empty($_SESSION["erro"])) { echo($_SESSION["erro"]); } ?></b></p></center>
	<?php include("blocks/footer.php"); ?>
	</tbody>
</table>
</body>
</html>
<?php } ?>
