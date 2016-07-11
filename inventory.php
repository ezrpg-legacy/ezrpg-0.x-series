<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include("lib.php");
define("PAGENAME", "Inventory");
$player = check_user($secret_key, $db);


if ($_GET['id'])
{
	$query = $db->execute("select `status`, `item_id` from `items` where `id`=? and `player_id`=?", array($_GET['id'], $player->id));
	if ($query->recordcount() == 1)
	{
		$item = $query->fetchrow();
		switch($item['status'])
		{
			case "unequipped": //User wants to equip item
				//$itemtype = $db->getone("select `type` from `blueprint_items` where `id`=?", array($item['item_id']));
				
				//Check if another item is already equipped
				$unequip = $db->getone("select items.id from `items`, `blueprint_items` where items.item_id = blueprint_items.id and blueprint_items.type=(select `type` from `blueprint_items` where `id`=?) and items.player_id=? and `status`='equipped'", array($item['item_id'], $player->id));
				if ($unequip) //If so, then unequip it (only one item may be equipped at any one time)
				{
					$query = $db->execute("update `items` set `status`='unequipped' where `id`=?", array($unequip));
				}
				//Equip the selected item
				$query = $db->execute("update `items` set `status`='equipped' where `id`=?", array($_GET['id']));
				break;
			case "equipped": //User wants to unequip item
				$query = $db->execute("update `items` set `status`='unequipped' where `id`=?", array($_GET['id']));
				break;
			default: //Set status to unequipped, in case the item had no status when it was inserted into db
				$query = $db->execute("update `items` set `status`='unequipped' where `id`=?", array($_GET['id']));
				break;
		}
	}
}


include("templates/private_header.php");
?>

<b>Weapons:</b>
<br />
<?php
$query = $db->execute("select items.id, items.item_id, items.status, blueprint_items.type, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.description from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='weapon' order by items.status asc", array($player->id));
if ($query->recordcount() == 0)
{
	echo "<br /><b>You have no weapons.</b>";
}
else
{
	while($item = $query->fetchrow())
	{
		echo "<fieldset>\n<legend>";
		echo "<b>" . $item['name'] . "</b></legend>\n";
		echo "<table width=\"100%\">\n";
		echo "<tr><td width=\"85%\">";
		echo $item['description'] . "\n<br />";
		echo "<b>Effectiveness:</b> " . $item['effectiveness'] . "\n";
		echo "</td><td width=\"15%\">";
		echo "<a href=\"shop.php?act=sell&id=" . $item['id'] . "\">Sell</a><br />";
		echo "<a href=\"inventory.php?id=" . $item['id'] . "\">";
		echo ($item['status'] == "equipped")?"Unequip":"Equip";
		echo "</a>";
		echo "</td></tr>\n";
		echo "</table>";
		echo "</fieldset>\n<br />";
	}
}
?>
<br /><br />
<br />

<b>Armour:</b>
<br />
<?php
$query = $db->execute("select items.id, items.item_id, items.status, blueprint_items.type, blueprint_items.name, blueprint_items.effectiveness, blueprint_items.description from `items`, `blueprint_items` where blueprint_items.id=items.item_id and items.player_id=? and blueprint_items.type='armour' order by items.status asc", array($player->id));
if ($query->recordcount() == 0)
{
	echo "<br /><b>You have no armour.</b>";
}
else
{
	while($item = $query->fetchrow())
	{
		echo "<fieldset>\n<legend>";
		echo "<b>" . $item['name'] . "</b></legend>\n";
		echo "<table width=\"100%\">\n";
		echo "<tr><td width=\"85%\">";
		echo $item['description'] . "\n<br />";
		echo "<b>Effectiveness:</b> " . $item['effectiveness'] . "\n";
		echo "</td><td width=\"15%\">";
		echo "<a href=\"shop.php?act=sell&id=" . $item['id'] . "\">Sell</a><br />";
		echo "<a href=\"inventory.php?id=" . $item['id'] . "\">";
		echo ($item['status'] == "equipped")?"Unequip":"Equip";
		echo "</a>";
		echo "</td></tr>\n";
		echo "</table>";
		echo "</fieldset>\n<br />";
	}
}
?>
<br /><br />

<?php
include("templates/private_footer.php");
?>