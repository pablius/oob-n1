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
$ari->popup = 1;
$template_dir = $ari->module->admintpldir(). "/about.tpl";

$html = $ari->t->fetch( $template_dir );

echo $html;

?>