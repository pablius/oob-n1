<h2 class="texto2">&nbsp;Recuperar a Senha</h2>
  <hr class="barra">
 {if $first}
<form name="recover" method="post" action="{$webdir}/seguridad/forgot/update">
<table width="100%"  border="0" cellpadding="10">
  <tr>
    <td class="texto" valign="top">
 {if $error}

  <div class="error">
  <ul> <font class="error">Erro:</font>
   <li> Correio eletr&ocirc;nico n&atilde;o registado</li>
 </ul>  
  </div> <br />
 {/if}
  <span class="texto5"> O correio eletr&ocirc;nico entra em seguida com qual foi registado:</span><br /> 
  <br />

    <font class="texto1"> Correio eletr&ocirc;nico<br>
      <input name="email" type="text" id="email">
</font><br />
<br />
  <input name="recover" type="submit" id="recover" value="Recuperar">
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
    <div align="center">Um correio eletr&ocirc;nico com<span class="texto6"> C&oacute;digo</span> validation foi emitido a su e-mail ,  ter&aacute; que chegar-lhe logo.</div>
  </div>
 {/if}
 {if $error}
 <br />
  <div class="error">
  <ul> <font class="error">Erro:</font>
    {if $INVALID_CODE}  <li> O c&oacute;digo do validation &eacute; inv&aacute;lido ou expirou</li> 
    {/if}
	{if $INVALID_EMAIL}  <li> O correio eletr&ocirc;nico &eacute; inv&aacute;lido </li> 
	{/if}
    {if $INVALID_PASSWORD} <li> A senha nova &eacute; inv&aacute;lida (  						Mesmo deve ter 4 a 8 car&aacute;teres alfanum&eacute;ricos)</li> {/if}
  {if $INVALID_PASSWORD_MATCH} <li> As senhas n&atilde;o concordam</li> {/if}
  </ul>  
  </div>
  {/if}
  <br />
  <span class="texto5"> Incorpore em seguida seus dados para validar sua identidade e para  atualizar a senha:</span>
    <br />
	  <br />
   <font class="texto1"> C&oacute;digo do validation<br>
  <input name="code" type="text" id="code" value="{$code}"></font><br>
  <br>
     <font class="texto1"> Correio eletr&ocirc;nico<br>
      <input name="email" type="text" id="email" value="{$email}"></font><br>
      <br>
        <font class="texto1"> Senha Nova<br>
<input name="pass" type="password" id="pass" maxlength="8">
<input name="passtwo" type="password" id="passtwo" maxlength="8">
</font><br>
<font class="texto5">(  						A senha deve entrar duas vezes, para valid&aacute;-lo)</font>
  <br>
  <br>
  <input name="update" type="submit" id="update" value="Update">
    </td>
  </tr>
</table>
</form>
{/if}