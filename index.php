<?php
require_once 'includes/common.php';
require 'lib/Slim/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

require_once 'api/includes/config.php';

$app = new \Slim\Slim();
$app->config(array('templates.path'=>'templates'));

$app->get('/', function() use($app) {
	$page = $app->request()->get('page');	
	$perpage = $app->request()->get('perpage');	
	if ($page < 1)
		$page = 1;
	if (is_null($perpage))
		$perpage = 1;

	$response = getPostsBetweenIds(null,null,true,1,$page);
	render_posts_from_api($response,$page,'Posts (page '.$page.')',true);
});

$app->get('/posts/new', function()  use($app) {
	$data = array('title' => 'New post');
	$app->render('makepost.html',$data);
});

$app->get('/posts/:slug', function($slug) {
	$response = getPostBySlug($slug);
	render_post_from_api($response);
});

$app->get('/posts/by/:name', function($name) use($app) {
	$page = $app->request()->get('page');	
	if ($page < 1)
		$page = 1;
		
	$url = BASE_URL.'/api/posts/by_author/'.$name.'?reverse=true&page='.$page;
	render_posts_from_url($url,$page,'Posts by '.$name.' (page '.$page.')');
});

$app->get('/id/:id/edit', function($id) use($app) {
	$url = BASE_URL.'/api/posts/id/'.$id.'?format=markdown';
	$json = get_json_from_url($url);
	if (isset($json->response[0]))
		$post = $json->response[0];
	$data = array(
		'title'=>'Edit post',
		'post'=>$post
	);
	$app->render('makepost.html',$data);
});

$app->get('/register', function() use($app) {
	$data = array('title'=>'Register');
	$app->render('register.html',$data);
});

$app->get('/userinfo', function() use($app) {
	$url = BASE_URL.'/api/users/id/'.$_SESSION['user_id'].'?getemail=true';
	$json = get_json_from_url($url);

	if (!isset($json->response[0]))
		die();

	$data = array(
		'title'=>'Change user info',
		'user'=>$json->response[0],
		'editgroup'=>false
	);
	$app->render('edituser.html',$data);
});

$app->get('/user/id/:id/edit', function($id) use($app) {
	$url = BASE_URL.'/api/users/id/'.$id.'?getemail=true';
	$json = get_json_from_url($url);

	if (!isset($json->response[0]))
		die();

	$data = array(
		'title'=>'Change user info',
		'user'=>$json->response[0],
		'editgroup'=>true
	);
	$app->render('edituser.html',$data);
});


$app->get('/reverse', function() use($app) {
	$page = $app->request()->get('page');	
	$perpage = $app->request()->get('perpage');	
	if ($page < 1)
		$page = 1;
	if (is_null($perpage))
		$perpage = 1;

	$response = getPostsBetweenIds(null,null,true,1,$page);
	render_posts_from_api($response,$page,'Posts (page '.$page.')',true);
});

$app->get('/reply/:comment_id', function($comment_id) use($app) {
	$data = array(
		'title' => 'Reply to comment',
		'parent_comment_id' => $comment_id
	);
	$app->render('reply.html',$data);
});

$app->get('/id/:id', function($id) {
	$response = getPostsByIds($id);
	render_post_from_api($response);
});

$app->get('/user/id/:id', function($id) {
	render_user_info('id/'.$id);
});

$app->get('/user/:login', function($login) {
	render_user_info($login);
});

$app->get('/tagged/:tags', function($tags) use($app) {
	$page = $app->request()->get('page');	
	if ($page < 1)
		$page = 1;
		
	$response = getPostsTagged($tags);
	render_posts_from_api($response,$page,'Tagged '.$tags.' (page '.$page.')',false);

	//$url = BASE_URL.'/api/posts/tagged/'.$tags.'?reverse=true&page='.$page;
	//render_posts_from_url($url,$page,'Tagged '.$tags.' (page '.$page.')');
});

$app->run();

function render_post_from_api($api) {
	global $app;

	if (isset($api->response[0]))
		$post = $api->response[0];
	else
		die("Post doesn't exist");
	$comments = getCommentsByPostId($post->id)->response;

	$data = array(
		'title' => $post->title,
		'post' => $post,
		'comments' => $comments
	);
	$app->render('post.html',$data);
}

function render_post_from_url($url) {
	global $app;
	$posts = get_json_from_url($url);

	if (isset($posts->response[0]))
		$post = $posts->response[0];
	else
		die("Post doesn't exist");
	$comments_json = get_json_from_url($post->comments->down);

	$data = array(
		'title' => $post->title,
		'post' => $post,
		'comments' => $comments_json->response
	);
	$app->render('post.html',$data);
}

function render_posts_from_api($api,$page,$title,$showcomments=false) {
	global $app;

	if (isset($api->response[0]))
		$posts = $api->response;
	else
		die("Posts don't exist");

	$newer_url = (is_null($api->meta->prev)) ? null : '?page='.($page-1);
	$older_url = (is_null($api->meta->next)) ? null : '?page='.($page+1);

	if ($showcomments) {
		$comments = array(getCommentsByPostId($posts[0]->id)->response);
		//array(get_json_from_url($posts[0]->comments->down)->response);
	}

	$data = array(
		'title' => $title,
		'posts' => $posts,
		'newer_url' => $newer_url,
		'older_url' => $older_url
	);
	if ($showcomments)
		$data['comments'] = $comments;
	
	$app->render('multipost.html',$data);
}

function render_posts_from_url($url,$page,$title='',$showcomments=false) {
	global $app;
	$json = get_json_from_url($url);

	render_posts_from_api($json,$page,$title,$showcomments);
}

function render_user_info($login) {
	global $app;
	$url = BASE_URL.'/api/users/'.$login;
	$users = get_json_from_url($url);
	$users = $users->response;

	if (empty($users))
		die();
	$user = $users[0];

	$url = BASE_URL.'/api/posts/by_author/'.$login.'?reverse=true';
	$posts = get_json_from_url($url);
	$posts = $posts->response;

	$url = BASE_URL.'/api/comments/by_author/'.$login.'?reverse=true';
	$comments = get_json_from_url($url);
	$comments = $comments->response;

	$data = array(
		'title' => $user->name,
		'user' => $user,
		'posts' => $posts,
		'comments' => $comments
	);
	$app->render('user.html',$data);
}

