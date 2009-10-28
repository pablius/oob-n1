<h2 class="title">Ver cambios de moneda</h2>
<div class="hzl"></div>

{if $error}
	<div class="error">
		Error:
		<ul>
			{if $INVALID_INTERVAL}     
				<li>La Fecha de Publicaci&oacute;n Desde proporcionada  debe ser menor o igual a la La Fecha de Publicaci&oacute;n Hasta.</li>
			{/if}
			
			{if $INVALID_DESDE}     
				<li>La Fecha Desde no es v&aacute;lida.</li>
			{/if}

			{if $INVALID_HASTA}     
				<li>La Fecha Hasta no es v&aacute;lida.</li>
			{/if}

			{if $SENT_DUPLICATE_DATA}
				<li>Los datos de este fomulario ya han sido enviados para su procesamiento.</li>
			{/if}

		</ul>
	</div>
{/if} 

<form name="value" id="value" method="post" action="">
	{$formElement}


	<div id="frame_A">
		<div id="frame_A_margen">

			<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="fondo1">
				<tr>
					<td height="22" valign="top">
						Moneda
					</td>

					<td colspan="3" valign="top">
						<select name="moneda" id="moneda">
							{html_options options=$optMoneda selected=$selMoneda}
						</select>
					</td>
				</tr>

				<tr>
					<td height="78" valign="middle">
						Fecha
					</td>

					<td align="left" valign="middle">

						Desde:
						{html_select_date prefix="desde" field_order="DMY" time=$desde start_year="-10" end_year="+10" all_extra=$desdeDisabled}
						<br />
						<input name="desdeCheck" id="desdeCheck" type="checkbox" value="1" {$desdeChecked} onclick="enabledDate('desde');" />

						<span>Sin Especificar</span>
					</td>

					<td align="left" valign="middle">
						Hasta:
						{html_select_date prefix="hasta" field_order="DMY" time=$hasta start_year="-10" end_year="+10" all_extra=$hastaDisabled }
						<br />

						<input name="hastaCheck" id="hastaCheck" type="checkbox" value="1" {$hastaChecked} onclick="enabledDate('hasta');">
						<span>Sin Especificar</span>
					</td>

				</tr>

				<tr>
					<td colspan="4" align="center" valign="top">
						<input name="mostrar" type="submit" value="Mostrar">			
					</td>
				</tr>

			</table>			


		</div>
	</div>

</form>


<br/>

{if $mostrar}

	<table id="display">

		<tr class="campos">
			<td>Valor</td>

			<td>Fecha</td>
		</tr>
			
		{section name=c loop=$changes}
		<tr class="valores">
			<td>
				{$changes[c].value}
			</td>

			<td>
				{$changes[c].date}
			</td>
		</tr>

		{sectionelse}
		<tr class="valores">
			<td colspan="2">
				<b>No se encontraron valores</b>
			</td>
		{/section}
		</tr>
	</table>
{/if}
