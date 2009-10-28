{include file="encabezado.tpl"}




	<div id="contenedor">

<!-- HEADER-->
		<div id="header"></div>


<!-- TOOL BAR (tb_*) -->
		<div id="toolbar">
			<img id="toolbar_image" src="{$webdir}/images/arriba.jpg" width="900" height="66" alt="" usemap="" />		
		</div>






<!-- CUERPO DEL SITIO -->
		<div id="body_main">
			<div class="margin_common">

<!-- MENU PRINCIPAL -->
				<div id="menu_container">

					

				</div>


<!-- CONTENIDOS A MOSTRAR -->
				<div id="content">

<!-- LOGIN -->


				{oob_block module="seguridad" block="login" }


<!-- CONTENIDOS A LISTAR -->					

				

						{$maincontent}

<!-- FIN CONTENIDOS -->

				</div>

			</div>
		</div>



{include file="pie.tpl"}
