<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include("lib.php");
define("PAGENAME", "Log");
$player = check_user($secret_key, $db);

if ($_GET['act'] == "clear")
{
	//Clear all log messages for current user
	$query = $db->execute("delete from `user_log` where `player_id`=?", array($player->id));
}

//Get all log messages ordered by status
$query = $db->execute("select `msg`, `status` from `user_log` where `player_id`=? order by `status` desc", array($player->id));

//Update the status of the messages because now they have been read
$query2 = $db->execute("update `user_log` set `status`='read' where `player_id`=? and `status`='unread'", array($player->id));

include("templates/private_header.php");

if ($query->recordcount() > 0)
{
	echo "<a href=\"log.php?act=clear\">Clear log</a>";
	while ($log = $query->fetchrow())
	{
		echo "<fieldset>\n";
		echo "<legend>";
		echo ($log['status']=="unread")?"<b>" . ucwords($log['status']) . "</b>":ucwords($log['status']);
		echo "</legend>\n";
		echo $log['msg'] . "\n";
		echo "</fieldset>\n<br />\n";
	}
}
else
{
	echo "No log messages!";
}

include("templates/private_footer.php");
?>