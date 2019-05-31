<?php
$master = "index.php";
include ("blocks/lock.php");
include ("blocks/default.php");
$user = '';
if(isset($_SESSION['username'])) { $user = $_SESSION['username']; }
include ("blocks/db_connect.php");
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
	$language_row = mysqli_fetch_array ($get_user_language, MYSQLI_ASSOC);
	$language = $language_row['language'];
	if ($language == '') {
		$language = "english";
	}
	include("lang/$language.php");
}

if (!file_exists("lang/$language.php")) {
  $info = $info."<p align=\"center\" class=\"table_error\">USER Language file not found ! \"$language\"</p>";	
}

if (isset($_SESSION["erro"])) {
  $info = $info."<p align=\"center\" class=\"table_error\">".$_SESSION["erro"]."</p>";	
  $_SESSION["erro"] = ''; 
  unset($_SESSION["erro"]);
}

echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"");
echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">");
echo("<HTML xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"en-US\" xml:lang=\"en-US\">");
echo("<HEAD>");
echo("<title>$menu_title - $ua_title</title>");
echo("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />");
echo("<meta http-equiv='refresh' content='30'/>");

if(isset($_GET["LOGOFF"])) {
//  session_start();
  unset($_SESSION['username']);
  unset($_SESSION['timeout']);
  unset($_SESSION["authenticated"]);
  unset($_SESSION);
  session_unset();
  session_destroy();
  if(!isset($_SESSION['username'])) header("Location: index.php");
  die();
}

?>
<link rel='shortcut icon' href='img/favicon.ico' />
<link href="media/css/stile.css" rel="StyleSheet" type="text/css">
<link href="media/css/demo_page.css" rel="StyleSheet" type="text/css">
<link href="media/css/demo_table_jui.css" rel="StyleSheet" type="text/css">
<link href="media/css/jquery-ui-1.7.2.custom.css" rel="StyleSheet" type="text/css">
<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="media/js/jquery.dataTables.js"></script>
<?php echo("
<script type=\"text/javascript\" charset=\"utf-8\">
			$(document).ready(function() {
				$('#example').dataTable( {
					\"oLanguage\": {
						\"sUrl\": \"media/dataTables.$language.txt\"
					},
					\"bJQueryUI\": true,
					\"sPaginationType\": \"full_numbers\",
					\"bSort\": true,
					\"bFilter\": true
				} );
			} );
		</script> ");?>
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
</table></br><?php echo("$info"); ?></br>
	<?php echo("<p class=\"text_title\" align=\"center\">$ua_t_title</p>"); ?>

	<div id="container">

	<?php
		echo("<div class='demo_jui'>");

		echo("<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"display\" id=\"example\">
				<thead>
					<tr>
						<th>$ua_t_th0</th>
						<th>$ua_t_th1</th>
						<th>$ua_t_th2</th>
						<th>$ua_t_th6</th>
						<th>$ua_t_th3</th>
						<th>$ua_t_th4</th>
						<th>$ua_t_th5</th>
						<th>$ua_t_th7</th>
						<th>$ua_t_th8</th>
						<th>&nbsp</th>
					</tr>
				</thead><tbody>");

		// Активные пользователи
		$result = shell_exec("sudo $pureftpwho_path -s");
		$array = explode("\n", $result);
		foreach ($array as $users) {
		if ( !empty($users) ) {
			list ($pid, $login, $time, $stat, $file, $peer, $local, $port, $current, $total, $porc, $band ) = explode("|", $users);
		}
		if ( !empty($pid) ) {

        $secondsInAMinute = 60;
        $secondsInAnHour  = 60 * $secondsInAMinute;
        $secondsInADay    = 24 * $secondsInAnHour;

        // extract days
        $days = floor($time / $secondsInADay);

        // extract hours
        $hourSeconds = $time % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        // extract minutes
        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        // extract the remaining seconds
        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);
		
		$ctime="";
		if ( (int) $days > 0     ) { $ctime=(int) $days."d "; }
		if ( (int) $hours < 10    ) { $ctime=$ctime."0"; }
		$ctime=$ctime.(int) $hours.":";
		if ( (int) $minutes < 10 ) { $ctime=$ctime."0"; }
		$ctime=$ctime.(int) $minutes.":";
		if ( (int) $seconds < 10 ) { $ctime=$ctime."0"; }
		$ctime=$ctime.(int) $seconds;
	
		$uns = " Kb/s";
		if ( $band > 1024 ) { $band = round($band / 1024,2); $uns = " Mb/s"; }
		if ( $band > 1024 ) { $band = round($band / 1024,2); $uns = " Gb/s"; }
		if ( $band > 1024 ) { $band = round($band / 1024,2); $uns = " Tb/s"; }

		$unc = " Kb";
		if ( $current > 1024 ) { $current = round($current / 1024,2); $unc = " Mb"; }
		if ( $current > 1024 ) { $current = round($current / 1024,2); $unc = " Gb"; }
		if ( $current > 1024 ) { $current = round($current / 1024,2); $unc = " Tb"; }

		$unt = " Kb";
		if ( $total > 1024 ) { $total = round($total / 1024,2); $unt = " Mb"; }
		if ( $total > 1024 ) { $total = round($total / 1024,2); $unt = " Gb"; }
		if ( $total > 1024 ) { $total = round($total / 1024,2); $unt = " Tb"; }
		
		echo("		<tr>
						<td class='center'>$pid</td>
						<td class='center'>$ctime</td>
						<td class='center'>$login</td>
						<td class='center'>$peer</td>
						<td class='center'>$band$uns</td>
						<td class='center'>$stat</td>
						<td>$file</td>
						<td class='center'>$current$unc</td>
						<td class='center'>$total$unt</td>
						<td class='center'>$porc%</td>
					</tr>");
		}
		}
		echo("	</tbody></div>");

	?>

	</div>

	</table>
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