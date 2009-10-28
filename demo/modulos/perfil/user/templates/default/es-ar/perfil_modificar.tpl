<div id="cont_lista">
	
	<div id="banner_titulo"><p>Perfil de Usuario</p></div>
	
	<div class="clear_box"></div>
     
	 {if $error}
        <div class="error">
            Error:
            <ul>
                
				{if ($NO_NOMBRE)}<li>Verifique su Nombre</li>{/if}
				{if ($NO_TELEFONO)}<li>Verifique su N&uacute;mero de Tel&eacute;fono</li>{/if}
				{if ($NO_BIO)}<li>Verifique los datos introducidos en su Mini biografia</li>{/if}
				{if ($NO_URL)}<li>Verifique la direcci&oacute;n de su p&aacute;gina personal</li>{/if}
				{if ($NO_FECHA_NACIMIENTO)}<li>Verifique su Fecha de Nacimiento</li>{/if}
				{if ($DUPLICATED)}<li>Ya existe un usuario con su mismo nombre</li>{/if}
				
				{if ($INVALID_USER)}<li>El nombre de usuario no es v&aacute;lido, o el E-Mail ingresado ya est&aacute; registrado</li>{/if}
				{if ($INVALID_EMAIL)}<li>La direcci&oacute;n de E-Mail no es v&aacute;lida</li>{/if}
				{if ($INVALID_PASS)}<li>La contrase&ntilde;a no es v&aacute;lida</li>{/if}
				{if ($NO_CONCUERDAN)}<li>Las contrase&ntilde;as no concuerdan</li>{/if}
				
				{if ($INVALID_CONDICIONES)}<li>Debe aceptar los t&eacute;rminos y condiciones para proceder</li>{/if}

            </ul> 
        </div>
	{/if}
	<div class="hzl"></div>
	
	<div class="box_nocontenedores">
		
		<form method="POST">
		
			<table width="100%" border="0" cellspacing="5">
			  <tr>
				<td colspan="3" bgcolor="#89A9C2">Datos Personales </td>
			  </tr>
			  <tr>
				<td colspan="2">Nombre*<br>
					<input name="nombre" type="text" id="nombre" size="35" value="{$nombre}">
				  </td>
				<td>Fecha de Nacimiento*<br>
				{html_select_date prefix="fecha_nacimiento_" time=$fecha_nacimiento field_order='DMY' start_year=1909 end_year=$end_year}
				</td>
			  </tr>
			  <tr>
				<td colspan="2">e-Mail*<br>
				  <input name="email" type="text" id="email" size="35" value="{$email}"></td>
				<td>Tel&eacute;fono<br>
				  <input name="telefono" type="text" id="telefono" size="35" value="{$telefono}"></td>
			  </tr>
			  <tr>
			   <td colspan="3">Mini-biografia<br><textarea name="bio">{$bio}</textarea></td>
			  </tr>
			  
			   <tr>
			   <td colspan="3">
			   Direcci&oacute;n de su p&aacute;gina web personal o blog<br>
				  <input name="url" type="text" id="url" size="35" value="{$url}">
			   </td>
			  </tr>
			  
			   <tr>
			   <td colspan="3">Usuario*<br>
				  <input name="usuario" type="text" id="usuario" size="30" value="{$usuario}" {if $disable_username_change}disabled{/if} ></td>

				
			  </tr>
			  <tr>
			  <td colspan="2">Contrase&ntilde;a<br>
				  <input name="pass" type="password" id="pass" size="12" value="" autocomplete="off"></td>
				<td>Repetir Contrase&ntilde;a<br>
				  <input name="passtwo" type="password" id="passtwo" size="12" value=""  autocomplete="off"></td>
			  </tr>
			 
			
			<tr>
				<td colspan="3" align="center">
					<input type="checkbox" name="condiciones" value="checkbox">
				<span>
					Acepto los <a href="{$webdir}/about/uso" target="_blank">t&eacute;rminos y condiciones del servicio</a>
				</span>
				<br />
					<input type="submit" name="registro" value="Guardar"/> o <a href="{$webidr}/">cancelar</a>
				</td>
			<tr>
			</table>
		</form>
	
	</div>
	<div class="clear_box"></div><br/>
</div> 
 