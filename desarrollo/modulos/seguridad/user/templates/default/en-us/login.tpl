<link href="{$webdir}/oob/admin/estilo.css" rel="stylesheet" type="text/css">
<p class="texto2"><br />Security</p>
<hr class="barra">

<table width="100%"  border="0" cellpadding="5" cellspacing="5">
  <tr>{if $login}
    <td width="50%" valign="top">
	<form name="login" method="post" action="{$webdir}/seguridad/login">

<span class="texto5">Please enter your user name and password . Only registered users can accede to our data base search . {if $register} {else}If you don&acute;t have an account yet make a free registration </span><span class="texto6"><a class="texto" href="{$webdir}/seguridad/nuevo">here</a>.</span><span class="texto"> {/if} </span><br>
<br>
{if $error} 
<div class="error">
<ul> <font class="error">Error:</font>
   <li>Invalid User or password </li>
 </ul>  </div><br>

 
 {/if}  <font class="texto1">User<br>
  <input name="uname"  class="degra-box" type="text" id="uname"></font><br>


    <font class="texto1"> <br>
    Password<br>

      <input name="pass"  class="degra-box" type="password" id="pass" maxlength="8">
  </font><br>
    <br>

  <input name="login" type="submit" id="login" value="Send">
  <input name="forgot" type="submit" id="forgot" value="Forgot my password">
  </form>  </td>{/if}
  {if $register}
    <td valign="top" class="fondo1"><span class="texto5">If you don&acute;t have an account yet make a free registration</span> here 
      <form name="login" method="post" action="{$webdir}/seguridad/nuevo">
        {if $error}
        <div class="error">
          <ul><font class="error">Error:</font>
            {if $INVALID_USER}    <li>The user or e-mail, is already registered or invalid </li> 
       {/if}
          {if $INVALID_PASS}       <li>Invalid Password  (4 a 8 alfanumeric characters )</li>
          {/if}
            {if $NO_CONCUERDAN}      <li>The passwords doesn&acute;t match</li>{/if}
            {if $INVALID_EMAIL}            <li>Invalid e-mail  </li>{/if}
            {if $INVALID_condiciones}
            <li>You must accept the Terms and Conditions to be able to register .</li>
	{/if}	
          </ul>
        </div><br>
        {/if}
        <font class="texto1">
          User<br>
          <input  class="degra-box"  name="uname" type="text" id="uname" value="{$newname}">
        </font><br>
        
        <font class="texto1"> <br>
        Password
        <br>
          
          <input  class="degra-box"  name="pass" type="password" id="pass" maxlength="8">
          <input  class="degra-box"  name="passtwo" type="password" id="passtwo" maxlength="8">
  </font><br>
  <font class="texto5">(the password must enter twice, to validate it)</font><font class="texto1"><br>
  <br>
    E-mail<br>
  <input  name="email" type="text"  class="degra-box" id="email" value="{$newemail}" size="30">
  </font><br>
  <br>
        <input type="checkbox" name="condiciones" value="checkbox">
        <span class="texto5">Accept  <a href="{$webdir}/about/uso" target="_blank" class="texto6">  terms and conditions</a></span><br>
  <br>
        <input  name="register" type="submit" id="register" value="Log In">
      </form></td>{/if}  </tr>
</table>


