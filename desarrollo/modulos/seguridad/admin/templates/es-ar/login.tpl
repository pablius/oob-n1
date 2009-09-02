<form name="login" method="post" action="{$webdir}/seguridad/login">
{$formElement}
<table width="510" height="283"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="100%" class="bg-login">
      <p><br>
	  <br>
        <br>
        <br>
      </p>

      <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td>
		
		<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
          <tr valign="top">
            <td  colspan="2" class="texto"><br>
        {if $error} 
		<div class="error">
				{if $SENT_DUPLICATE_DATA}
					Los datos de este fomulario ya han sido enviados<br /> para su procesamiento.
				{else}
					Usuario o contrase&ntilde;a no v&aacute;lida.<br />
				{/if}		</div><br />
				{else}<br /><br />
	{/if}		</td>
		
          </tr>
          <tr>
            <td class="style5"><strong class="texto">Usuario</strong></td>
            <td class="texto">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><input name="uname" type="text" id="uname"></td>
          </tr>
          <tr>
            <td class="style5"><strong class="texto">Password</strong></td>
            <td class="texto">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><input name="pass" type="password" id="pass" >          </tr>
          <tr>
            <td width="18%" height="33" valign="middle"><input name="login" type="submit" class="submitbutton" value="Ingresar" /></td>
            <td ></td>
          </tr>
        </table>
		
		
		</td>
      </tr>
    </table>
		{$formElement}
	
</td>
  </tr>
</table>
</form>

