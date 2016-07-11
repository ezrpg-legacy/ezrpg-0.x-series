<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

//Function to check if user is logged in, and if so, return user data as an object
function check_user($secret_key, &$db)
{
	if (!isset($_SESSION['userid']) || !isset($_SESSION['hash']))
	{
		header("Location: index.php");
		exit;
	}
	else
	{
		$check = sha1($_SESSION['userid'] . $_SERVER['REMOTE_ADDR'] . $secret_key);
		if ($check != $_SESSION['hash'])
		{
			session_unset();
			session_destroy();
			header("Location: index.php");
			exit;
		}
		else
		{
			$query = $db->execute("select * from `players` where `id`=?", array($_SESSION['userid']));
			$userarray = $query->fetchrow();
			if ($query->recordcount() == 0)
			{
				session_unset();
				session_destroy();
				header("Location: index.php");
				exit;
			}
			foreach($userarray as $key=>$value)
			{
				$user->$key = $value;
			}
			return $user;
		}
	}
}

//Gets the number of unread messages
function unread_messages($id, &$db)
{
	$query = $db->getone("select count(*) as `count` from `mail` where `to`=? and `status`='unread'", array($id));
	return $query['count'];
}

//Gets new log messages
function unread_log($id, &$db)
{
	$query = $db->getone("select count(*) as `count` from `user_log` where `player_id`=? and `status`='unread'", array($id));
	return $query['count'];
}

//Insert a log message into the user logs
function addlog($id, $msg, &$db)
{
	$insert['player_id'] = $id;
	$insert['msg'] = $msg;
	$insert['time'] = time();
	$query = $db->autoexecute('user_log', $insert, 'INSERT');
}

?>