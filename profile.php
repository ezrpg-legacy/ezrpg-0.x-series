<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include("lib.php");
define("PAGENAME", "Profile");
$player = check_user($secret_key, $db);

//Check for user ID
if (!$_GET['id'])
{
	header("Location: members.php");
}
else
{
	$query = $db->execute("select `id`, `username`, `registered`, `level`, `kills`, `deaths`, `hp` from `players` where `username`=?", array($_GET['id']));
	if ($query->recordcount() == 0)
	{
		header("Location: members.php");
	}
	else
	{
		$profile = $query->fetchrow();
	}
}

include("templates/private_header.php");
?>


<fieldset>
<legend><?=$profile['username']?>'s Profile</legend>
<table width="90%">
<tr>
<td width="50%">Username:</td>
<td width="50%"><?=$profile['username']?> (<a href="mail.php?act=compose&to=<?=$profile['username']?>">Mail</a>)</td>
</tr>
<tr>
<td>Level:</td>
<td><?=$profile['level']?></td>
</tr>
<tr><td></td></tr>
<tr>
<td>Status:</td>
<td><font color="<?=($profile['hp']==0)?"red\">Dead":"green\">Alive"?></font></td>
</tr>
<tr>
<td>Kills:</td>
<td><?=$profile['kills']?></td>
</tr>
<tr>
<td>Deaths:</td>
<td><?=$profile['deaths']?></td>
</tr>
<tr><td></td></tr>
<tr>
<td>Registered:</td>
<td><?=date("F j, Y, g:i a", $profile['registered'])?></td>
</tr>
<tr>
<td>In-Game Age:</td>
<?php
$diff = time() - $profile['registered'];
$age = intval(($diff / 3600) / 24);
?>
<td><?=$age?> days</td>
</tr>
</table>
<br /><br />
<center>
<a href="battle.php?act=attack&username=<?=$profile['username']?>">Battle</a>
</center>
</fieldset>


<?php
include("templates/private_footer.php");
?>