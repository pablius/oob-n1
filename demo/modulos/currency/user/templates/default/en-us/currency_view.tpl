<script language="javascript" type="text/javascript" src="{$webdir}/scripts/functionsjs/functionsjs.js">
</script>
<link href="../../../eximius_perspectiva/estilo.css" rel="stylesheet" type="text/css" />

<h1 class="texto2">View Currency Exchanges</h1>
{if $error}
	<div class="error">
		<ul>Error:
			{if $INVALID_INTERVAL}     
				<li>The date of publication &quot;From&quot; must be smaller or equal to the date of publication.</li>
			{/if}
			
			{if $INVALID_DESDE}     
				<li>The date &quot;From&quot; is not valid.</li>
			{/if}

			{if $INVALID_HASTA}     
				<li>The date &quot;Until&quot; is not valid .</li>
			{/if}

			{if $SENT_DUPLICATE_DATA}
				<li>The data of this form have been sent for processing .</li>
			{/if}
		</ul>
</div>
{/if} 


<form name="value" id="value" method="post" action="">
	{$formElement}
	<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="fondo1">
		<tr>
			<td width="11%" height="27" valign="top">
				<strong class="texto6">Currency</strong>			</td>
			<td valign="top">
				<select name="moneda" id="moneda">
					{html_options options=$optMoneda selected=$selMoneda}
				</select>		  </td>
		    <td valign="top">&nbsp;</td>
		    <td width="1%" valign="top">&nbsp;</td>
		</tr>	
		<tr class="cuadro-arriba">
			<td valign="top">
				<strong class="texto6">Data</strong>			</td>
			<td width="39%" align="left" valign="top">
				<font class="texto5">From: </font> 
				{html_select_date prefix="desde" field_order="DMY" time=$desde start_year="-10" end_year="+10" all_extra=$desdeDisabled}<br />
			  <input name="desdeCheck" id="desdeCheck" type="checkbox" value="1" {$desdeChecked} onClick="enabledDate('desde')">
		  <span class="texto6">Unspecified</span></td>
			<td width="49%" align="left" valign="top">
				<font class="texto5">Until: </font>
				{html_select_date prefix="hasta" field_order="DMY" time=$hasta start_year="-10" end_year="+10" all_extra=$hastaDisabled }<br />
			  <input name="hastaCheck" id="hastaCheck" type="checkbox" value="1" {$hastaChecked} onClick="enabledDate('hasta')">
		  <span class="texto6">Unspecified</span></td>
		</tr>

		<tr>
			<td height="29" colspan="4" align="center" valign="bottom">
			<input name="mostrar" type="submit" value="Send">			</td>
		</tr>	
  </table>
	
	<br>
	{if $mostrar}
		<table width="50%" align="center">
			<tr>
				<td><div align="center"><strong class="texto3">Value</strong></div></td>
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
						No values found. </td>
				</tr>
			{/section}
  </table>
	{/if}
</form>
<script language="javascript" type="text/javascript">setID('value')</script>