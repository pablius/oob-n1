<?
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/

global $ari;


$ari->t->assign("text", true);
$handle = $ari->url->getVars();
$ari->t->caching=1;

if ($handle[0] == 'uso' || $handle[0] == 'help' || $handle[0] == 'privacidad')
$ari->t->display($ari->module->usertpldir(). "/" . $handle[0] . ".tpl");
else
$ari->t->display($ari->module->usertpldir(). "/about.tpl");


?>