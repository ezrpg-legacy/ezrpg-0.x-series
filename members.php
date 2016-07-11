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

$limit = (!$_GET['limit'])?30:intval($_GET['limit']); //Use user-selected limit of players to list
$begin = (!$_GET['begin'])?$player->id-intval($limit / 2):intval($_GET['begin']); //List players with the current player in the middle of the list
$begin = ($begin < 0)?0:$begin; //Can't list negative players :)

$total_players = $db->getone("select count(ID) as `count` from `players`");

$begin = ($begin >= $total_players)?$total_players - $limit:$begin; //Can't list players don't don't exist yet either
$begin = ($begin < 0)?0:$begin; //Can't list negative players :)

$lastpage = (($total_players - $limit) < 0)?0:$total_players - $limit; //Get the starting point if the user has browsed to the last page

include("templates/private_header.php");
?>

<a href="members.php?begin=<?=($begin - $limit)?>&limit=<?=$limit?>">Previous Page</a> | <a href="members.php?begin=<?=($begin + $limit)?>&limit=<?=$limit?>">Next Page</a>
<br /><br />
Show <a href="members.php?begin=<?=$begin?>&limit=5">5</a> | <a href="members.php?begin=<?=$begin?>&limit=10">10</a>  | <a href="members.php?begin=<?=$begin?>&limit=20">20</a> | <a href="members.php?begin=<?=$begin?>&limit=30">30</a> | <a href="members.php?begin=<?=$begin?>&limit=40">40</a> | <a href="members.php?begin=<?=$begin?>&limit=50">50</a> | <a href="members.php?begin=<?=$begin?>&limit=100">100</a> members per page

<br /><br /><br />

<table width="100%" border="0">
<tr>
<th width="30%"><b>Username</b></td>
<th width="30%"><b>Level</b></td>
<th width="40%"><b>Actions</b></td>
</tr>
<?php
//Select all members ordered by level (highest first, members table also doubles as rankings table)
$query = $db->execute("select `id`, `username`, `level` from `players` order by `level` desc limit ?,?", array($begin, $limit));

while($member = $query->fetchrow())
{
	echo "<tr>\n";
	echo "<td><a href=\"profile.php?id=" . $member['username'] . "\">";
	echo ($member['username'] == $player->username)?"<b>":"";
	echo $member['username'];
	echo ($member['username'] == $player->username)?"</b>":"";
	echo "</a></td>\n";
	echo "<td>" . $member['level'] . "</td>\n";
	echo "<td><a href=\"mail.php?act=compose&to=" . $member['username'] . "\">Mail</a> | <a href=\"battle.php?act=attack&username=" . $member['username'] . "\">Battle</a></td>\n";
	echo "</tr>\n";
}
?>
</table>

<?php
include("templates/private_footer.php");
?>