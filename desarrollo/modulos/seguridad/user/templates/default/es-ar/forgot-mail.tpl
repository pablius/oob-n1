<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd ">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Contrase&ntilde;a Olvidada</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<link rel="stylesheet" type="text/css" href="{$webdir}/perspectives/default/css/mails.css" />
</head>

<body>
<div id="lp_container">

	<div id="head">
		<img src="http://eximius.dev/images/enca-logo-2.jpg" width="266" height="79" alt="Recuperar contrase&ntilde;a" />
	</div>

	<div id="body">

		<h1>Has solicitado cambiar tu contrase&ntilde;a</h1>

		Para hacerlo, necesitas ingresar estos datos:

		<ul>
			<li>C&oacute;digo de Validaci&oacute;n: <span>{$code}</span></li>
			<li>Correo Electr&oacute;nico: <span>{$email}</span></li>
			<li class="lower">Te recordamos que tu nombre de usuario es: <span>{$uname}</span></li>
		</ul>

		Haz clic <a href="{$webdir}/seguridad/forgot/update/{$code}">aqu&iacute;</a> para cambiar la contrase&ntilde;a<br/>
		<br/>
		Muchas Gracias<br/>
		<br/>

	</div>

</div>

</body>
</html>