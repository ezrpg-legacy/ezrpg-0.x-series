<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include("lib.php");
define("PAGENAME", "Register");

$msg1 = "<font color=\"red\">"; //Username error?
$msg2 = "<font color=\"red\">"; //Password error?
$msg3 = "<font color=\"red\">"; //Verify Password error?
$msg4 = "<font color=\"red\">"; //Email error?
$msg5 = "<font color=\"red\">"; //Verify Email error?
$error = 0;

if ($_POST['register'])
{
	//Check if username has already been used
	$query = $db->execute("select `id` from `players` where `username`=?", array($_POST['username']));
	//Check username
	if (!$_POST['username']) { //If username isn't filled in...
		$msg1 .= "You need to fill in your username!<br />\n"; //Add to error message
		$error = 1; //Set error check
	}
	else if (strlen($_POST['username']) < 3)
	{ //If username is too short...
		$msg1 .= "Your username must be longer than 3 characters!<br />\n"; //Add to error message
		$error = 1; //Set error check
	}
	else if (!preg_match("/^[-_a-zA-Z0-9]+$/", $_POST['username']))
	{ //If username contains illegal characters...
		$msg1 .= "Your username may contain only alphanumerical characters!<br />\n"; //Add to error message
		$error = 1; //Set error check
	}
	else if ($query->recordcount() > 0)
	{
		$msg1 .= "That username has already been used. Please create only one account, Creating more than one account will get all your accounts deleted!<br />\n";
		$error = 1; //Set error check
	}

	//Check password
	if (!$_POST['password'])
	{ //If password isn't filled in...
		$msg2 .= "You need to fill in your password!<br />\n"; //Add to error message
		$error = 1; //Set error check
	}
	else if ($_POST['password'] != $_POST['password2'])
	{
		$msg3 .= "You didn't type in both passwords correctly!<br />\n";
		$error = 1;
	}
	else if (strlen($_POST['password']) < 3)
	{ //If password is too short...
		$msg2 .= "Your password must be longer than 3 characters!!<br />\n"; //Add to error message
		$error = 1; //Set error check
	}
	else if (!preg_match("/^[-_a-zA-Z0-9]+$/", $_POST['password']))
	{ //If password contains illegal characters...
		$msg2 .= "Your password may contain only alphanumerical characters!<br />\n"; //Add to error message
		$error = 1; //Set error check
	}
	
	//Check email
	if (!$_POST['email'])
	{ //If email address isn't filled in...
		$msg4 .= "You need to fill in your email!<br />\n"; //Add to error message
		$error = 1; //Set error check
	}
	else if ($_POST['email'] != $_POST['email2'])
	{
		$msg5 .= "You didn't type in both email address correctly!";
		$error = 1;
	}
	else if (strlen($_POST['email']) < 3)
	{ //If email is too short...
		$msg4 .= "Your email must be longer than 3 characters!<br />\n"; //Add to error message
		$error = 1; //Set error check
	}
	else if (!preg_match("/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+@([-0-9A-Z]+\.)+([0-9A-Z]){2,4}$/i", $_POST['email']))
	{
		$msg4 .= "Your email format is wrong!<br />\n"; //Add to error message
		$error = 1; //Set error check
	}
	else
	{
		//Check if email has already been used
		$query = $db->execute("select `id` from `players` where `email`=?", array($_POST['email']));
		if ($query->recordcount() > 0)
		{
			$msg4 .= "That email has already been used. Please create only one account, Creating more than one account will get all your accounts deleted!<br />\n";
			$error = 1; //Set error check
		}
	}
	
	
	if ($error == 0)
	{
		$insert['username'] = $_POST['username'];
		$insert['password'] = sha1($_POST['password']);
		$insert['email'] = $_POST['email'];
		$insert['registered'] = time();
		$insert['last_active'] = time();
		$insert['ip'] = $_SERVER['REMOTE_ADDR'];
		$query = $db->autoexecute('players', $insert, 'INSERT');
		
		
		if (!$query)
		{
			$could_not_register = "Sorry, you could not register! Please contact the admin!<br /><br />";
		}
		else
		{
			$insertid = $db->Insert_ID();
			
			include("templates/header.php");
			echo "Congratulations! You have successfully registered!<br />You may login to the game now.";
			include("templates/footer.php");
			exit;
		}
	}
}

$msg1 .= "</font>"; //Username error?
$msg2 .= "</font>"; //Password error?
$msg3 .= "</font>"; //Verify Password error?
$msg4 .= "</font>"; //Email error?
$msg5 .= "</font>"; //Verify Email error?

include("templates/header.php");

?>

<?=$could_not_register?>
<form method="POST" action="register.php">
<table width="100%">
<tr><td width="40%"><b>Username</b>:</td><td><input type="text" name="username" value="<?=$_POST['username'];?>" /></td></tr>
<tr><td colspan="2">Enter the username that you will use to login to your game. Only alpha-numerical characters are allowed.<br /><?=$msg1;?><br /></td></tr>

<tr><td width="40%"><b>Password</b>:</td><td><input type="password" name="password" value="<?=$_POST['password'];?>" /></td></tr>
<tr><td colspan="2">Type in your desired password. Only alpha-numerical characters are allowed.<br /><?=$msg2;?><br /></td></tr>

<tr><td width="40%"><b>Verify Password</b>:</td><td><input type="password" name="password2" value="<?=$_POST['password2'];?>" /></td></tr>
<tr><td colspan="2">Please re-type your password.<br /><?=$msg3;?><br /></td></tr>

<tr><td width="40%"><b>Email</b>:</td><td><input type="text" name="email" value="<?=$_POST['email'];?>" /></td></tr>
<tr><td colspan="2">Enter your email address. Only alpha-numerical characters are allowed.<br /><?=$msg4;?><br /></td></tr>

<tr><td width="40%"><b>Verify Email</b>:</td><td><input type="text" name="email2" value="<?=$_POST['email2'];?>" /></td></tr>
<tr><td colspan="2">Please re-type your email address.<br /><?=$msg5;?><br /></td></tr>

<tr><td colspan="2" align="center"><input type="submit" name="register" value="Register!"></td></tr>
</table>
</form>


<?php
include("templates/footer.php");
?>