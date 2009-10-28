<div id="cont_lista">
	
	<div id="banner_titulo"><p>Mis actualizaciones</p></div>
	
	<div class="clear_box"></div>
     	
	<div class="box_nocontenedores">
		
	{if $mensajes}
	<div id="actualizaciones">
		
		<ul >
		{section name=m loop=$mensajes}
			<li >
				{if $mensajes[m].foto}<img src="{$mensajes[m].foto}" />{/if}
				{$mensajes[m].mensaje} <small>({$mensajes[m].fecha})</small>
				<a href="/perfil/mensaje/borrar/{$mensajes[m].id}">borrar</a>
			</li>
		{/section}
		</ul>
	
	</div>
	
	{/if}	
	</div>
	
	<span class="pager">{pager rowcount=$mensajes_count limit=$limit txt_first="" class_num="texto2" class_numon="error" class_text="texto-boton" show='page' txt_prev='anterior' txt_next='siguiente'}</span>

	<div class="clear_box"></div><br/>
</div> 
 