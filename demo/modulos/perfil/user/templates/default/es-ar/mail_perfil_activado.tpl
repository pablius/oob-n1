<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<!-- Style Sheets -->
	<link id="main_css" href="http://reservas.chasmatours.com/perspectives/default/css/main_styles.css" rel="stylesheet" type="text/css" />
	<link id="design_css" href="http://reservas.chasmatours.com/perspectives/default/css/design_style.css" rel="stylesheet" type="text/css" />
</head>

<body>
	<div id="cuerpo_mail">
		<div id="logo_chasma_mail"><img src="{$webdir}/images/logo_mail.jpg" /></div>
		

<div id="cont_lista">
	<h2>Operador Activado</h2>
	<div class="clear_box"></div>
	<div class="hzl"></div>
	 El Operador ha sido activado. <br/>Ya puede Operar con nuestro sistema.
	<div class="hzl"></div>
	
	<div class="box_nocontenedores">
		
			<table width="100%" border="0" cellspacing="5">
			  <tr>
				<td colspan="3" bgcolor="#89A9C2">Datos Personales</td>
			  </tr>
			  <tr>
				<td colspan="2"><b>Nombre</b> <br>
					{$nombre}
				  </td>
				<td><b>Apellido</b><br>
				 {$apellido}
				 </td>
			  </tr>
			 			 
			   <tr>
			   <td colspan="3"><b>Usuario</b><br>
				  {$usuario}</td>
			  </tr>
			 
			 <tr>
				<td colspan="3" bgcolor="#89A9C2">Agencia</td>
			</tr>
				
				<tr>
				<td colspan="3">
					<b>{$nombre_agencia}</b> ({$cuit})
				</td>
			  </tr>
			
			</table>
	
	</div>
	<div class="clear_box"></div>
</div> 
 
		
		<!-- FOOTER  -->
		<div id="pie_mail">
		<b>Chasma Tours</b>&nbsp;|&nbsp;25 de Mayo 66, 1er Piso, Oficina 6 - C.P.5000 - C&oacute;rdoba, Argentina<br/>
					  Tel. +54-351-4110027 rot. - Fax. +54-351-4110029 - e-mail: <a href="mailto:info@chasmatours.com" target="#">info@chasmatours.com</a>
		</div>
		<div style="clear:both; "></div> 

	</div>
</body>
</html>