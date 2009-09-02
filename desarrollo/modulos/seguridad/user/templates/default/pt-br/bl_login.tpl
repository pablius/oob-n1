{if $logued} <span class="texto6"> Boa vinda</span> <span class="texto6">{$uname}</span><font class="style1"> (<a class="texto5" href="{$webdir}/seguridad/logout">sair</a> | update senha)  </font> {else}
<form name="login" method="post" action="{$webdir}/seguridad/login">
  <table width="100%" border="0" align="right" cellpadding="0" cellspacing="0" class="fondo2">
  <tr>
    <td valign="bottom">
      <div align="right"><span class="texto6">Usu&aacute;rio&nbsp;</span><font class="style1">
        <input name="uname" class="portada_box" type="text" id="uname" size="15">
  &nbsp;&nbsp;
           </font><span class="texto6">Senhaa</span><font class="style1"> &nbsp;
           <input name="pass" class="portada_box"  type="password" id="pass" size="15" maxlength="8">
  &nbsp;
           <input name="login" type="submit" id="login" class="portada_button"  value="Emitir ">
  &nbsp;
  &nbsp;    </font>        </div>  </td>
    <td valign="middle"><div align="center"><a href="/seguridad/forgot"><img src="{$webdir}/images/forgot.jpg" width="20" height="20" border="0" /></a></div></td>
    <td valign="middle"><div align="center"><a href="/seguridad/login"><img src="{$webdir}/images/log.jpg" width="20" height="20" border="0" /></a></div></td>
  </tr>  
</table>
</form>
  

{/if}

