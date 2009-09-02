<h2 class="texto2">&nbsp;Password Recover </h2>
  <hr class="barra">
 {if $first}
<form name="recover" method="post" action="{$webdir}/seguridad/forgot/update">
<table width="100%"  border="0" cellpadding="10">
  <tr>
    <td class="texto" valign="top">
 {if $error}

  <div class="error">
  <ul> <font class="error">Error:</font>
   <li>Unregistered E-mail </li>
 </ul>  </div> <br />
 {/if}Enter the registered e-mail:<br />
 <br />

    <font class="texto1"> E-mail  <br>
      <input name="email" type="text" id="email">
</font><br /><br />
  <input name="recover" type="submit" id="recover" value="Recover">
    </td>
  </tr>
</table>
</form>
{else}
<form name="validar" method="post" action="{$webdir}/seguridad/forgot/update">
<table width="100%"  border="0" cellspacing="10">
  <tr>
    <td class="texto" valign="top">
 {if $posted}
  <div class="texto6" style="border:solid; border-width:1px; padding:5px;">
    <div align="center">An e-mail with the validation code has been sent </div>
  </div>
 {/if}
 {if $error}
 <br />
  <div class="error">
  <ul> <font class="error">Error:</font>
    {if $INVALID_CODE}  <li>Code of Validation is invalid or has expired </li> 
    {/if}
	{if $INVALID_EMAIL}  <li>Invalid e-mail </li> 
	{/if}
    {if $INVALID_PASSWORD} <li>Invalid new password (4 a 8 characters alfanumerics)</li> {/if}
  {if $INVALID_PASSWORD_MATCH} <li>The passwords doesn&acute;t match</li> {/if}
  </ul>  
  </div>
  {/if}
  <br />
  <span class="texto5">Enter your data to validate your identity and to update password :</span>
    <br />
	  <br />
   <font class="texto1">Validation Code <br>
  <input name="code" type="text" id="code" value="{$code}"></font><br><br>
     <font class="texto1">e-mail<br>
      <input name="email" type="text" id="email" value="{$email}"></font><br><br>
        <font class="texto1">New Password <br>
<input name="pass" type="password" id="pass" maxlength="8">
<input name="passtwo" type="password" id="passtwo" maxlength="8">
</font><br>
<font class="texto5">(Password must enter twice, to validate it )</font>
  <br>
  <br>
  <input name="update" type="submit" id="update" value="Update">
    </td>
  </tr>
</table>
</form>
{/if}