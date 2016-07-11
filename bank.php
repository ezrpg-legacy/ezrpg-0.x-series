<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include("lib.php");
define("PAGENAME", "Bank");
$player = check_user($secret_key, $db);

//Allow the option to change the interest rate when making admin panel

if (isset($_POST['deposit']))
{
	if ($_POST['deposit'] > $player->gold || $_POST['deposit'] < 0)
	{
		$msg = "<font color=\"red\">You cannot deposit that much gold!</font>\n";
	}
	else
	{
		$query = $db->execute("update `players` set `bank`=?, `gold`=? where `id`=?", array($player->bank + $_POST['deposit'], $player->gold - $_POST['deposit'], $player->id));
		$msg = "<font color=\"green\">You deposited your gold into the bank.</font>\n";
		$player = check_user($secret_key, $db); //Get new stats so new amount of gold is displayed on left menu
	}
}
else if (isset($_POST['withdraw']))
{
	if ($_POST['withdraw'] > $player->bank || $_POST['withdraw'] < 0)
	{
		$msg = "<font color=\"red\">You do not have that much gold in your bank account!</font>\n";
	}
	else
	{
		$query = $db->execute("update `players` set `bank`=?, `gold`=? where `id`=?", array($player->bank - $_POST['withdraw'], $player->gold + $_POST['withdraw'], $player->id));
		$msg = "<font color=\"green\">You withdrew your gold from the bank.</font>\n";
		$player = check_user($secret_key, $db); //Get new stats so new amount of gold is displayed on left menu
	}
}
else if (isset($_POST['interest']))
{
	$interest_rate = intval($player->bank * 0.03);
	if ($player->interest == 0)
	{
		$query = $db->execute("update `players` set `interest`=1, `gold`=? where `id`=?", array($player->gold + $interest_rate, $player->id));
		$msg = "<font color=\"green\">You collected your interest.</font>\n";
		$player = check_user($secret_key, $db); //Get new stats so new amount of gold is displayed on left menu
	}
	else
	{
		$msg = "<font color=\"red\">You can collect your interest again tomorrow.</font>\n";
	}
}

include("templates/private_header.php");

echo "<b>Bank Assistant:</b><br />\n<i>\n";
echo (isset($msg))?$msg:"Welcome to the bank, sir. What would you like to do?\n";
echo "</i>";
?>
<br /><br />
<table width="100%">
<tr>
<td width="50%">
<fieldset>
<legend>Deposit Gold:</legend>
You have <b><?=$player->gold?></b> gold on you.<br />
<form method="post" action="bank.php">
<input type="text" name="deposit" value="<?=$player->gold?>" />
<input type="submit" name="bank_action" value="Deposit"/>
</form>
</fieldset>
</td>
<td rowspan="2" width="50%">
<fieldset class="empty">
<legend>Collect Interest</legend>
The interest rate is at: 3%
<br /><br />
<form method="post" action="bank.php">
<?php
//Disable interest button once user has collected it
//Store action in 'interest' column in players table
//Reset that column with cron job
?>
<input type="submit" name="interest" value="Collect!"<?=($player->interest == 1)?" disabled=\"disabled\"":""?>/>
</form>

<br />
<?=($player->interest == 1)?"You may collect your interest again tomorrow!":"You may collect your interest now!"?>

<br /><br />
Your daily interest is: <b><?=intval($player->bank * 0.03)?></b> gold
</fieldset>
</td>
</tr>
<tr>
<td width="50%">
<fieldset>
<legend>Withdraw Gold:</legend>
You have <b><?=$player->bank?></b> gold in your bank account.<br />
<form method="post" action="bank.php">
<input type="text" name="withdraw" value="<?=$player->bank?>" />
<input type="submit" name="bank_action" value="Withdraw"/>
</form>
</fieldset>
</td>
</tr>
</table>

<?php
include("templates/private_footer.php");
?>