<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {oob_block} plugin, loads user blocks inside a template
 *
 */
function smarty_function_oob_block($params, &$smarty)
{
global $ari;
	
    if (empty($params['module'])) {
        $smarty->_trigger_fatal_error("[plugin:oob_block] parameter 'module' cannot be empty");
        return;
    }
    
        if (empty($params['block'])) {
        $smarty->_trigger_fatal_error("[plugin:oob_block] parameter 'block' cannot be empty");
        return;
    }
    $modulo = new oob_module ($params['module']);
    
	if ($ari->get("mode") == "user" ||$ari->get("mode") == "cron" )
		@include ($modulo->userdir() .DIRECTORY_SEPARATOR . "bl_" . $params['block'] . ".php");
  
	if ($ari->get("mode") == "admin")
		@include ($modulo->admindir() .DIRECTORY_SEPARATOR . "bl_" . $params['block'] . ".php");
	
}

/* vim: set expandtab: */

?>
