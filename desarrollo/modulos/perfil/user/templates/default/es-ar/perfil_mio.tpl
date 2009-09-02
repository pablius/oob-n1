<div id="cont_lista">
	
	<div id="banner_titulo"><p>Inicio</p></div>
	
	<div class="clear_box"></div>
     
	 {if $error}
        <div class="error">
            Error:
            <ul>
                
				{if ($NO_FOTO)}<li>Ha ocurrido un error al intentar cargar su fotograf&iacute;a</li>{/if}
				{if ($NO_MENSAJE)}<li>El mensaje que ha escrito no es correcto.</li>{/if}
				
				
				{if $NO_FILE}
				<li>No ha seleccionado ning&uacute;n archivo</li>
				{/if}
			
				{if $FILE_EXISTS}
				<li>Ya hay un archivo con ese nombre, borre el viejo primero, o carguelo con un nombre distinto.</li>
				{/if}
			
				{if $NOT_A_FILE}
				<li>No hay archivo para subir.</li>
				{/if}
			
				{if $NOT_ALLOWED}
				<li>Esa clase de archivos no est&aacute; permitida. Solo se permiten im&aacute;genes en los perfiles.</li>
				{/if}
			
				{if $UNEXISTANT}
				<li>El archivo no existe.</li>
				{/if}
			
				{if $CONFIG}
				<li>El sistema est&aacute; teniendo problemas con su archivo, reintentelo m&aacute;s tarde.</li>
				{/if}
			

            </ul> 
        </div>
	{/if}

	
	<div class="box_nocontenedores">
		
		<div id="bienvenidos">
			<div id="foto"><img src="{$foto}"/></div>
			<div id="texto">

			<i>¡Hola {$nombre}! Bienvenido a TalkMee</i>
				<br/>
				<h1>¿Olvidaste compartir algo desde tu móvil?
					Puedes compartir tu contenido desde aqu&iacute;
				</h1>

				<form action="" method="post" enctype="multipart/form-data" name="form1">
				
					<input name="file" type="file" id="file" value="Examinar..."/>
					<br/>
					<textarea name="mensaje" cols="60"></textarea>
					<br/>
					<input type="submit" value="Publicar"/> 
				
				</form>	 
			
			</div>
		
			<br />
		</div>
		{if $novedades}
		<div id="site_map_string"><span class="text_string">Inicio></span><span class="text_string2">Novedades</div>
		<div id="contenido_principal">
			<div id="novedad_string">
				<ul  >
				{section name=n loop=$novedades}
					<li class="{$novedades[n].class}">
						<span class="texto_ahora_te_sigue"><a href="{$novedades[n].link}">{$novedades[n].nombre}</a> {$novedades[n].mensaje}</span>
						</li>
				{/section}
				</ul>
			</div>
		</div>
		{/if}	
	</div>
	<div class="clear_box"></div><br/>
</div> 
 