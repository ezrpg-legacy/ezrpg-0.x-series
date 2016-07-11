<!-- Edit templates/private_header.php to edit the layout -->
<html>
<head>
<title>ezRPG :: <?=PAGENAME?></title>
<link rel="stylesheet" type="text/css" href="./templates/style.css" />
</head>
<body>
<div id="wrapper">

<div id="header">
<div id="header-text">
ezRPG
</div>
</div>

<div id="left">
<div class="left-section">
<b>Username:</b> <?=$player->username?><br />
<b>Level:</b> <?=$player->level?><br />
<?php
$percent = intval(($player->exp / $player->maxexp) * 100);
?>
<b>EXP:</b> <?=$player->exp?>/<?=$player->maxexp?> (<?=$percent?>%)<br />
<b>HP:</b> <?=$player->hp?>/<?=$player->maxhp?><br />
<b>Energy:</b> <?=$player->energy?>/<?=$player->maxenergy?><br />
<b>Gold:</b> <?=$player->gold?><br />
</div>

<div class="left-section">
<ul>
<li><a class="header">Links</a></li>
<li><a href="home.php">Home</a></li>
<li><a href="log.php">Log [<?=unread_log($player->id, $db)?>]</a></li>
<li><a href="inventory.php">Inventory</a></li>
<li><a href="bank.php">Bank</a></li>
<li><a href="hospital.php">Hospital</a></li>
<li><a href="battle.php">Battle</a></li>
<li><a href="shop.php">Shop</a></li>
</ul>
</div>

<div class="left-section">
<ul>
<li><a class="header">Community</a></li>
<li><a href="mail.php">Mail [<?=unread_messages($player->id, $db)?>]</a></li>
<li><a href="members.php">Members List</a></li>
<li><a href="#">Forum</a></li>
</div>

<div class="left-section">
<ul>
<li><a class="header">Other</a></li>
<li><a href="#">Help</a></li>
<li><a href="http://www.ezrpgproject.com/">ezRPG Project</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</div>

<br /><br />

<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">
<img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-sa/3.0/88x31.png" />
</a>

</div>

<div id="right">
<div id="content">