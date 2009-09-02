<form name="login" method="post" action="{$webdir}/seguridad/login">
 <font class="texto3">Entrar</font><br>
<table width="100%"  border="0">
  <tr>
    <td width="50%" valign="top">
   
  <font class="texto6">Usu&aacute;rio</font>
 <br>
  <input name="uname" type="text" id="uname">
  <font class="boxsub"> <br>
  </font><span class="texto6">Senha</span><br>
      <input name="pass" type="password" id="pass" maxlength="8">
 
      <br>
      <input name="login" type="submit" id="login" value="Entrar">
      <br>
  <br>
  - <a href="{$webdir}/seguridad/forgot" class="texto5">Esqueci de minha senha</a>  
  <br>
  - <a href="{$webdir}/seguridad/nuevo" class="texto5"> Usu&aacute;rio novo</a></td>
    </tr>
</table>
  <input name="sourceurl" type="hidden" id="sourceurl" value="{$sourceurl}">
</form>
