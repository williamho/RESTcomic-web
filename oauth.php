<?php
require_once('includes/oauth.php');

if (isset($_GET['return']))
	$returnURL = $_GET['return'];
else
	$returnURL = null;
if (isset($_GET['url']))
	$url = $_GET['url'];
else
	$url = null;
if (isset($_GET['method']))
	$method = $_GET['method'];
else
	$method = 'GET';

$_POST['ip'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['returned'] = json_decode(oauthRequest($url,$method,$_POST));

header('Location: '.$returnURL);

