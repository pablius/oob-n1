<?php

//codigo por jpcoseani
//script que devuelve el listado de modulos de un rol determinado

global $ari;
$ari->popup = 1;  // no mostrar el main_frame 

//falta controlar si viene el id del rol y validar si el rol se crea correctamente

if( isset($_POST['id']) ){
	$role = new seguridad_role ( $_POST['id'] );
}else{
	throw new OOB_Exception_400("La variable [id] no esta definida");
}

//ARRAY CON LOS MODULOS DEL ROLE
$modules_role = false;
$modules_role = seguridad_role::listModulesFor ( $role , false );

$modulos = array();

if( $modules = OOB_module::listModules())
	{

	foreach ($modules as $m)
		{
			$padre = array();
			$padre['leaf'] = false;
			$padre['id']= "m_" . $m->name();
			$padre['text']= $m->nicename();
			$padre['expanded'] = true;
			$padre['iconCls'] = 'Clsmodule';
			$name = '';
			$name = $m->name();
			//VEO SI EL MODULO ESTA EN EL ROL
			
			if( $modules_role){
					if ( in_array( $name , $modules_role ) ){
						$padre['checked'] = true;
					}
					else
					{	
						$padre['checked'] = false;
					}
			}else
			{
				$padre['checked'] = false;
			}	
						
			$padre['children']=array();
			
			
			if ($permisos = seguridad_permission :: listPermissionsFor($m))
			{	
				
	      			foreach ($permisos as $p)
				{
					$permiso=array();
					$permiso['leaf'] = false;
					$permiso['expanded'] = true;
					$permiso['id'] = "p_" . $p->get("permission");
					$permiso['text']=  $p->get("nicename");					
					$permiso['children'] = array();	
					$permiso['iconCls'] = 'Clspermiso';
					
				
				
					if ($acciones = seguridad_action :: listActionsFor($p, ALL_MENU)){	
				
					foreach($acciones as $a)
						{													
							$accion = array();
							$accion['leaf'] = true;
							$accion['id'] = "a_" . $a->get("action");
							$accion['text']=  $a->get("nicename");
							$accion['iconCls'] = 'Clsaccion';
							
							if ( seguridad_action :: exists($a,$role) ){						
								$accion['checked']=true;
							}else{
								$accion['checked']=false;
							}
							$permiso['children'][] = $accion;							
						}												
					}
					
					$padre['children'][]=$permiso;
				
								
				}					
			
			}	
			
			

			$modulos[] = $padre;	
		}
		
		
	
	}

	

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $modulos );
$obj_comunication->send(true,true);

?>