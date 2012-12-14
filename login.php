<?php
require_once 'includes/common.php';
require_once 'api/includes/config.php';

// Check login info
global $db, $config;
$returnURL = isset($_GET['return']) ? $_GET['return'] : BASE_URL;

if (!isset($_POST['username']) && !isset($_POST['password'])) {
	invalidLogin();
}

$user = $_POST['username'];
$pass = $_POST['password'];
$hash = '*';

$query = "
	SELECT u.*
	FROM {$config->tables['users']} u
	WHERE u.login = :login
";
$stmt = $db->prepare($query);
$stmt->bindParam(':login',$user);
$stmt->execute();
if (!($user = $stmt->fetchObject()))
	invalidLogin();
$hash = $user->password;
if (User::$hasher->checkPassword($pass,$hash)) {
	$json = get_json_from_url(BASE_URL.'/api/users/id/'.$user->user_id);

	setUserInfo(
		$user->user_id, 
		$user->login, 
		$user->name, 
		$user->api_key,
		$json->response[0]->group
	);
}
else
	invalidLogin();
goBack();

function goBack() {
	global $returnURL;
	header('Location: '.$returnURL);
}

function invalidLogin() {
	$_SESSION['error'] = 'Invalid username/password combination';
}
