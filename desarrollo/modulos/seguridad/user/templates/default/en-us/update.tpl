<span class="texto2">Password Update </span>
<hr class="barra">
<table width="100%"  border="0" cellspacing="5" cellpadding="5">
  <tr>
    <td class="texto"><form name="update" method="post" action="{$webdir}/seguridad/update">
  <table width="100%"  border="0" cellpadding="5">
  <tr>
    <td valign="top">
<font class="texto5">You can refresh your login data. If you only want to update your e-mail is not necesary to complete the password fields</font><br>
<br>
{if $error}
	   <div class="error">
         <ul><font class="error">Error:</font>
         {if $INVALID_PASS}     <li>Invalid password (4 a 8 characters alfanumerics)</li>
         {/if}
         {if $NO_CONCUERDAN}      <li>The passwords doesn&acute;t match</li>
         {/if}
  		 {if $INVALID_EMAIL}            <li>Invalid e-mail  </li>
  		 {/if}
         </ul>
       </div><br>
        {/if}

	 <font class="texto1"><br>
	 <span class="texto6">User</span><br>
          <input name="uname"  type="text" id="uname" value="{$uname}" readonly="true">
<br>
    <font class="texto1"><br> 
    <span class="texto6">Password</span><br>

          <input name="pass" class="degra-box"  type="password" id="pass" maxlength="8">
          <input name="passtwo" class="degra-box"  type="password" id="passtwo" value="{$original}" maxlength="8">
<br>
<font class="small">(you must enter your password two times)</font>
<font class="texto1"> <br>
<span class="texto6"><br> 
E-mail
</span><br>
<input name="email" class="degra-box"  type="text" id="email" value="{$email}"> 
</h2>
<br>
      <input name="id" type="hidden" value="{$id}">
      <br>
      <br>
      <input name="update" type="submit" id="update" value="Refresh"></td>
  </tr>
</table>

</form></td>
  </tr>
</table>

