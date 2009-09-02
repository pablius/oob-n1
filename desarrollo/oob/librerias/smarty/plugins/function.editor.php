<?php


/**
* ------------------------------------------------------------- 
* @param InstanceName Editor instance name (form field name)
* @param Width optional width (css units)
* @param Height optional height (css units)
* @param CheckBrowser optional check the browser compatibility when rendering the editor
* @param DisplayErrors optional show error messages on errors while rendering the editor
*
*/ 
function smarty_function_editor($params, &$smarty)
{ 
		global $ari;
        require_once ($ari->filesdir .  DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'editor' . DIRECTORY_SEPARATOR . 'fckeditor.php');
		
        if(!isset($params['InstanceName']) || empty($params['InstanceName']))
        { 
                $smarty->trigger_error('fckeditor: required parameter "InstanceName" missing');
        }


        $oFCKeditor = new FCKeditor($params['InstanceName']) ;
		if ($ari->mode == 'admin')
			$oFCKeditor->BasePath = $ari->adminaddress . '/scripts/editor/';
		else
			$oFCKeditor->BasePath = $ari->webaddress . '/scripts/editor/';
			
		if (isset ($params['Value']))
			$oFCKeditor->Value = $params['Value'];
			
 /*					if (isset ($params['Width']))
			$oFCKeditor->Width = $params['Width'];
			
					if (isset ($params['Height']))
			$oFCKeditor->Height = $params['Height']; */

		$oFCKeditor->Config['DefaultLanguage']		=substr ($ari->agent->getSelectedLang(),0,2) ;
		$oFCKeditor->Config['CustomConfigurationsPath'] = $oFCKeditor->BasePath . 'config.js' ;
		
		if (isset ($params['simple']) && $params['simple'] == true)
				$oFCKeditor->ToolbarSet= 'Small' ;

//$oFCKeditor->Create() ;   
        return $oFCKeditor->CreateHtml();
} 

/* vim: set expandtab: */

?>
