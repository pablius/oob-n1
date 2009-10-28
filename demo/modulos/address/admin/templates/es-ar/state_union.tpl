<script language="javascript" type="text/javascript" src="{$webdir}/scripts/functionsjs/functionsjs.js">
</script>
<script language="javascript" type="text/javascript" src="{$webdir}/scripts/functionsjs/address_functions.js">
</script>

<font class="texto4" id="title_no_item" style="display:none">--- Sin Especificar ---</font>

<h1 class="titulo2" align="center"><br />
Uni&oacute;n de Provincias/Estados </h1>

{if $error}
   <div class="error">
	 <ul><font class="error">Error:</font>
	 {if $INVALID_SOURCES}       
		<li>Debe seleccionar las provincias or&iacute;genes de la uni&oacute;n.</li>
	 {/if}
	 
	 {if $NO_COUNTRY}       
		<li>Debe seleccionar el pa&iacute;s de destino.</li>
	 {/if}
	 
	 {if $NO_DESTINY_OPTION}       
		<li>Debe seleccionar la provincia destino de la uni&oacute;n, o bien, ingresar una nueva.</li>
	 {/if}
	 
	 {if $NO_DESTINY_EXISTS}       
		<li>Debe seleccionar la provincia destino de la uni&oacute;n.</li>
	 {/if}

	 {if $INVALID_NAME}   
		<li>Debe ingresar correctamente el nombre de la nueva provincia destino de la uni&oacute;n.</li>
	 {/if}
	 
	 {if $DUPLICATE_STATE}   
		<li>La nueva provincia destino ingresada ya existe.</li>
	 {/if}
	 
	 </ul>
   </div>
{/if}
   
{if count($states)>0}
  <form name="union" method="post" action="{$webdir}/address/state/union">
	
	<br />
	<h2>Paso 1: Seleccione las Provincias Or&iacute;genes que desea unir</h2>
	
	Esta es la lista de Provincias registradas en el sistema
	
	<table width="100%" border="0">
		<tr>
			<td align="left" width="10%"><strong>Origen</strong></td>
			<td align="center" width="45%"><strong>Provincia</strong></td>
			<td align="center" width="45%"><strong>Pa&iacute;s</strong></td>
		</tr>
		{section name=t loop=$states}
			<tr>
				<td align="left">
					<input name="sources[]" type="checkbox" value="{$states[t].id}" {$states[t].source_checked} >
				</td>
				<td><strong>{$states[t].name}</strong></td>
				<td><strong>{$states[t].countryName}</strong></td>		
			</tr>
		{/section}
	</table>       				
	<div align="left">
		<a href="javascript:ChequearTodos('sources[]','union',true)">&lt;&lt;Seleccionar todo&gt;&gt;</a>
		&nbsp;		
		<a href="javascript:ChequearTodos('sources[]','union',false)">&lt;&lt;Deseleccionar&gt;&gt;</a>			
	</div>

	<br>
	<hr>
	<br>
	
	<table width="100%" border="1">
	  <tr valign="top">
	 	 <td colspan="2" valign="top">

			<h2>Paso 2: Seleccione la Provincia Destino de la uni&oacute;n</h2>
			<br />
			Seleccione una Provincia existente, o bien, ingrese una nueva Provincia como destino de la uni&oacute;n
			<br />
			<br />

			<font id="title_country">Seleccione Pa&iacute;s:</font> 
			<select  class="degra-box"  name="address_country[]" id="address_country" onChange="nullStateCity('')" >
				<option value="-1">--- Sin Especificar ---</option>
				{html_options options=$countries selected=$address_country_selected}
			</select>
			 <br />
			 <br />
		 </td>
	  
	  </tr>
	  
	  <tr valign="top">
		<td valign="top">
			<input name="destiny" type="radio" value="1" {if !$new_state}checked="checked"{/if}>
			<strong>Seleccione una Provincia existente</strong>
   		    <br />
			<br>
			<font id="title_state">Provincia:</font>
			<input   name="address_state[]" id="address_state_id" type="hidden" size="1" readonly value="{$address_state_id}">
			<input  class="degra-box"  name="address_state_name[]" id="address_state_name"  type="text" size="20" readonly value="{$address_state_name}" style="border-style:none">
			<a  href="javascript:openSelectState('{$webdir}/address/state/select/','newaccount','')" ><font id="title_select">Seleccionar</font></a>
		
			<!-- text de localidad => no quitar porque el javascript no anda sino -->
			<input   name="address_city[]" id="address_city_id" type="hidden" size="1" value="{$address_city_id}">
			<input class="degra-box"   name="address_city_name[]" id="address_city_name"  type="hidden" size="20" readonly value="{$address_city_name}" style="border-style:none">
			
			
		<!--	
			<font id="title_city">Localidad:</font><br>
			<input   name="address_city[]" id="address_city_id" type="hidden" size="1" value="{$address_city_id}">
			<input class="degra-box"   name="address_city_name[]" id="address_city_name"  type="hidden" size="20" readonly value="{$address_city_name}" style="border-style:none">
			<a  href="javascript:openSelectCity('{$webdir}/address/city/select/','newaccount','')" >Seleccionar</a>  
			<br>
			<br>
		 -->  
		
		</td>
	
		<td valign="top">
			<input name="destiny" type="radio" value="-1" {if $new_state}checked="checked"{/if}>
			<strong>Ingrese una Nueva Provincia como destino de la uni&oacute;n</strong>
			<br> 
			<br>
			<span class="texto">Nombre:</span>
			<input name="new_name" type="text" value="{$new_name}">
			<br />
			<br />
		</td>
	
	  </tr>
	</table>
	
	<br>
	<hr size="1">
	<br>
	<br>
	<div align="center">
	   <input name="guardar" type="submit" value="Guardar"> 
    </div>

{else}
	<div class="error">No se encontraron provincias/estados.</div>
{/if}


</form>