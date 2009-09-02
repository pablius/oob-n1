<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{$webdir}/perspectives/default/estilo.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div align="right"><a class="texto3" href="javascript:window.close()" >Cerrar</a></div>
<h2 align="center" class="titulo2">Borrar Usuario</h2>

{if $error}
	<div class='error'>
		ERROR:
		<ul>
			{if $ALREADY_DELETED}
				<li>O usu&aacute;rio tem sido eliminado j&aacute;.</li>
			{/if}
			{if $NO_SUCH_USER}
				<li>O usu&aacute;rio não existe.</li>
			{/if}
		</ul> 
</div>
{/if}

<form name="delete" id="delete" method="post">

<div align="center">
  <table width="98%" border="0" cellpadding="0" cellspacing="0" class="borde-caja">
    <tr class="cuadro-arriba">
      <td><div align="center" class="texto1"><strong>{$userName}</strong>: &iquest;Certamente esse desejos para eliminar seu usu&aacute;rio?</div></td>
    </tr>
    <tr class="cuadro-abajo">
      <td height="42"><div align="center">
        <input name="si" type="submit" value="Si" >
        <input name="No" type="button" value="No" onClick="window.close()"> 
        </div></td>
    </tr>
  </table>
  </div>
  
  <br>
<br>
	
{if $close}
	<script language="javascript" type="text/javascript">
		window.close(); 
		window.opener.location = "{$webdir}";
	</script>
{/if}	
	
</form>
</body>
</html>
