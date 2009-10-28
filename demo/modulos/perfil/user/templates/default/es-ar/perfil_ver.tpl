<div id="cont_lista">
	
	<div id="banner_titulo"><p>Actualizaciones de {$nombre}</p></div>
	
	<div class="clear_box"></div>
     	
	<div class="box_nocontenedores">
	<p><img src="{$foto}" /><br />{$nombre}</p>
	<p>{$bio}</p>
	<p><a href="{$url}">{$url}</a></p>
	
	{if $es_amigo == false}
	<a href="{$webdir}/perfil/amigo/agregar/{$id_perfil}">Agregar a mis amigos</a>
	{/if}
		
	{if $timeline}
	<div id="actualizaciones">
		
		<ul >
		{section name=m loop=$timeline}
			<li >
				{if $timeline[m].foto}<img src="{$timeline[m].foto}" />{/if}
				{$timeline[m].mensaje} <small>({$timeline[m].fecha})</small>
			</li>
		{/section}
		</ul>
	
	</div>
	
	{/if}	
	</div>
	
	<span class="pager">{pager rowcount=$mensajes_count limit=$limit txt_first="" class_num="texto2" class_numon="error" class_text="texto-boton" show='page' txt_prev='anterior' txt_next='siguiente'}</span>

	<div class="clear_box"></div><br/>
</div> 
 