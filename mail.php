<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

include("lib.php");
define("PAGENAME", "Mail");
$player = check_user($secret_key, $db);

$errormsg = "<font color=\"red\">";
$errors = 0;
if ($_POST['sendmail'])
{
	//Process mail info, show success message
	$query = $db->execute("select `id` from `players` where `username`=?", array($_POST['to']));
	if ($query->recordcount() == 0)
	{
		$errormsg .= "That player does not exist!<br />";
		$errors = 1;
	}
	if (!$_POST['body'])
	{
		$errormsg .= "You must enter a message!<br />";
		$errors = 1;
	}
	
	if ($errors != 1)
	{
		$sendto = $query->fetchrow();
		$insert['to'] = $sendto['id'];
		$insert['from'] = $player->id;
		$insert['body'] = $_POST['body'];
		//$insert['body'] = str_replace("&", "&amp;", $insert['body']);
		//$insert['body'] = str_replace("<", "&lt;", $insert['body']);
		//$insert['body'] = str_replace(">", "&gt;", $insert['body']);
		//$insert['body'] = str_replace("\"", "&quot;", $insert['body']);
		//$insert['body'] = str_replace(">", "&gt;", $insert['body']);
		//Hehe, found an easier way of doing that ^ with htmlentities():
		$insert['body'] = htmlentities($_POST['body'], ENT_QUOTES);
		//$insert['body'] = (!get_magic_quotes_gpc())?addslashes($insert['body']):$insert['body'];
		$insert['subject'] = ($_POST['subject'] == "")?"No Subject":$_POST['subject'];
		//$insert['subject'] = (!get_magic_quotes_gpc())?addslashes($insert['subject']):$insert['subject'];
		$insert['time'] = time();
		$query = $db->execute("insert into `mail` (`to`, `from`, `body`, `subject`, `time`) values (?, ?, ?, ?, ?)", array($insert['to'], $insert['from'], $insert['body'], $insert['subject'], $insert['time']));
		if ($query)
		{
			include("templates/private_header.php");
			echo "Your message was successfully sent to " . $_POST['to'] . "!";
			include("templates/private_footer.php");
			exit;
		}
		else
		{
			$errormsg .= "Sorry, the mail could not be sent.";
			//Add to admin error log, or whatever, maybe for another version ;)
		}
	}
}
$errormsg .= "</font><br />\n";

include("templates/private_header.php");
?>

<script>
function checkAll() {
	count = document.inbox.elements.length;
    for (i=0; i < count; i++) 
	{
    	if(document.inbox.elements[i].checked == 1)
    		{document.inbox.elements[i].checked = 0; document.inbox.check.checked=0;}
    	else {document.inbox.elements[i].checked = 1; document.inbox.check.checked=1;}
	}
}
</script>


