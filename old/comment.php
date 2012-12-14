<?php
require_once 'includes/common.php';
?>
<html>
<?php
require_once 'includes/userinfo.php';

if (isset($_SESSION['returned'])) {
	echo $_SESSION['returned'];
	unset($_SESSION['returned']);
}
?>
 <form action="oauth.php?<?php 
	 //echo getAPIQuery('/api/posts/id/1/comments','POST',getCurrentURI());
	 echo getAPIQuery('/api/comments','POST',getCurrentURI());
 ?>" method="post">
   Make a comment<br>
  Comment ID: <input type="text" name="comment_id" size="5"><br>
 	Name: <input type="text" name="name" size="60"><br>
   Post ID: <input type="text" name="post_id" size="3"><br>
   Parent Comment ID: <input type="text" name="parent_comment_id" size="3"><br>
   Comment body:<br>
   <textarea name="content" cols="50" rows="5"></textarea><br>
  PUT: 
  <input type="checkbox" name="_METHOD" value="PUT"><br>
   <input type="submit" value="Submit">
 </form>
</html>
