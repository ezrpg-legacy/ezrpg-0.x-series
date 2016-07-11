<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include("lib.php");
define("PAGENAME", "Stat Points");
$player = check_user($secret_key, $db);

if ($player->stat_points > 0)
{
	switch($_GET['act'])
	{
		case '0':
			$query = $db->execute("update `players` set `stat_points`=?, `strength`=? where `id`=?", array($player->stat_points - 1, $player->strength + 1, $player->id));
			if ($query)
			{
				$player = check_user($secret_key, $db); //Get new stats
				$msg = "<b>You have increased your strength! It is now at " . $player->strength . ".</b><br /><br />";
			}
			else
			{
				//Error, insert error into log
			}
			break;
		case '1':
			//Add to vitality, and update health
			//Health increase should be able to be changed from admin panel
			$query = $db->execute("update `players` set `stat_points`=?, `vitality`=?, `maxhp`=? where `id`=?", array($player->stat_points - 1, $player->vitality + 1, $player->maxhp + 20, $player->id));
			if ($query)
			{
				$player = check_user($secret_key, $db); //Get new stats
				$msg = "<b>You have increased your vitality! It is now at " . $player->vitality . ".</b><br /><br />";
			}
			else
			{
				//Error, insert error into log
			}
			break;
		case '2':
			$query = $db->execute("update `players` set `stat_points`=?, `agility`=? where `id`=?", array($player->stat_points - 1, $player->agility + 1, $player->id));
			if ($query)
			{
				$player = check_user($secret_key, $db); //Get new stats
				$msg = "<b>You have increased your agility! It is now at " . $player->agility . ".</b><br /><br />";
			}
			else
			{
				//Error, insert error into log
			}
			break;

	}
}

include("templates/private_header.php");

echo $msg;

if ($player->stat_points == 0)
{
?>
<b>Stat Trainer:</b><br />
<i>Sorry sir, but you currently do not have any stat points to spend.<br />
Please come back later when you have leveled up.</i>
<?php
}
else
{
?>
<b>Stat Trainer:</b><br />
<i>You have <?=$player->stat_points?> stat points to spend, sir. What would you like to spend them on?</i>
<br /><br />
<a href="stat_points.php?act=0">Strength</a><br />
<a href="stat_points.php?act=1">Vitality</a><br />
<a href="stat_points.php?act=2">Agility</a><br />
<?php
}
include("templates/private_footer.php");
?>