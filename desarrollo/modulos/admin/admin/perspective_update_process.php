<?php

global $ari;
$ari->popup = 1;

//VIENE EL NAME DE LA PERPECTIVA???
if (isset($_POST['name'])){


//CREACION DEL OBJETO PERSPECTIVE CON EL NAME   QUE VIENE DEL  FORMULARIO
$perspective = new oob_perspective ($_POST['name']);

//*****************************ACTUALIZAR ROLES  (MIEMBROS Y NO MIEMBROS)*******************************

if (isset($_POST['roles'])){

//SE CREA UN ARRAY CON LOS ROLES MIEMBROS DEVUELTOS POR EL CONTROL 'SELECT'
$miebros_roles=split("," , $_POST['roles']);
$real_members_roles=array();


//SE CREA UN ARRAY CON LOS ROLES MIEMBROS QUE ESTAN EN LA BASE DE DATOS
if($roles = OOB_perspective::listRolesFor($perspective))
		{		
			
			foreach ($roles as $r)
			{
				$real_members_roles[]=$r->get('role');		
			}			
		}
		
		//SE BORRAN LOS ROLES QUE NO SON MAS MIEMBROS
		for($i=0;$i<count($real_members_roles);$i++){
		if (!in_array($real_members_roles[$i], $miebros_roles)){
			$tmpRole = new seguridad_role($real_members_roles[$i]);
  			$perspective->removeRole($tmpRole);
		}
		}
		
		//SE AGREGAN LOS ROLES QUE PASARON A SER MIEMBROS
		for($i=0;$i<count($miebros_roles);$i++){
		if (!in_array($miebros_roles[$i], $real_members_roles)){
			$tmpRole = new seguridad_role($miebros_roles[$i]);
  			$perspective->addRole($tmpRole);			
		}			
		}
		
}
//*****************************FIN ACTUALIZACION ROLES*************************************************		
		
//*****************************ACTUALIZAR MODULOS (MIEMBROS Y NO MIEMBROS)*****************************

if (isset($_POST['modulos'])){
//SE CREA UN ARRAY CON LOS MODULOS MIEMBROS DEVUELTOS POR EL CONTROL 'SELECT'
$miebros_modulos=split("," , $_POST['modulos']);
$real_members_modules=array();

//SE CREA UN ARRAY CON LOS MODULOS MIEMBROS QUE ESTAN EN LA BASE DE DATOS
if($modules = OOB_perspective::listModulesFor($perspective))
		{			
			foreach ($modules as $m)
			{
				$real_members_modules[]=$m->name();		
			}			
		}
		
		//SE ELIMINAN LOS MODULOS QUE YA NO SON MIEMBROS
		for($i=0;$i<count($real_members_modules);$i++){
		if (!in_array($real_members_modules[$i], $miebros_modulos)){								
				$tmpModule = new OOB_module($real_members_modules[$i]);
				$perspective->removeModule($tmpModule);				
		}
		}
		
		//SE AGREGAN LOS MODULOS QUE PASARON A SER MIEMBROS
		for($i=0;$i<count($miebros_modulos);$i++){
		if (!in_array($miebros_modulos[$i], $real_members_modules)){
			$tmpModule = new OOB_module($miebros_modulos[$i]);
			$perspective->addModule($tmpModule);			
		}			
		}		

}				
//*****************************FIN ACTUALIZACION MODULOS**********************************************
		

//DEVUELVO UN JSON PARA INDICAR QUE EL PROCESO TERMINO CORRECTAMENTE
$result=array();
$result["errors"]=array();
$result["success"] = true;

$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

}else{
	throw new OOB_Exception_400("La variable [name] no esta definida");
}

?>