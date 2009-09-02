<!-- INICIO SEGURIDAD -->
<h2 class="title">Seguridad</h2>

<div class="clearbox"></div>
	{if $login}

	<!-- SEGURIDAD: Left Box -->

		<div class="midbox">

			<div class="hzl"></div>
				<p class="frame">
					Por favor ingresa tu nombre de usuario y contrase&ntilde;a. Solo los usuarios registrados pueden acceder a nuestra base de datos.

					{if $register}

					{else}
						Si a&uacute;n no posees una cuenta reg&iacute;strate gratuitamente </span><span class="texto6"><a class="texto" href="{$webdir}/seguridad/nuevo">aqu&iacute;</a>.
					{/if}
				</p>
			<div class="hzl"></div>

			<form class="seguridad" name="login" method="post" action="{$webdir}/seguridad/login">

				{if $error}
					<div class="error">
						Error:
						<ul>
							<li>Usuario o contrase&ntilde;a no v&aacute;lida</li>
						</ul> 
					</div>
				{/if}

				<div>Usuario</div>
				<input type="text" name="uname" value="" size="40" alt="Usuario" class="input_text" id="uname" />

				<br/>
				<br/>

				<div>Contrase&ntilde;a</div>
				<input type="password" name="pass" value="" size="40" alt="Usuario" class="input_text" id="pass" maxlength="12" />
				
				<br>
				<br>

				<input name="login" type="submit" id="login" class="button" value="Ingresar">
				<input name="forgot" type="submit" id="forgot" class="button" value="Olvid&eacute; mi contrase&ntilde;a">

			</form>

		</div>

	{/if}



	{if $register}

	<!-- SEGURIDAD: Right Box -->

		<div class="midbox">

			<div class="hzl"></div>
				<p class="frame">Si no posee un usuario y contrase&ntilde;a, puede registrarse a continuaci&oacute;n:</p>
			<div class="hzl"></div>

			<form class="seguridad" name="login" method="post" action="{$webdir}/seguridad/nuevo">

				{if $error}

					<div class="error">
						Error:
						<ul>
							{if $INVALID_USER} <li>El Usuario o Correo Electr&oacute;nico, ya est&aacute; registrado o es inv&aacute;lido</li> {/if}

							{if $INVALID_PASS} <li>Contrase&ntilde;a inv&aacute;lida (4 a 8 caracteres alfanum&eacute;ricos)</li> {/if}

							{if $NO_CONCUERDAN} <li>Las contrase&ntilde;as no concuerdan</li>{/if}

							{if $INVALID_EMAIL} <li>Ha introducido una direcci&oacute;n de Correo Electr&oacute;nico inv&aacute;lida</li> {/if}

							{if $INVALID_condiciones} <li>Debe aceptar los T&eacute;rminos y Condiciones para poder almacenar su usuario.</li> {/if}	
						</ul>
					</div>
				{/if}


				<div>Usuario</div>
				<input type="text" name="uname" value="" size="50" alt="Usuario" class="input_text" id="uname" />

				<br/>
				<br/>

				<div>Contrase&ntilde;a</div>
					<input name="pass" type="password" class="input_text" id="pass" maxlength="8" alt="Password" />
					<input name="passtwo" type="password" class="input_text" id="passtwo" maxlength="8" alt="Password" />
				<br/>
				<span>(debe ingresar la contrase&ntilde;a dos veces, para validarla)</span><br/>

				<br>
				<br>

				<div>Correo Electr&oacute;nico</div>
				<input  name="email" type="text" class="input_text" id="email" value="{$newemail}" size="100">

				<br>
				<br>

				<input type="checkbox" name="condiciones" value="checkbox">

				<span>
					Acepto los <a href="{$webdir}/about/uso" target="_blank">t&eacute;rminos y condiciones del servicio</a>
				</span>
				
				<br>
				<br>
				<br>

				<input name="register" type="submit" id="register" class="button" value="Registrarse">

			</form>

		</div>

	{/if}

<!-- FIN SEGURIDAD -->