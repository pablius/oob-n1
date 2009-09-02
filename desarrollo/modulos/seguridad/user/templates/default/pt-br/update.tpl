<span class="texto2">&Agrave; senha do update</span>
<hr class="barra">
<table width="100%"  border="0" cellspacing="5" cellpadding="5">
  <tr>
    <td class="texto"><form name="update" method="post" action="{$webdir}/seguridad/update">
  <table width="100%"  border="0" cellpadding="5">
  <tr>
    <td valign="top">
<font class="texto5"> Aqui pode atualizar os dados de seu cliente do usu&aacute;rio.  						Se &uacute;nico quiser atualizar seu correio eletr&ocirc;nico, n&atilde;o &eacute;  necess&aacute;rio que termina os campos da senha..</font><br>
<br>
{if $error}
	   <div class="error">
         <ul><font class="error">Erro:</font>
         {if $INVALID_PASS}     <li> Senha inv&aacute;lida (  						4 a 8 car&aacute;teres alfanum&eacute;ricos)</li>{/if}
         {if $NO_CONCUERDAN}      <li> As senhas n&atilde;o concordam</li>{/if}
  		 {if $INVALID_EMAIL}            <li> Introduziu um sentido inv&aacute;lido do correio eletr&ocirc;nico</li>{/if}
         </ul>
       </div><br>
        {/if}

	 <font class="texto1"><br>
	 <span class="texto6">Usu&aacute;rio</span><br>
          <input name="uname"  type="text" id="uname" value="{$uname}" readonly="true">
<br>
    <font class="texto1"><br> 
    <span class="texto6">Senha</span><br />
    <input name="pass" class="degra-box"  type="password" id="pass" maxlength="8">
          <input name="passtwo" class="degra-box"  type="password" id="passtwo" value="{$original}" maxlength="8">
<br>
<font class="small"> A senha deve entrar duas vezes, para valid&aacute;-lo)</font>
<font class="texto1"> <br>
<span class="texto6"><br>
Correio eletr&ocirc;nico</span><br>
<input name="email" class="degra-box"  type="text" id="email" value="{$email}"> 
</h2>
<br>
      <input name="id" type="hidden" value="{$id}">
      <br>
      <br>
      <input name="update" type="submit" id="update" value="Update"></td>
  </tr>
</table>

</form></td>
  </tr>
</table>

