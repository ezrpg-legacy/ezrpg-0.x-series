<?php
/*************************************/
/*           ezRPG script            */
/*         Written by Zeggy          */
/*  http://code.google.com/p/ezrpg   */
/*    http://www.ezrpgproject.com/   */
/*************************************/

$config_server = "localhost"; //Database host
$config_database = "zrpg"; //Database name
$config_username = "root"; //Database username
$config_password = ""; //Database password
$secret_key = "mysecret"; //Secret key, make it a random word/sentence/whatever

//Do not edit below this line

$version = "0.12";

include('adodb/adodb.inc.php'); //Include adodb files
$db = &ADONewConnection('mysql'); //Connect to database
$db->Connect($config_server, $config_username, $config_password, $config_database); //Select table

$db->SetFetchMode(ADODB_FETCH_ASSOC); //Fetch associative arrays
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC; //Fetch associative arrays
//$db->debug = true; //Debug

?>