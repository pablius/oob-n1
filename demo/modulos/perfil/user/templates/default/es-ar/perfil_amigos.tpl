<div id="cont_lista">
	
	<div id="banner_titulo"><p>Amigos de {$nombre}</p></div>
	
	<div class="clear_box"></div>
     	
	<div class="box_nocontenedores">
		
	{if $amigos}
	<div id="amigos">
		
		<ul >
		{section name=a loop=$amigos}
			<li >
				
				<a href="{$webdir}/perfil/perfil/ver/{$amigos[a].perfil.id}"> <img src="{$amigos[a].perfil.foto}" /> - {$amigos[a].perfil.nombre}</a><br>{$amigos[a].perfil.bio}
				<br /><a href="{$webdir}/perfil/amigo/borrar/{$amigos[a].perfil.id}">borrar</a>
				<br /><br />
			</li> 
		{/section}
		</ul>
	
	</div>
	{else}
	Aún no tiene amigos en Talkmee.
	{/if}	
	</div>
	
	<span class="pager">{pager rowcount=$amigos_count limit=$limit txt_first="" class_num="texto2" class_numon="error" class_text="texto-boton" show='page' txt_prev='anterior' txt_next='siguiente'}</span>

	<div class="clear_box"></div><br/>
</div> 
 