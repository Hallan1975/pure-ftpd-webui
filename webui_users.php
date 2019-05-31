<?php
$master = "webui_users.php";
include ("blocks/lock.php");
include ("blocks/default.php");
$user = '';
if(isset($_SESSION['username'])) { $user = $_SESSION['username']; }
include ("blocks/db_connect.php"); /*Подлкючаемся к базе*/
$info = '';
$get_user_language = FALSE;
$get_user_language = mysqli_query($db,"SELECT language FROM userlist WHERE user='$user';");
if (!$get_user_language) {
	if (($err = mysqli_errno($db)) == 1054) {
		$info = "<p align=\"center\" class=\"table_error\">Your version of Pure-FTPd WebUI users table is not currently supported by current version, please upgrade your database to use miltilanguage support.</p>";
	}
	$language = "english";
	include("lang/english.php");
}
else {
	$language_row = mysqli_fetch_array ($get_user_language);
	$language = $language_row['language'];
	if ($language == '') {
		$language = "english";
	}
	include("lang/$language.php");
}

echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"");
echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">");
echo("<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"en-US\" xml:lang=\"en-US\">");
echo("<head>");
echo("<title>$menu_title - $wu_title</title>");
echo("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />");
?>
<link rel='shortcut icon' href='img/favicon.ico' />
<link href="media/css/stile.css" rel="StyleSheet" type="text/css">
<link href="media/css/demo_page.css" rel="StyleSheet" type="text/css">
<link href="media/css/demo_table_jui.css" rel="StyleSheet" type="text/css">
<link href="media/css/jquery-ui-1.7.2.custom.css" rel="StyleSheet" type="text/css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script type="text/javascript" language="javascript" src="media/js/password.js"></script>
</head>
<body id="dt_example" class="ex_highlight_row">
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="main_border">
  <tbody>
<?php include("blocks/header.php"); ?>
  <tr>
      <td><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
         <tr>
               <td valign="top">
<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <?php include("blocks/menu.php"); ?>
    </tr>
</table></br><?php echo("$info</br>");
			if (isset ($_POST['add'])) {
				echo("
					<form name=\"form1\" method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">
						<p>
							<label>$ewu_form_login</br>
							<input type=\"text\" name=\"user_add\" id=\"user_add\">
							</label>
						</p>
						<p>
							<label>$ewu_form_pwd</br>
							<input type=\"password\" name=\"password\" id=\"password\">
							<i id='pass-status' class='fa fa-eye' aria-hidden='true' onClick='viewPassword(\"password\",\"pass-status\")'></i>
							&nbsp
							<INPUT type='button'value='$um_generate' onClick='generate(\"password\",\"genpasssize\");'>
							<INPUT type='text' value='".$ftp_pass."' name='genpasssize' id='genpasssize' style='width: 20px;' '>
							</label>
						</p>
						<p>
							<label>$ewu_form_lang</br>
							<select name=\"language\">");
							$directory = "lang/";
							$languages = glob("" . $directory . "*.php");
							foreach($languages as $lang) {
								$rest = substr($lang, 5);
								$lng = substr($rest, 0, -4);
								if ($lng == $language ) {
								  echo("<option selected>$lng</option>");
								} else {
								  echo("<option>$lng</option>");
								}
							}
							echo("</label>
							</select>
						</p>
						<p>
							<label>
							<INPUT type=\"submit\" name=\"adduser\" value=\"$wu_saveuserbutton\">
							</label>
						</p>
					</form>");
			}
			elseif (isset ($_POST['adduser'])) {
				if (isset ($_POST['user_add'])) {$user_add = $_POST['user_add']; if ($user_add == '') {unset ($user_add);}}
				if (isset ($_POST['password'])) {$pass = $_POST['password']; if ($pass == '') {unset ($pass);}}
				if (isset ($_POST['language'])) {$language = $_POST['language']; if ($language == '') {unset ($language);}}
				if (isset ($user_add) && isset($pass) && isset($language)) {
					$result = mysqli_query ($db,"INSERT INTO userlist (user,pass,language) VALUES ('$user_add',md5('$pass'),'$language')");
					if ($result == 'true') {echo "<p><strong>$wu_add_resultok</strong></p>";}
					else {echo "<p><strong>$wu_add_resulterror</strong></p>";}
				}
				else {echo "<p><strong>$wu_add_checkfields</strong></p>";}

						echo "</br>
							<form name='to_list' method='post' action='" . $_SERVER['PHP_SELF'] . "'>
								<p>
									<label>
									<input type='submit' name='users' id='users' value='$wu_add_checkfieldsback'>
									</label>
								</p>
							</form>";
			}
			else {
				echo("<p class=\"text_title\">$wu_select</p>");
				echo("<form action=\"edit_webui_users.php\" method=\"post\">");
				$result = mysqli_query ($db,"SELECT user,id FROM userlist");
				$myrow = mysqli_fetch_array ($result);
				$id = $myrow["id"];
					do {
						printf ("<p><input name='id' type='radio' value='%s'><label> %s</label></p>", $myrow["id"], $myrow["user"]);
					}
				while ($myrow = mysqli_fetch_array ($result));
                echo("<p><input name=\"submit\" type=\"submit\" value=\"$wu_editbutton\">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                	<input type=\"submit\" name=\"delete\" value=\"$ewu_deluserbutton\" onclick=\"if(!confirm('Confirma Exclusao do usuario ?')){return false;}\"></p></form>");
                echo("<form name=\"to_list\" method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">
                	<p><input type=\"submit\" name=\"add\" value=\"$wu_adduserbutton\"></p></form>");
            } ?>
               </td>
            </tr>
          </table>
        </td>
       </tr>
<?php include("blocks/footer.php"); ?>
  </tbody>
</table>
</body>
</html>
