<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/
//OOB_module :: includeClass("seguridad","seguridad_permission");
//OOB_module :: includeClass("seguridad","seguridad_action");

$ari->t->caching=0;
$ari->t->assign("error", false);
$sp = new oob_safepost ("form1");

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('new','action','seguridad')) )
{	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
} 

$i = 0;
$modSelect = '';
$arrModulo = array();
$arrIdModulo = array();
if ($objModulo =  OOB_module :: listModules())
{	//cargar modulos
	foreach($objModulo as $m)
	{	$modules [$i]['name']= $m->nicename(); //nombre
		$modules [$i]['id']=$m->name(); //id
		$perm_name = array();
		$perm_id = array();
		
		if ($objPermiso = seguridad_permission :: listPermissionsFor($m,true))
		{	$j = 0;
			//cargar permisos para modulo actual
			foreach($objPermiso as $p)
			{	$perm_name[$j] = $p->get("nicename")." (".$p->get("name").")";
				$perm_id[$j] = $p->get("permission");
				$j++;
			}
		}		
		
		$refrescados = array();
		//vemos si tenemos q refrescar algo
		if (isset($_POST['modulo']) && 
			isset($_POST['permiso']) && 
			isset($_POST['accion']) && 
			isset($_POST['nicename']) && 
			isset($_POST['inmenu']))
		{ 
			//cargo el array refrescados
			$r = 0;
			for ($k = 0; $k<count($_POST['modulo']);$k++)
			{
				//me fijo si el elemento posteado actual es del modulo q recorro
				//if ($_POST['modulo'][$k] == $m->name() && 
					//OOB_validatetext :: isClean($_POST['accion'][$k]) && 
					//OOB_validatetext :: isCorrectLength ($_POST['accion'][$k], 1, MAX_LENGTH) )

				//me fijo si el elemento posteado actual es del modulo q recorro
				if ($_POST['modulo'][$k] == $m->name())
				{	//lo cargo en el array de refrescados
					$refrescados[$r]['row'] = $k * (-1);
					$refrescados[$r]['modulo'] = $_POST['modulo'][$k]; 
					$refrescados[$r]['permiso'] = $_POST['permiso'][$k];
					$accion = OOB_validatetext :: inputHTML($_POST['accion'][$k]);
					$refrescados[$r]['accion']= $accion;
					$nicename = OOB_validatetext :: inputHTML($_POST['nicename'][$k]);
					$refrescados[$r]['nicename'] = $nicename;
					$refrescados[$r]['inmenu']= $_POST['inmenu'][$k];
					
					//busco el nombre del permiso
					$refrescados[$r]['permisoName'] = "";
					foreach($objPermiso as $p)
					{	
						if ($p->get('permission') == $_POST['permiso'][$k])
						{	
							$refrescados[$r]['permisoName'] = $p->get("nicename");
							break;
						}	
					}
					
					$r++;
					
				}//end if
				
			}//end for
		
		}//end if 

		$modules[$i]['refrescados'] = $refrescados;
		$modules[$i]['permission_name'] = $perm_name;
		$modules[$i]['permission_id'] = $perm_id;	
		$i++;	
		
	}//end for
	
}//end if


if (!isset ($_POST['guardar']))
{	
	$array_values['modules'] = $modules;
	refrescar($array_values);
} 
else 
{	
	$storeOk = true;
	
	//verificar datos enviados duplicados
	if(!$sp->Validar())
	{	$ari->error->addError ('seguridad_action', 'SENT_DUPLICATE_DATA');
	}
	
	if(!isset($_POST['modulo']))
	{
		$storeOk = false;
		$ari->error->addError ("seguridad_action", "NO_ACTION");	
	}
	else
	{
		for ($i = 0; $i<count($_POST['modulo']);$i++)
		{
			$accion = new seguridad_action();
			if (isset($_POST['accion'][$i]))
			{	$accion->set('name', $_POST['accion'][$i]);
			}
			
			if (isset($_POST['nicename'][$i]))
			{	$accion->set('nicename', $_POST['nicename'][$i]);
			}
	
			if (isset($_POST['permiso'][$i]))
			{	$permiso = new seguridad_permission ($_POST['permiso'][$i]);
				$accion->set('permission', $permiso);
			}
	
			if (isset($_POST['inmenu'][$i]))
			{	$accion->set('inmenu', $_POST['inmenu'][$i]);
			}
			
		 	if(!$accion->store()) 
			{	
				$storeOk = false;
			}
			
		}//end for

	}
	
	if($storeOk)
	{
		header( "Location: " . $ari->get("adminaddress") . '/seguridad/role/list'); 
  		exit;
	}	
	else
	{	
		$array_values['modules'] = $modules;
		refrescar($array_values);
		
		$ari->t->assign("form", true);
		$ari->t->assign("error", true );
	    
	    if($errores = $ari->error->getErrorsfor("seguridad_action"))
		{	
			foreach ($errores as $error)
		  	{	$ari->t->assign($error, true );
		  	}
		}
		
	}//end else $storeOk
}

$ari->t->assign("formElement", $sp->FormElement());
$ari->t->display($ari->module->admintpldir(). "/action_new.tpl");

function refrescar($array_values)
{
	global $ari;
	$ari->t->assign("modules", $array_values['modules']);
}

?>
