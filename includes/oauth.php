<?php
require_once('includes/common.php');
require 'api/lib/oauth/OAuth.php';

/**
 * @param string $url URL to call
 * @param string $method GET, POST, PUT, or DELETE
 * @param array $params array of parameters
 */
function oauthRequest($url,$method,$params) {
	if (strtoupper($method) === 'PUT') {
		$params['_METHOD'] = 'PUT';
		$method = 'POST';
	}
	if (strtoupper($method) === 'DELETE') {
		$params['_METHOD'] = 'DELETE';
		$method = 'POST';
	}

	$key = $_SESSION['login'];
	if (isset($_SESSION['api_key']))
		$secret = $_SESSION['api_key'];
	else
		$secret = ''; // Will probably fail

	$consumer = new OAuthConsumer($key, $secret);
	$sig_method = new OAuthSignatureMethod_HMAC_SHA1();

	// Sign the request with user's api key
	$req = OAuthRequest::from_consumer_and_token($consumer, null, 
		$method, $url, $params);
	$req->sign_request($sig_method, $consumer, null);

	// Get data with cURL
	$ch = curl_init($req->to_url());
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

	$response = curl_exec($ch);
	curl_close($ch);

	return $response;
}

