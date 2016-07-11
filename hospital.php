<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include("lib.php");
define("PAGENAME", "Hospital");
$player = check_user($secret_key, $db);

if ($player->hp == $player->maxhp)
{
	include("templates/private_header.php");
	echo "<b>Nurse:</b><br />\n";
	echo "<i>You are at full health! You don't need to be healed.</i>\n";
	include("templates/private_footer.php");
	exit;
}
else
{
	//Add possibility of PARTIAL healing in next version?
	
	$heal = $player->maxhp - $player->hp;
	$cost = $heal * 1; //Replace 0 with variable from settings table/file
	
	if ($_GET['act'])
	{
		if ($player->gold < $cost)
		{
			include("templates/private_header.php");
			echo "<b>Nurse:</b><br />\n";
			echo "<i><font color=\"red\">You don't have enough gold!</i>\n";
			include("templates/private_footer.php");
			exit;
		}
		else
		{
			$query = $db->execute("update `players` set `gold`=?, `hp`=? where `id`=?", array($player->gold - $cost, $player->maxhp, $player->id));
			$player = check_user($secret_key, $db); //Get new stats
			include("templates/private_header.php");
			echo "<b>Nurse:</b><br />\n";
			echo "<i>You have completely healed yourself!</i>\n";
			include("templates/private_footer.php");
			exit;
		}
	}
	
	include("templates/private_header.php");
	//Add option to change price of hospital (life to heal * set number chosen by GM in admin panel)
?>
<b>Nurse:</b><br />
<i>To completely heal yourself, it will cost <b><?=$heal?></b> Gold.</i>
<br />
<a href="hospital.php?act=heal">Heal!</a>
<?php
	include("templates/private_footer.php");
	exit;
}
?>