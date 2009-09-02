<link href="{$webdir}/oob/admin/estilo.css" rel="stylesheet" type="text/css">
<form name="login" method="post" action="{$webdir}/seguridad/login">
 <font class="texto3">Enter</font><br>
<table width="100%"  border="0">
  <tr>
    <td width="50%" valign="top">
   
  <font class="texto6">User</font>
 <br>
  <input name="uname" type="text" id="uname">
  <font class="boxsub"> <br>
  </font><span class="texto6">Password</span><br>
      <input name="pass" type="password" id="pass" maxlength="8">
 
      <br>
      <input name="login" type="submit" id="login" value="Send">
      <br>
  <br>
  - <a href="{$webdir}/seguridad/forgot" class="texto5">Forggot my password </a>  
  <br>
  - <a href="{$webdir}/seguridad/nuevo" class="texto5">New User </a></td>
    </tr>
</table>
  <input name="sourceurl" type="hidden" id="sourceurl" value="{$sourceurl}">
</form>
