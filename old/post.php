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
 <h1>Make a post</h1>
 <form action="oauth.php?<?php 
 	echo getAPIQuery('/api/posts','POST',getCurrentURI());
 ?>" method="post">
  Post ID: <input type="text" name="post_id" size="5"><br>
  Post title: <input type="text" name="title" size="30"><br>
  Image URL:<br> 
 <input type="text" name="image_url" size="60"><br>
  Post body:<br>
  <textarea name="content" cols="50" rows="5"></textarea><br>
  Tags (comma-separated):<br> 
 <input type="text" name="tags" size="60"><br>
  Post status:
  <select name="status">
   <option value="0">Visible</option>
   <option value="1">Scheduled</option>
   <option value="2">Invisible</option>
  </select><br>
  Commentable: 
  <input type="checkbox" name="commentable" checked="checked" value="1"><br>
  PUT: 
  <input type="checkbox" name="_METHOD" value="PUT"><br>
  <input type="submit" value="Submit">
 </form>
</html>
