<p class="texto2"><br />Usu&aacute;rio</p>
<hr class="barra">

<table width="100%"  border="0" cellpadding="5" cellspacing="5">
  <tr>{if $login}
    <td width="50%" valign="top">
	<form name="login" method="post" action="{$webdir}/seguridad/login">

<span class="texto5">Incorporar por favor seus nome e senha do usu&aacute;rio.  Somente os usu&aacute;rios registados podem entrar  a nossa busca da base de dados.  {if $register} {else}Se voc&ecirc; don&acute;t tiver uma conta fazer um registo livre </span><span class="texto6"><a class="texto" href="{$webdir}/seguridad/nuevo">aqui </a>.</span><span class="texto"> {/if} </span><br>
<br>
{if $error} 
<div class="error">
<ul> <font class="error">Erro:</font>
   <li>Usuario o contrase&ntilde;a no v&aacute;lida</li>
 </ul>  
</div><br>

 
 {/if}  <font class="texto1">Usu&aacute;rio <br>
  <input name="uname"  class="degra-box" type="text" id="uname"></font><br>


    <font class="texto1"> <br>
    Senha <br>

      <input name="pass"  class="degra-box" type="password" id="pass" maxlength="8">
  </font><br>
    <br>

  <input name="login" type="submit" id="login" value="Entrar ">
	<input name="forgot" type="submit" id="forgot" value="esqueceu-se de minha senha " />
	</form>  </td>{/if}
  {if $register}
    <td valign="top" class="fondo1"><span class="texto5">Se voc&ecirc; don&acute;t tiver uma conta fazer um registo livre aqui 
      </span>
      <form name="login" method="post" action="{$webdir}/seguridad/nuevo">
        {if $error}
        <div class="error">
          <ul><font class="error">Erro:</font>
            {if $INVALID_USER}    <li>O usu&aacute;rio ou o E-mail, s&atilde;o registados j&aacute; ou invalid </li> 
       {/if}
          {if $INVALID_PASS}       <li>Senha inv&aacute;lida (4 a 8 car&aacute;teres alfanumerics) </li>{/if}
            {if $NO_CONCUERDAN}      <li>O f&oacute;sforo do doesn&acute;t das senhas </li>{/if}
            {if $INVALID_EMAIL}            <li>E-mail inv&aacute;lido </li>{/if}
            {if $INVALID_condiciones}
            <li>Voc&ecirc; deve aceitar os termos e as circunst&acirc;ncias registam. </li>
	{/if}	
          </ul>
        </div><br>
        {/if}
        <font class="texto1">Usu&aacute;rio <br>
          <input  class="degra-box"  name="uname" type="text" id="uname" value="{$newname}">
        </font><br>
        
        <font class="texto1"> <br>
          Senha<br>
          
          <input  class="degra-box"  name="pass" type="password" id="pass" maxlength="8">
          <input  class="degra-box"  name="passtwo" type="password" id="passtwo" maxlength="8">
  </font><br>
  <font class="texto5">(a senha deve entrar duas vezes, o validar) </font><font class="texto1"><br>
  <br>
    E-mail <br>
  <input  name="email" type="text"  class="degra-box" id="email" value="{$newemail}" size="30">
  </font><br>
  <br>
        <input type="checkbox" name="condiciones" value="checkbox">
        <span class="texto5">Aceitar </span><span class="texto6">termos e circunst&acirc;ncias </span><br>
  <br>
  <br>
        <input  name="register" type="submit" id="register" value="Registo ">
    </form></td>
    {/if}  </tr>
</table>


