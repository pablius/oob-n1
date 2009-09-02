<div id="cont_lista">
	
	<div id="banner_titulo"><p>Seguidores de {$nombre}</p></div>
	
	<div class="clear_box"></div>
     	
	<div class="box_nocontenedores">
		
	{if $seguidores}
	<div id="seguidores">
		
		<ul >
		{section name=s loop=$seguidores}
			<li >
				
				<a href="{$webdir}/perfil/perfil/ver/{$seguidores[s].perfil.id}"><img src="{$seguidores[s].perfil.foto}" /> - {$seguidores[s].perfil.nombre}</a><br>{$seguidores[s].perfil.bio}
				<br />{if $seguidores[s].bloqueado} 
							perfil bloqueado <a href="{$webdir}/perfil/seguidor/activar/{$seguidores[s].id}">quitar bloqueo</a>
					  {else} 
							<a href="{$webdir}/perfil/seguidor/bloquear/{$seguidores[s].id}">bloquear</a> 
					  {/if}
				<br />
			</li> 
		{/section}
		</ul>
	
	</div>
	{else}
	Aún no tiene seguidores en Talkmee.
	{/if}	
	</div>
	
	<span class="pager">{pager rowcount=$seguidores_count limit=$limit txt_first="" class_num="texto2" class_numon="error" class_text="texto-boton" show='page' txt_prev='anterior' txt_next='siguiente'}</span>

	<div class="clear_box"></div><br/>
</div> 
 