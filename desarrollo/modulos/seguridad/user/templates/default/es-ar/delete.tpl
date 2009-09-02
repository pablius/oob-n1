<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd ">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Eliminar cuenta de usuario</title>
	<meta http-equiv="Content-Type" content="text/html; charset={$encoding}" />

	<!-- Style Sheets -->
	<link href="{$webdir}/perspectives/default/css/popups.css" rel="stylesheet" type="text/css" />

	<!-- Java Scripts -->
</head>
<body>

	<div id="container">

		<div id="top-cerrar">
			<a href="javascript: window.close();">Cerrar</a>
		</div>

		<h2>Borrar Usuario</h2>

		<br/>

		{if $error}
			<div id="error">
				ERROR:
				<ul>
					{if $ALREADY_DELETED}
						<li>El usuario ya ha sido eliminado.</li>
					{/if}
					{if $NO_SUCH_USER}
						<li>El usuario no existe.</li>
					{/if}
				</ul> 
			</div>
		{/if}

		<form name="delete" id="delete" method="post">

			<div id="center">

				<span>{$userName}</span>: &iquest;Est&aacute; seguro que desea eliminar su cuenta de usuario?<br/>

				<br/>
				<br/>

				<input name="si" type="submit" value="Si" class="button" />
				<input name="No" type="button" value="No" onClick="window.close()" class="button" /> 

			</div>
			  
			<br>
			<br>
				
			{if $close}
				<script language="javascript" type="text/javascript">
					window.close(); 
					window.opener.location = "{$webdir}";
				</script>
			{/if}	
			
		</form>
	</div>

</body>
</html>