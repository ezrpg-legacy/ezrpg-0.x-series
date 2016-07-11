<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include("lib.php");
define("PAGENAME", "Shop");
$player = check_user($secret_key, $db);

switch($_GET['act'])
{
	case "buy":
		if (!$_GET['id']) //No item ID
		{
			header("Location: shop.php");
			break;
		}
		
		//Select the item from the database
		$query = $db->execute("select `id`, `name`, `price` from `blueprint_items` where `id`=?", array($_GET['id']));
		
		//Invalid item (it doesn't exist)
		if ($query->recordcount() == 0)
		{
			header("Location: shop.php");
			break;
		}
		
		$item = $query->fetchrow();
		if ($item['price'] > $player->gold)
		{
			include("templates/private_header.php");
			echo "<b>Shop Keeper:</b><br />\n";
			echo "<i>Sorry, but you cannot afford this!</i><br /><br />\n";
			echo "<a href=\"inventory.php\">Return to inventory</a> | <a href=\"shop.php\">Return to shop</a>";
			include("templates/private_footer.php");
			break;
		}
		
		$query1 = $db->execute("update `players` set `gold`=? where `id`=?", array($player->gold - $item['price'], $player->id));
		$insert['player_id'] = $player->id;
		$insert['item_id'] = $item['id'];
		$query2 = $db->autoexecute('items', $insert, 'INSERT');
		if ($query1 && $query2) //If successful
		{
			$player = check_user($secret_key, $db); //Get new user stats
			
			include("templates/private_header.php");
			echo "<b>Shop Keeper:</b><br />\n";
			echo "<i>Thank you, enjoy your new <b>" . $item['name'] . "</b>!</i><br /><br />\n";
			echo "<a href=\"inventory.php\">Return to inventory</a> | <a href=\"shop.php\">Return to shop</a>";
			include("templates/private_footer.php");
			break;
		}
		else
		{
			//Error logging here
		}
		
		break;
		
	case "sell":
		if (!$_GET['id']) //No item ID
		{
			header("Location: shop.php");
			break;
		}
		
		//Select the item from the database
		$query = $db->execute("select items.id, blueprint_items.name, blueprint_items.price from `blueprint_items`, `items` where items.item_id=blueprint_items.id and items.player_id=? and items.id=?", array($player->id, $_GET['id']));
		
		//Either item doesn't exist, or item doesn't belong to user
		if ($query->recordcount() == 0)
		{
			include("templates/private_header.php");
			echo "Sorry, that item does not exist!";
			include("templates/private_footer.php");
			break;
		}
		
		$sell = $query->fetchrow(); //Get item info
		
		//Check to make sure clicking Sell wasn't an accident
		if (!$_POST['sure'])
		{
			include("templates/private_header.php");
			echo "Are you sure you want to sell your <b>" . $sell['name'] . "</b> for <b>" . floor($sell['price']/2) . "</b> gold?<br /><br />\n";
			echo "<form method=\"post\" action=\"shop.php?act=sell&id=" . $sell['id'] . "\">\n";
			echo "<input type=\"submit\" name=\"sure\" value=\"Yes, I am sure!\" />\n";
			echo "</form>\n";
			include("templates/private_footer.php");
			break;
		}
		
		//Delete item from database, add gold to player's account
		$query = $db->execute("delete from `items` where `id`=?", array($sell['id']));
		$query = $db->execute("update `players` set `gold`=? where `id`=?", array($player->gold + floor($sell['price']/2), $player->id));
		
		$player = check_user($secret_key, $db); //Get updated user info
		
		include("templates/private_header.php");
		echo "You have sold your <b>" . $sell['name'] . "</b> for <b>" . floor($sell['price']/2) . "</b> gold.<br /><br />\n";
		echo "<a href=\"inventory.php\">Return to inventory</a> | <a href=\"shop.php\">Return to shop</a>";
		include("templates/private_footer.php");
		break;
	
	case "weapon":
		//Check in case somebody entered 0
		$_GET['fromprice'] = ($_GET['fromprice'] == 0)?"":$_GET['fromprice'];
		$_GET['toprice'] = ($_GET['toprice'] == 0)?"":$_GET['toprice'];
		$_GET['fromeffect'] = ($_GET['fromeffect'] == 0)?"":$_GET['fromeffect'];
		$_GET['toeffect'] = ($_GET['toeffect'] == 0)?"":$_GET['toeffect'];
		
		//Construct query
		$query = "select `id`, `name`, `description`, `price`, `effectiveness` from `blueprint_items` where ";
		$query .= ($_GET['name'] != "")?"`name` LIKE  ? and ":"";
		$query .= ($_GET['fromprice'] != "")?"`price` >= ? and ":"";
		$query .= ($_GET['toprice'] != "")?"`price` <= ? and ":"";
		$query .= ($_GET['fromeffect'] != "")?"`effectiveness` >= ? and ":"";
		$query .= ($_GET['toeffect'] != "")?"`effectiveness` <= ? and ":"";
		
		$query .= "`type`='weapon' order by `price` asc";
		
		//Construct values array for adoDB
		$values = array();
		if ($_GET['name'] != "")
		{
			array_push($values, "%".trim($_GET['name'])."%");
		}
		if ($_GET['fromprice'])
		{
			array_push($values, intval($_GET['fromprice']));
		}
		if ($_GET['toprice'])
		{
			array_push($values, intval($_GET['toprice']));
		}
		if ($_GET['fromeffect'])
		{
			array_push($values, intval($_GET['fromeffect']));
		}
		if ($_GET['toeffect'])
		{
			array_push($values, intval($_GET['toeffect']));
		}
		
		$query = $db->execute($query, $values); //Search!
		
		include("templates/private_header.php");
		
		echo "<fieldset>";
		echo "<legend><b>Shop Keeper:</b></legend>\n";
		echo "<i>What would you like to see, sir?</i><br /><br />\n";
		echo "<form method=\"get\" action=\"shop.php\">\n";
		echo "<table width=\"100%\">\n";
		echo "<tr>\n<td width=\"40%\">Name:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"name\" value=\"" . stripslashes($_GET['name']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Price:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"4\" value=\"" . stripslashes($_GET['fromprice']) . "\" /> to <input type=\"text\" name=\"toprice\" size=\"4\" value=\"" . stripslashes($_GET['toprice']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Effectiveness:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes($_GET['fromeffect']) . "\" /> to <input type=\"text\" name=\"toeffect\" size=\"4\" value=\"" . stripslashes($_GET['toeffect']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Type:</td>\n";
		echo "<td width=\"60%\"><select name=\"act\" size=\"2\">\n";
		echo "<option value=\"weapon\" selected=\"selected\">Weapons</option>\n";
		echo "<option value=\"armour\">Armour</option>\n";
		echo "</select></td>\n</tr>\n";
		echo "<tr>\n<td></td>";
		echo "<td><input type=\"submit\" value=\"Submit\" /></td>\n</tr>";
		echo "</table>";
		echo "</form>\n";
		echo "</fieldset>";
		echo "<br /><br />";
		echo "<b>Shop Keeper:</b><br />\n";
		echo "<i>Here's our collection of armour:</i><br /><br />\n";
		
		if ($query->recordcount() == 0)
		{
			echo "No items found! Try changing your search criteria.";
		}
		else
		{
			while ($item = $query->fetchrow())
			{
				echo "<fieldset>\n";
				echo "<legend><b>" . $item['name'] . "</b></legend>\n";
				echo "<table width=\"100%\">\n";
				echo "<tr><td width=\"85%\">";
				echo $item['description'] . "\n<br />";
				echo "<b>Effectiveness:</b> " . $item['effectiveness'] . "\n";
				echo "</td><td width=\"15%\">";
				echo "<b>Price:</b> " . $item['price'] . "<br />";
				echo "<a href=\"shop.php?act=buy&id=" . $item['id'] . "\">Buy</a><br />";
				echo "</td></tr>\n";
				echo "</table>";
				echo "</fieldset>\n<br />";
			}
		}
		include("templates/private_footer.php");
		break;
	
	case "armour":
		//Check in case somebody entered 0
		$_GET['fromprice'] = ($_GET['fromprice'] == 0)?"":$_GET['fromprice'];
		$_GET['toprice'] = ($_GET['toprice'] == 0)?"":$_GET['toprice'];
		$_GET['fromeffect'] = ($_GET['fromeffect'] == 0)?"":$_GET['fromeffect'];
		$_GET['toeffect'] = ($_GET['toeffect'] == 0)?"":$_GET['toeffect'];
		
		//Construct query
		$query = "select `id`, `name`, `description`, `price`, `effectiveness` from `blueprint_items` where ";
		$query .= ($_GET['name'] != "")?"`name` LIKE  ? and ":"";
		$query .= ($_GET['fromprice'] != "")?"`price` >= ? and ":"";
		$query .= ($_GET['toprice'] != "")?"`price` <= ? and ":"";
		$query .= ($_GET['fromeffect'] != "")?"`effectiveness` >= ? and ":"";
		$query .= ($_GET['toeffect'] != "")?"`effectiveness` <= ? and ":"";
		
		$query .= "`type`='armour' order by `price` asc";
		
		//Construct values array for adoDB
		$values = array();
		if ($_GET['name'] != "")
		{
			array_push($values, "%".trim($_GET['name'])."%");
		}
		if ($_GET['fromprice'])
		{
			array_push($values, intval($_GET['fromprice']));
		}
		if ($_GET['toprice'])
		{
			array_push($values, intval($_GET['toprice']));
		}
		if ($_GET['fromeffect'])
		{
			array_push($values, intval($_GET['fromeffect']));
		}
		if ($_GET['toeffect'])
		{
			array_push($values, intval($_GET['toeffect']));
		}
		
		$query = $db->execute($query, $values); //Search!
		
		include("templates/private_header.php");
		
		echo "<fieldset>";
		echo "<legend><b>Shop Keeper:</b></legend>\n";
		echo "<i>What would you like to see, sir?</i><br /><br />\n";
		echo "<form method=\"get\" action=\"shop.php\">\n";
		echo "<table width=\"100%\">\n";
		echo "<tr>\n<td width=\"40%\">Name:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"name\" value=\"" . stripslashes($_GET['name']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Price:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"4\" value=\"" . stripslashes($_GET['fromprice']) . "\" /> to <input type=\"text\" name=\"toprice\" size=\"4\" value=\"" . stripslashes($_GET['toprice']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Effectiveness:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" value=\"" . stripslashes($_GET['fromeffect']) . "\" /> to <input type=\"text\" name=\"toeffect\" size=\"4\" value=\"" . stripslashes($_GET['toeffect']) . "\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Type:</td>\n";
		echo "<td width=\"60%\"><select name=\"act\" size=\"2\">\n";
		echo "<option value=\"weapon\">Weapons</option>\n";
		echo "<option value=\"armour\" selected=\"selected\">Armour</option>\n";
		echo "</select></td>\n</tr>\n";
		echo "<tr>\n<td></td>";
		echo "<td><input type=\"submit\" value=\"Submit\" /></td>\n</tr>";
		echo "</table>";
		echo "</form>\n";
		echo "</fieldset>";
		echo "<br /><br />";
		echo "<b>Shop Keeper:</b><br />\n";
		echo "<i>Here's our collection of armour:</i><br /><br />\n";
		
		if ($query->recordcount() == 0)
		{
			echo "No items found! Try changing your search criteria.";
		}
		else
		{
			while ($item = $query->fetchrow())
			{
				echo "<fieldset>\n";
				echo "<legend><b>" . $item['name'] . "</b></legend>\n";
				echo "<table width=\"100%\">\n";
				echo "<tr><td width=\"85%\">";
				echo $item['description'] . "\n<br />";
				echo "<b>Effectiveness:</b> " . $item['effectiveness'] . "\n";
				echo "</td><td width=\"15%\">";
				echo "<b>Price:</b> " . $item['price'] . "<br />";
				echo "<a href=\"shop.php?act=buy&id=" . $item['id'] . "\">Buy</a><br />";
				echo "</td></tr>\n";
				echo "</table>";
				echo "</fieldset>\n<br />";
			}
		}
		
		include("templates/private_footer.php");
		break;
	
	default:
		//Show search form
		include("templates/private_header.php");
		echo "<fieldset>";
		echo "<legend><b>Shop Keeper:</b></legend>\n";
		echo "<i>What would you like to see, sir?</i><br /><br />\n";
		echo "<form method=\"get\" action=\"shop.php\">\n";
		echo "<table width=\"100%\">\n";
		echo "<tr>\n<td width=\"40%\">Name:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"name\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Price:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromprice\" size=\"4\" /> to <input type=\"text\" name=\"toprice\" size=\"4\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Effectiveness:</td>\n";
		echo "<td width=\"60%\"><input type=\"text\" name=\"fromeffect\" size=\"4\" /> to <input type=\"text\" name=\"toeffect\" size=\"4\" /></td>\n";
		echo "</td>\n</tr>";
		echo "<tr>\n<td width=\"40%\">Type:</td>\n";
		echo "<td width=\"60%\"><select name=\"act\" size=\"2\">\n";
		echo "<option value=\"weapon\" selected=\"selected\">Weapons</option>\n";
		echo "<option value=\"armour\">Armour</option>\n";
		echo "</select></td>\n</tr>\n";
		echo "<tr>\n<td></td>";
		echo "<td><input type=\"submit\" value=\"Submit\" /></td>\n</tr>";
		echo "</table>";
		echo "</form>\n";
		echo "</fieldset>";
		include("templates/private_footer.php");
		break;
}
?>