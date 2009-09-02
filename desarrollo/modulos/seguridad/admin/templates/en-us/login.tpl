
 <h1>Login</h1>
  <hr class="barra">

<table width="100%"  border="0">
  <tr>{if $login}
    <td width="50%" valign="top">
	<form name="login" method="post" action="{$webdir}/seguridad/login">
  Insert your login information:
{if $error} 
    <div class="error">
<ul> <font class="error">Error:</font>
   <li>User or password are invalid</li>
 </ul>  </div>
 {/if}
 
  <h2>User<br>
  <input name="uname" type="text" id="uname"></h2>
    <h2> Password <br>
      <input name="pass" type="password" id="pass" maxlength="8">
  </h2>
  <input name="login" type="submit" id="login" value="Login">

  </form>
  </td>{/if}
 
  </tr>
</table>


