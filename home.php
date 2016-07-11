<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include("lib.php");
define("PAGENAME", "Home");
$player = check_user($secret_key, $db);

include("templates/private_header.php");
?>

<table width="100%" border="0">
<tr>
<td width="50%">
<b>Username:</b> <?=$player->username?><br />
<b>Email:</b> <?=$player->email?><br />
<b>Registered:</b> <?=date("F j, Y, g:i a", $player->registered)?><br />
<?php
$diff = time() - $player->registered;
$age = intval(($diff / 3600) / 24);
?>
<b>Character Age:</b> <?=$age?> days<br />
<b>Kills/Deaths:</b> <?=$player->kills?>/<?=$player->deaths?><br />
<br />
<?php
if ($player->stat_points > 0)
{
	echo "<i>You have <b>" . $player->stat_points . "</b> stat points to spend!<br />";
	echo "<a href=\"stat_points.php\">Spend them here</a></i>";
}
else
{
	echo "<br /><i>Sorry, you have no stat points to spend!</i>";
}
?>
</td>
<td width="50%">
<b>Level:</b> <?=$player->level?><br />
<?php
$percent = intval(($player->exp / $player->maxexp) * 100);
?>
<b>EXP:</b> <?=$player->exp?>/<?=$player->maxexp?> (<?=$percent?>%)<br />
<b>HP:</b> <?=$player->hp?>/<?=$player->maxhp?><br />
<b>Energy:</b> <?=$player->energy?>/<?=$player->maxenergy?><br />
<b>Gold:</b><?=$player->gold?><br />
<br />
<b>Strength:</b> <?=$player->strength?><br />
<b>Vitality:</b> <?=$player->vitality?><br />
<b>Agility:</b><?=$player->agility?><br />
</td>
</tr>
</table>


<br /><br />

<!-- Here you can put news updates or whatever. A News System will be coming next version! -->

<br /><br />
<center><a href="http://code.google.com/p/ezrpg">ezRPG Project</a></center>
<!-- Yes, you may remove that link if you wish :) -->

<?php
include("templates/private_footer.php");
?>