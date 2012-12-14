<?php
session_start();

define('BASE_URL',getCurrentDir());

if (!isset($_SESSION['user_id']))
	setUserInfo(0,'unregistered','','');

function getBaseURL() {
	$scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") ?
		'http' : 'https';
	$pageURL = $scheme . '://' . $_SERVER['SERVER_NAME'];
	if ($_SERVER['SERVER_PORT'] != '80')
		$pageURL .= ':'.$_SERVER['SERVER_PORT'];
	return $pageURL;
}

function getCurrentURI() {
	$pageURL = getBaseURL();
	$pageURL .= $_SERVER['REQUEST_URI'];
	return $pageURL;
}

function getCurrentDir() {
	$pageURL = getBaseURL();
	$pageURL .= parse_url($_SERVER['PHP_SELF'],PHP_URL_PATH);
	$pos = strrpos($pageURL,'/');
	return substr($pageURL,0,$pos);
}

function setUserInfo($id,$login,$name,$api_key) {
	$_SESSION['user_id'] = $id;
	$_SESSION['login'] = $login;
	$_SESSION['user_name'] = $name;
	$_SESSION['api_key'] = $api_key;
}

function getAPIQuery($uri,$method,$return) {
	$arr = array(
		'url' => getCurrentDir().$uri, 
		'method' => $method,
		'return' => $return
	);
	return http_build_query($arr);
}

function printErrors($json) {
	$errors = $json->errors;

	if (empty($errors))
		return false;

	echo '<ul>';
	foreach ($errors as $error) {
		echo '<li>'.$error->msg.'</li>';
	}
	echo '</ul>';
	return true;
}

function printChildren($parent) {
	if (!isset($parent->replies))
		return;
	echo '<ul class="comments">';
	foreach($parent->replies as $reply) {
		printComment($reply);
		printChildren($reply);
	}
	echo '</ul>';
}

function printComment($comment) {
	echo '<li><span class="comment_information" '.
		'id="comment-'.$comment->id.'">'.
		'<img src="'.$comment->author->icon.
		'" style="vertical-align:middle"> '. 
		'<a href="'.BASE_URL.'/user/'.$comment->author->login.'">'.
		'<b style="color:'.$comment->author->group_color.'">'.
		$comment->author->name.
		'</b></a> ('.$comment->author->login.') at <i>'.
		$comment->timestamp.'</i> '.
		'<a href="'.BASE_URL.'/id/'.$comment->post_id.
		'#comment-'.$comment->id.'">#</a> '.
		'[<a href="#" onClick="window.open(\''.
		BASE_URL.'/reply/'.$comment->id.'\');">Reply</a>'.']'.
		'</span><p>'. 
		$comment->content .'</p>';
}

function printTags($tagsArray) {
	foreach ($tagsArray as $tag) {
		echo '<a href="'.BASE_URL.'/tagged/'.$tag.'">'.$tag.'</a> ';
	}
}

