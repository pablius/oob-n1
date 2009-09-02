
{if $logued} 

	<div id="logged_in_box">
		Bienvenido <span>{$uname}</span> (<a href="{$webdir}/seguridad/logout">salir</a> | <a href="{$webdir}/seguridad/update">actualizar contrase&ntilde;a</a>)
	</div>

{else}

	<form name="login" method="post" action="{$webdir}/seguridad/login">
		<div id="login_box">
			<ul>
				<li>
					<a href="/seguridad/login"><img src="{$webdir}/images/log.jpg" width="20" height="20" border="0" alt="Log" />
				</li>

				<li>
					<a href="/seguridad/forgot"><img src="{$webdir}/images/forgot.jpg" width="20" height="20" border="0" alt="Recuperar Contraseña" /></a>	
				</li>

				<li>
					<input name="login" id="login" class="button" type="submit" value="Login" alt="Login" />
				</li>

				<li class="table_sep"></li>

				<li>
					<input name="pass" class="input_text" type="password" id="pass" size="15" value="" alt="Contraseña" />
				</li>

				<li class="table_sep"></li>

				<li>
					<span>Contrase&ntilde;a</span>&nbsp;
				</li>

				<li class="table_sep"></li>

				<li>
					<input name="uname" class="input_text" type="text" id="uname" size="15" value="" alt="Usuario" />
				</li>

				<li class="table_sep"></li>

				<li>
					<span>Nombre</span>&nbsp;
				</li>
			</ul>
		</div>

		<div class="clear_box"></div>
	</form>
{/if}