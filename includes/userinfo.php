<?php 
require_once 'includes/common.php';

if (isset($_SESSION['error'])) {
	echo $_SESSION['error'];
	unset($_SESSION['error']);
}

if ($_SESSION['user_id'] === 0) { ?>

<form id='login' action='login.php?return=<?php echo getCurrentURI(); ?>' method='post'>
<fieldset>
<legend>Login</legend>
<input type='hidden' name='login' id='login' value='1'/>
<label for='username' >Username</label>
<input type='text' name='username' id='username' maxlength="24" required />
<br>
<label for='password' >Password</label>
<input type='password' name='password' id='password' maxlength="50" required />
<br>
<input type='submit' name='Submit' value='Submit' />
</fieldset>
</form>

<?php } 
else {
echo $_SESSION['user_name'];
?>
<form id='logout' action='logout.php?return=<?php echo getCurrentURI(); ?>' method='post'>
<input type='submit' name='logout' value='Log out' />
</form>
<?php
}
?>
