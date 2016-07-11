<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include("lib.php");
define("PAGENAME", "Home");

//Begin checking if user has tried to login
$error = 0; //Error count
$errormsg = "<font color=\"red\">"; //Error message to be displayed in case of error (modified below depending on error)
if ($_POST['login'])
{
	if ($_POST['username'] == "")
	{
		$errormsg .= "Please enter a username!";
		$error = 1;
	}
	else if ($_POST['password'] == "")
	{
		$errormsg .= "Please enter your password!";
		$error = 1;
	}
	else if ($error == 0)
	{
		$query = $db->execute("select `id`, `username` from `players` where `username`=? and `password`=?", array($_POST['username'], sha1($_POST['password'])));
		if ($query->recordcount() == 0)
		{
			$errormsg .= "You could not login! Please check your username/password!";
			$error = 1;
		}
		else
		{
			$player = $query->fetchrow();
			$query = $db->execute("update `players` set `last_active`=? where `id`=?", array(time(), $player['id']));
			$hash = sha1($player['id'] . $_SERVER['REMOTE_ADDR'] . $secret_key);
			$_SESSION['userid'] = $player['id'];
			$_SESSION['hash'] = $hash;
			header("Location: home.php");
		}
	}
}
$errormsg .= "</font>";


include("templates/header.php");
?>

<table width="100%" border="0">
<tr>
<td width="60%">
<!-- Put your game description here! -->
Welcome to ezRPG!
Login now to play, or <a href="register.php">Register</a> to join the game!
<br /><br />
<i>Edit index.php to change this text and introduce your game.</i>
</td>
<td width="40%">
Login:<br />
<?=($error==1)?$errormsg:""?>
<form method="POST" action="index.php">
Username: <input type="text" name="username" value="<?=$_POST['username']?>" /><br />
Password: <input type="password" name="password" /><br />
<input name="login" type="submit" value="Login!" />
</form>
</td>
</tr>
</table>

<?php
include("templates/footer.php");
?>