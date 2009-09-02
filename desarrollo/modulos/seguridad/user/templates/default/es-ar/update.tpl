
		<h2 class="title">Actualizar Contrase&ntilde;a</h2>

		<div class="hzl"></div>

		<p class="frame">Aqu&iacute; puede actualizar los datos de su cuenta de usuario. Si solo quiere actualizar su correo electr&oacute;nico, no es necesario que complete los campos de contrase&ntilde;a.</p>

		<br/>
		<br/>

		{if $error}
			<div class="error">

				Error:
				<ul>
				{if $INVALID_PASS}	<li>Contrase&ntilde;a inv&aacute;lida (4 a 8 caracteres alfanum&eacute;ricos)</li> {/if}
				{if $NO_CONCUERDAN}	<li>Las contrase&ntilde;as no concuerdan</li> {/if}
				{if $INVALID_EMAIL}	<li>Ha introducido una direcci&oacute;n de Correo Electr&oacute;nico inv&aacute;lida</li> {/if}
				</ul> 

			</div>
		{/if}

		<form class="seguridad" name="update" method="post" action="{$webdir}/seguridad/update">

			<div>Usuario</div>
			<input type="text" name="uname" value="{$uname}" readonly="readonly" size="40" class="input_text" id="uname" alt="Usuario" />

			<br/>
			<br/>

			<div>Contrase&ntilde;a</div>
			<input type="password" name="pass" value="" size="40" class="input_text" id="pass" maxlength="12" alt="Password" /> 
			<input type="password" name="passtwo" value="{$original}" size="40" class="input_text" id="passtwo" maxlength="12" alt="Password2" />

			<br/>

			<span>(debe ingresar la contrase&ntilde;a dos veces, para validarla)<span>

			<br>
			<br>

			<div>Correo Electr&oacute;nico</div>
			<input type="text" name="email" class="input_text" id="email" size="40" value="{$email}" alt="E-Mail" />

			<input name="id" type="hidden" value="{$id}">

			<br/>
			<br/>

			<input class="button" name="update" type="submit" id="update" value="Actualizar" alt="Actualizar" />

		</form>

		<br />
		<br />

		{oob_block module="seguridad" block="user_delete"}
