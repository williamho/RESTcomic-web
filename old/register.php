<?php
require_once 'includes/common.php';
?>
<html>
<?php
if (isset($_SESSION['returned'])) {
	echo $_SESSION['returned'];
	unset($_SESSION['returned']);
}

	require_once 'includes/userinfo.php';
if (0) {
//if ($_SESSION['user_id'] !== 0) {
	require_once 'includes/userinfo.php';

	echo 'You are already logged in';
}
else {
?>

<form action="oauth.php?<?php 
	echo getAPIQuery('/api/users','POST',getCurrentURI()); 
	?>" method="post">
<fieldset>
<legend>User Info</legend>
  User ID: <input type="text" name="user_id" size="5"><br>
<label for='username' >Username</label>
<input type='text' name='username' id='username' maxlength="24"  /><br>

<label for='password' >Password</label>
<input type='password' name='password' id='password' maxlength="50"  /><br>

<label for='confirmpassword' >Confirm Password</label>
<input type='password' name='confirmpassword' id='confirmpassword' maxlength="50"  /><br>
</fieldset>

<fieldset>
<legend>Optional information</legend>
<label for='name' >Name</label>
<input type='text' name='name' id='name' maxlength="32" /><br>

<label for='email' >Email</label>
<input type='text' name='email' id='email' maxlength="100" /><br>

<label for='website' >Website</label>
<input type='text' name='website' id='website' maxlength="1024" /><br>
</fieldset>

<fieldset>
PUT
<input type="checkbox" name="_METHOD" value="PUT"><br>
<legend>Submit</legend>
<input type='submit' name='Submit' value='Submit' />
</fieldset>
</form>
<?php 
}
?>
</html>
