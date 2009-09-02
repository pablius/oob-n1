<h2 class="title">Recuperar Contrase&ntilde;a</h2>
<div class="hzl"></div>

{if $first}


	<p>Ingrese a continuaci&oacute;n el Correo Electr&oacute;nico con el que se registr&oacute;:</p>

	<br/>
	<form class="seguridad" name="recover" method="post" action="{$webdir}/seguridad/forgot/update">

		{if $error}
		<div class="error" style="display: none;">
			Error:
			<ul>
				<li>Correo electr&oacute;nico no registrado</li>
			</ul> 
		</div>
		{/if}

		<div>Correo Electr&oacute;nico</div>
		<input name="email" type="text" size="50" class="input_text" id="email" alt="e-mail" alt="e-mail" />


		<br />
		<br />

		<input name="recover" type="submit" class="button" id="recover" value="Recuperar" />						

	</form>


{else}


	{if $posted}
		<p class="notice">Se ha enviado un Correo Electr&oacute;nico con su codigo de validaci&oacute;n, el mismo deber&aacute; llegarle en breve.</p>
		<div class="hzl"></div>
	{/if}

	<form class="seguridad" name="validar" method="post" action="{$webdir}/seguridad/forgot/update">

		{if $error}

		<div class="error" style="display: none;">
			Error:

			{if $INVALID_CODE}

				{if $INVALID_EMAIL}<li>El Correo Electr&oacute;nico es inv&aacute;lido</li>{/if}

				{if $INVALID_PASSWORD}<li>La nueva contrase&ntilde;a no es v&aacute;lida (la misma debe tener 4 a 8 caracteres alfanum&eacute;ricos)</li>{/if}

				{if $INVALID_PASSWORD_MATCH}<li>Las contrase&ntilde;as no concuerdan</li>{/if}
			{/if}
		</div>

		{/if}


		<p>Ingrese a continuaci&oacute;n sus datos para validar su identidad y actualizar la contrase&ntilde;a:</p>

		<br />
		<br />

		<div>C&oacute;digo de Validaci&oacute;n</div>
		<input name="code" type="text" class="input_text" id="code" size="30" value="{$code}" alt="code" />
		
		<br />
		<br />

		<div>Correo Electr&oacute;nico</div>
		<input name="email" type="text" class="input_text" id="email" size="30" value="{$email}" alt="e-mail" />

		<br />
		<br />

		<div>Nueva Contrase&ntilde;a</div>
		<input name="pass" type="password" class="input_text" id="pass" size="30" maxlength="8" alt="password" />
		<input name="passtwo" type="password" class="input_text" id="passtwo" size="30" maxlength="8" alt="password" />
						
		<br />

		<span>(debe ingresar la contrase&ntilde;a dos veces, para validarla)</span>
		<br />
		<br />

		<input name="update" type="submit" class="button" id="update" value="Actualizar" />

	</form>

{/if}