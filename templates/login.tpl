{include file="header.tpl" title=Login} {include file="messagebox.tpl"}
<table>
	<form method="POST" action="login.php">
	
	
	<tr>
		<td>Username</td>
		<td><input type="text" name="username" /></td>
	</tr>
	<tr>
		<td>Password</td>
		<td><input type="password" name="password" /></td>
	</tr>
	<tr>
		<td><input type="submit" value="Login" /></td>
	</tr>


	</form>
</table>
<a href="login.php?action=register">Register</a>

{include file="footer.tpl"}
