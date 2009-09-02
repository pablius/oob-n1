<?
# Nutus [©2007 - Nutus, Todos los derechos reservados]
/*
 * Created on 22/06/2007
 * @author Flavio Robles (flavio.robles@nutus.com.ar)
 */

global $ari;
$plantilla = $ari->newTemplate();
$plantilla->caching = 0; 
$modulo = new oob_module ('seguridad');
$plantilla->assign("userID", $ari->get("user")->id());
$plantilla->display($modulo->usertpldir(). "/bl_user_delete.tpl");
?>