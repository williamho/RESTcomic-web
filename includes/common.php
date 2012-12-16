<?php
session_start();

define('BASE_URL',getCurrentDir());

if (!isset($_SESSION['user_id'])) {
	$json = get_json_from_url(BASE_URL.'/api/users/id/0');
	setUserInfo(0,'unregistered','','',$json->response[0]->group);
}

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

function setUserInfo($id,$login,$name,$api_key,$group) {
	$_SESSION['user_id'] = $id;
	$_SESSION['login'] = $login;
	$_SESSION['user_name'] = $name;
	$_SESSION['api_key'] = $api_key;
	$_SESSION['group'] = $group;
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

// this is a huge mess of a function
function printComment($comment) {
	$popup_info = "'width=500,height=300,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1'";

	echo '<li><span class="comment_information" '.
		'id="comment-'.$comment->id.'">'.
		// The icon
		'<img src="'.$comment->author->icon.
		'" style="float:left; margin: 0 5px 0 0;"> '. 

		// The name
		'<a href="'.BASE_URL.'/user/'.$comment->author->login.'" style="text-decoration:none;">'.
		'<b style="color:'.$comment->author->group_color.'; ">'.
		$comment->author->name .'</b></a> '.

		// The login
		'('.$comment->author->login.') '.

		// The page anchor
		'<a href="'.BASE_URL.'/id/'.$comment->post_id.
		'#comment-'.$comment->id.'" class="sym" title="jump to comment">#</a> '.

		// The reply link
		'<a href="#comment-'.$comment->id.'" onClick="javascript:void window.open(\''.
		BASE_URL.'/reply/'.$comment->id.'\', '.'\'_blank\', '. $popup_info .
		');" class="sym" title="reply">&#9166;</a> ';

	// Edit
	if (userCanEditComment($comment)) {
	echo '<a href="#comment-'.$comment->id.'" onClick="javascript:void window.open(\''.
			BASE_URL.'/editcomment/'.$comment->id.'\', '.'\'_blank\', '. $popup_info .
			');" class="sym" title="edit">&#9997</a> ';

	echo '<a href="#comment-'.$comment->id.'" onClick="javascript:void window.open(\''.
			BASE_URL.'/deletecomment/'.$comment->id.'\', '.'\'_blank\', '. $popup_info .
			');" class="sym" title="delete">&#215;</a> ';
	}
		

	// The timestamp
	echo '<br/>at <i>'.$comment->timestamp.'</i> '.
		'</span>'. 

	// The content
		'<p>' . $comment->content .'</p>';
}

function userCanEditComment($comment) {
	$edit_perm = $_SESSION['group']->permissions->edit_comment;
	$editable = false;
	switch($edit_perm) {
	case 'own':
		if ($_SESSION['user_id'] == $comment->author->id)
			$editable = true;
		break;
	case 'group':
		$json = getUsersByIds($comment->author->id);
		if (!isset($json->response[0]))
			return false;

		$group_id = $json->response[0]->group->id;
		if ($_SESSION['group']->id == $group_id)
			$editable = true;
		break;
	case 'yes':
		$editable = true;
	}
	return $editable;
}

function printTags($tagsArray) {
	foreach ($tagsArray as $tag) {
		echo '<a href="'.BASE_URL.'/tagged/'.$tag.'">'.$tag.'</a> ';
	}
}

function get_json_from_url($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($ch);
	//curl_close($ch);
	return json_decode($data);
}

