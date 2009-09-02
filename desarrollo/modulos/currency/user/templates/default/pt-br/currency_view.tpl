<script language="javascript" type="text/javascript" src="{$webdir}/scripts/functionsjs/functionsjs.js">
</script>
<link href="../../../eximius_perspectiva/estilo.css" rel="stylesheet" type="text/css" />

<h1 class="texto2">Ver Trocas de Moeda Corrente</h1>
{if $error}
	<div class="error">
		<ul>Erro:
			{if $INVALID_INTERVAL}     
				<li>A data da publica&ccedil;&atilde;o &quot;De&quot;  deve ser menor ou igual &agrave; data de publica&ccedil;&atilde;o &quot;Ate&quot;.</li>
			{/if}
			
			{if $INVALID_DESDE}     
				<li>A data &quot;De&quot; es inv&aacute;lida.</li>
			{/if}

			{if $INVALID_HASTA}     
				<li>A data &quot;Ate&quot; es inv&aacute;lida.</li>
			{/if}

			{if $SENT_DUPLICATE_DATA}
				<li>Os dados deste fomulario t&ecirc;m sido emitidos j&aacute; para seu processar.</li>
			{/if}
		</ul>
</div>
{/if} 


<form name="value" id="value" method="post" action="">
	{$formElement}
	<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="fondo1">
		<tr>
			<td height="26" valign="top">
				<strong class="texto6">Moeda </strong>			</td>
			<td colspan="3" valign="top">
				<select name="moneda" id="moneda">
					{html_options options=$optMoneda selected=$selMoneda}
				</select>		  </td>
		</tr>	
		<tr class="cuadro-arriba">
			<td valign="top">
				<strong class="texto6">Data</strong>			</td>
			<td align="left" valign="top">
				<span class="texto5">De</span><font class="titulo3">: </font> 
				{html_select_date prefix="desde" field_order="DMY" time=$desde start_year="-10" end_year="+10" all_extra=$desdeDisabled}
				<br />
				<input name="desdeCheck" id="desdeCheck" type="checkbox" value="1" {$desdeChecked} onClick="enabledDate('desde')">
		  <span class="texto6">Sem Especificar</span></td>
			<td align="left" valign="top">
				<span class="texto5">Ate</span><font class="titulo3">: </font>
				{html_select_date prefix="hasta" field_order="DMY" time=$hasta start_year="-10" end_year="+10" all_extra=$hastaDisabled }<br />
			  <input name="hastaCheck" id="hastaCheck" type="checkbox" value="1" {$hastaChecked} onClick="enabledDate('hasta')">
		  <span class="texto6">Sem Especificar</span></td>
		</tr>

		<tr>
			<td height="29" colspan="4" align="center" valign="bottom">
				<input name="mostrar" type="submit" value="Busca">			
		  </td>
		</tr>	
		
  </table>
	
	<br>
	{if $mostrar}
		<table width="50%" align="center">
			<tr>
				<td><div align="center"><strong class="texto3">Valor</strong></div></td>
				<td><div align="center"><strong class="texto3">Data</strong></div></td>
			</tr>
			
			{section name=c loop=$changes}
				<tr class="fondo2">
					<td>
						<div align="center" class="texto5">{$changes[c].value}</div>
					</td>
					<td>
						<div align="center" class="texto5">{$changes[c].date}</div>
					</td>
		  </tr>	      
			{sectionelse}
				<tr>
					<td colspan="3" class="texto6">
						O valor n&atilde;o foi encontrado para esta moeda </td>
				</tr>
			{/section}
  </table>
	{/if}
</form>
<script language="javascript" type="text/javascript">setID('value')</script>