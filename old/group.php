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
	echo getAPIQuery('/api/groups','POST',getCurrentURI()); 
	?>" method="post">
<fieldset>
<legend>Group Info</legend>
  Group ID: <input type="text" name="group_id" size="5"><br>
<label for='name' >Name</label>
<input type='text' name='name' id='name' maxlength="64" required /><br>

<label for='color' >Color</label>
<input type='text' name='color' id='color' maxlength="7" /><br>
</fieldset>
<fieldset>
<legend>Permissions</legend>
<label>Admin <input type='checkbox' name='admin_perm' value='1'></label>
<br>
<label>
make post
  <select name="make_post_perm">
   <option value="0">No</option>
   <option value="1">Hidden</option>
   <option value="2">Yes</option>
  </select>
</label><br>
<label>edit post</label>
  <select name="edit_post_perm">
   <option value="0">None</option>
   <option value="1">Own</option>
   <option value="2">Group</option>
   <option value="3">All</option>
  </select>
<br>
<label>make comment</label>
  <select name="make_comment_perm">
   <option value="0">No</option>
   <option value="1">Hidden</option>
   <option value="2">Yes</option>
  </select>
<br>
<label>edit comment</label> 
<select name="edit_comment_perm">
   <option value="0">None</option>
   <option value="1">Own</option>
   <option value="2">Group</option>
   <option value="3">All</option>
  </select>
<br>
</fieldset>

<fieldset>
PUT
<input type="checkbox" name="_METHOD" value="PUT"><br>
<legend>Submit</legend>
<input type='submit' name='Submit' value='Submit' />
</fieldset>
</form>
</html>

