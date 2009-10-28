<link href="{$webdir}/oob/admin/estilo.css" rel="stylesheet" type="text/css">

<form name="login" method="post" action="{$webdir}/seguridad/login">
<font class="texto3">Ingresar</font><br />
<table width="100%"  border="0">
	<tr>
		<td width="50%" valign="top">
   
			<font class="texto6">Usuario</font><br/>
			<input name="uname" type="text" id="uname" alt="usuario" /> <span class="texto6">Contrase&ntilde;a</span><br/>
			<input name="pass" type="password" id="pass" maxlength="8" alt="password" />
 
			<br/>

			<input name="login" type="submit" id="login" value="Ingresar" /><br/>

			<br />

			- <a href="{$webdir}/seguridad/forgot" class="texto5" title="Recuperar Contrase&ntilde;a">Olvid&eacute; mi contrase&ntilde;a</a><br/>

			- <a href="{$webdir}/seguridad/nuevo" class="texto5" title="Nuevo Usuario">Nuevo usuario</a>
		</td>
	</tr>
</table>

<input name="sourceurl" type="hidden" id="sourceurl" value="{$sourceurl}" />

</form>
