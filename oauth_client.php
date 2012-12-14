<?php
require 'api/lib/oauth/OAuth.php';
require 'api/includes/config.php';

$params = array();
foreach ($_POST as $key => $value)
	$params[$key] = $value;

$login = $params['login'];
// Get api key from db
global $db, $config;
$query = "
	SELECT u.api_key
	FROM {$config->tables['users']} u
	WHERE u.login = :login
";
$stmt = $db->prepare($query);
$stmt->bindParam(':login',$login);
$stmt->execute();
$result = $stmt->fetchColumn();

$key = $login;
$secret = $result;
$consumer = new OAuthConsumer($key, $secret);
$sig_method = new OAuthSignatureMethod_HMAC_SHA1();

$url = 'http://'.$_SERVER['SERVER_NAME'].':8888/api/posts';
//$url = 'api/posts';
//$url = 'http://'.$_SERVER['SERVER_NAME'].':8888/api/posts/id/1';
//$params['_METHOD'] = 'DELETE';

//use oauth lib to sign request
$req = OAuthRequest::from_consumer_and_token($consumer, null, 'POST', $url, $params);
$req->sign_request($sig_method, $consumer, null);//note: double entry of token

//get data using signed url
$ch = curl_init($req->to_url());
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
$response = curl_exec($ch);

if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200)
	header('Content-type: application/json');
print_r($response);  

curl_close($ch);

