<li class="amigos">
	Amigos a quienes sigo
	<ul class="amigos_a_quienes_sigo"> 
		{if $amigos}
		{section name=a loop=$amigos}
		<li><a href="{$webdir}/perfil/perfil/ver/{$amigos[a].perfil.id}" title="{$amigos[a].perfil.nombre}"><img src="{$amigos[a].perfil.foto}"</a></li>
		{/section}
		{/if}
		<li class="ver_todos_li"><a href="{$webdir}/perfil/perfil/amigos" class="ver_todos">ver todos</a></li>
	</ul>
	<div style="clear:both;"></div>
						
</li>
 