<a href="mail.php">Inbox</a> | <a href="mail.php?act=compose">Compose Mail</a>
<br /><br />
<?php
switch($_GET['act'])
{
	case "read": //Reading a message
		$query = $db->execute("select `id`, `to`, `from`, `subject`, `body`, `time`, `status` from `mail` where `id`=? and `to`=?", array($_GET['id'], $player->id));
		if ($query->recordcount() == 1)
		{
			$msg = $query->fetchrow();
			echo "<table width=\"100%\" border=\"0\">\n";
			echo "<tr><td width=\"20%\"><b>To:</b></td><td width=\"80%\"><a href=\"profile.php?id=" . $msg['to'] . "\">" . $player->username . "</a></td></tr>\n";
			$from = $db->GetOne("select `username` from `players` where `id`=?", array($msg['from']));
			echo "<tr><td width=\"20%\"><b>From:</b></td><td width=\"80%\"><a href=\"profile.php?id=" . $from . "\">" . $from . "</a></td></tr>\n";
			echo "<tr><td width=\"20%\"><b>Date:</b></td><td width=\"80%\">" . date("F j, Y, g:i a", $msg['time']) . "</td></tr>";
			echo "<tr><td width=\"20%\"><b>Subject:</b></td><td width=\"80%\">" . stripslashes($msg['subject']) . "</td></tr>";
			echo "<tr><td width=\"20%\"><b>Body:</b></td><td width=\"80%\">" . stripslashes(nl2br($msg['body'])) . "</td></tr>";
			echo "</table>";
			if ($msg['status'] == "unread")
			{
				$query = $db->execute("update `mail` set `status`='read' where `id`=?", array($msg['id']));
			}
			echo "<br /><br />\n";
			echo "<table width=\"30%\">\n";
			echo "<tr><td width=\"50%\">\n";
			echo "<form method=\"post\" action=\"mail.php?act=compose\">\n";
			echo "<input type=\"hidden\" name=\"to\" value=\"" . $from . "\" />\n";
			echo "<input type=\"hidden\" name=\"subject\" value=\"RE: " . stripslashes($msg['subject']) . "\" />\n";
			$reply = explode("\n", $msg['body']);
			foreach($reply as $key=>$value)
			{
				$reply[$key] = ">>" . $value;
			}
			$reply = implode("\n", $reply);
			echo "<input type=\"hidden\" name=\"body\" value=\"\n\n\n" . $reply . "\" />\n";
			echo "<input type=\"submit\" value=\"Reply\" />\n";
			echo "</form>\n";
			echo "</td><td width=\"50%\">\n";
			echo "<form method=\"post\" action=\"mail.php?act=delete\">\n";
			echo "<input type=\"hidden\" name=\"id\" value=\"" . $msg['id'] . "\" />\n";
			echo "<input type=\"submit\" name=\"delone\" value=\"Delete\" />\n";
			echo "</form>\n";
			echo "</td></tr>\n</table>";
		}
		break;
	
	case "compose": //Composing mail (justt he form, processing is at the top of the page)
		echo $errormsg;
		echo "<form method=\"POST\" action=\"mail.php?act=compose\">\n";
		echo "<table width=\"100%\" border=\"0\">\n";
		echo "<tr><td width=\"20%\"><b>To:</b></td><td width=\"80%\"><input type=\"text\" name=\"to\" value=\"";
		echo ($_POST['to'] != "")?$_POST['to']:$_GET['to'];
		echo "\" /></td></tr>\n";
		echo "<tr><td width=\"20%\"><b>Subject:</b></td><td width=\"80%\"><input type=\"text\" name=\"subject\" value=\"";
		echo ($_POST['subject'] != "")?stripslashes($_POST['subject']):stripslashes($_GET['subject']);
		echo "\" /></td></tr>\n";
		echo "<tr><td width=\"20%\"><b>Body:</b></td><td width=\"80%\"><textarea name=\"body\" rows=\"15\" cols=\"50\">";
		echo ($_POST['body'] != "")?stripslashes(stripslashes($_POST['body'])):stripslashes(stripslashes($_GET['body']));
		echo "</textarea></td></tr>\n";
		echo "<tr><td></td><td><input type=\"submit\" value=\"Send!\" name=\"sendmail\" /></td></tr>\n";
		echo "</table>\n";
		echo "</form>\n";
		break;
	
	case "delete":
		if ($_POST['delone']) //Deleting message from viewing page, single delete
		{
			if (!$_POST['id'])
			{
				echo "A message must be selected!";
			}
			else
			{
				$query = $db->getone("select count(*) as `count` from `mail` where `id`=? and `to`=?", array($_POST['id'], $player->id));
				if ($query['count'] = 0)
				{
					//In case there are some funny guys out there ;)
					echo "This message does not belong to you!";
				}
				else
				{
					if (!$_POST['deltwo'])
					{
						echo "Are you sure you want to delete this message?<br /><br />\n";
						echo "<form method=\"post\" action=\"mail.php?act=delete\">\n";
						echo "<input type=\"hidden\" name=\"id\" value=\"" . $_POST['id'] . "\" />\n";
						echo "<input type=\"hidden\" name=\"deltwo\" value=\"1\" />\n";
						echo "<input type=\"submit\" name=\"delone\" value=\"Delete!\" />\n";
						echo "</form>";
					}
					else
					{
						$query = $db->execute("delete from `mail` where `id`=?", array($_POST['id']));
						echo "The message has been successfully deleted!";
						//Redirect back to inbox, or show success message
						//Can be changed in the admin panel
					}
				}
			}
		}
		else if ($_POST['delmultiple']) //Deleting messages from inbox, multiple selections
		{
			if (!$_POST['id'])
			{
				echo "A message must be selected!";
			}
			else
			{
				foreach($_POST['id'] as $msg)
				{
					$query = $db->getone("select count(*) as `count` from `mail` where `id`=? and `to`=?", array($msg, $player->id));
					if ($query['count'] = 0)
					{
						//In case there are some funny guys out there ;)
						echo "This message does not belong to you!";
						$delerror = 1;
					}
				}
				if (!$delerror)
				{
					if (!$_POST['deltwo'])
					{
						echo "Are you sure you want to delete this message?<br /><br />\n";
						echo "<form method=\"post\" action=\"mail.php?act=delete\">\n";
						foreach($_POST['id'] as $msg)
						{
							echo "<input type=\"hidden\" name=\"id[]\" value=\"" . $msg . "\" />\n";
						}
						echo "<input type=\"hidden\" name=\"deltwo\" value=\"1\" />\n";
						echo "<input type=\"submit\" name=\"delmultiple\" value=\"Delete!\" />\n";
						echo "</form>";
					}
					else
					{
						foreach($_POST['id'] as $msg)
						{
							$query = $db->execute("delete from `mail` where `id`=?", array($msg));
						}
						echo "The message has been successfully deleted!";
						//Redirect back to inbox, or show success message
						//Can be changed in the admin panel (TODO)
					}
				}
			}
		}
		break;
	
	default: //Show inbox
		echo "<form method=\"post\" action=\"mail.php?act=delete\" name=\"inbox\">\n";
		echo "<table width=\"100%\" border=\"0\">\n";
		echo "<tr>\n";
		echo "<td width=\"5%\"><input type=\"checkbox\" onclick=\"javascript: checkAll();\" name=\"check\" /></td>\n";
		echo "<td width=\"20%\"><b>From</b></td>\n";
		echo "<td width=\"35%\"><b>Subject</b></td>\n";
		echo "<td width=\"40%\"><b>Date</b></td>\n";
		echo "</tr>\n";
		$query = $db->execute("select `id`, `from`, `subject`, `time`, `status` from `mail` where `to`=? order by `time` desc", array($player->id));
		if ($query->recordcount() > 0)
		{
			$bool = 1;
			while($msg = $query->fetchrow())
			{
				echo "<tr class=\"row" . $bool . "\">\n";
				echo "<td width=\"5%\"><input type=\"checkbox\" name=\"id[]\" value=\"" . $msg['id'] . "\" /></td>\n";
				$from = $db->GetOne("select `username` from `players` where `id`=?", array($msg['from']));
				echo "<td width=\"20%\">";
				echo ($msg['status'] == "unread")?"<b>":"";
				echo "<a href=\"profile.php?id=" . $from . "\">" . $from . "</a>";
				echo ($msg['status'] == "unread")?"</b>":"";
				echo "</td>\n";
				echo "<td width=\"35%\">";
				echo ($msg['status'] == "unread")?"<b>":"";
				echo "<a href=\"mail.php?act=read&id=" . $msg['id'] . "\">" . stripslashes($msg['subject']) . "</a>";
				echo ($msg['status'] == "unread")?"</b>":"";
				echo "</td>\n";
				echo "<td width=\"40%\">" . date("F j, Y, g:i a", $msg['time']) . "</td>\n";
				echo "</tr>\n";
				$bool = ($bool==1)?2:1;
			}
		}
		else
		{
			echo "<tr class=\"row1\">\n";
			echo "<td colspan=\"4\"><b>No Messages</b></td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "<input type=\"submit\" name=\"delmultiple\" value=\"Delete!\" />\n";
		echo "</form>";
		break;
}

include("templates/private_footer.php");
?>