<?php

/*
CREATE TABLE `contactos_clase` (         
	`id` int(11) NOT NULL auto_increment,  
	`detalle` varchar(250) default NULL,   
	`status` tinyint(1) default NULL,      
	PRIMARY KEY  (`id`)                    
) ENGINE=InnoDB DEFAULT CHARSET=utf8  
*/

class contactos_clase extends OOB_model_type
{
	
	static protected $public_properties = array(
		'detalle' 	=> 'isClean,isCorrectLength-0-255'
	); // property => constraints
	
	static protected $table = 'contactos_clase';
	static protected $class = __CLASS__;	
	static $orders = array('detalle'); 
	
	// definimos los attr del objeto
	public $detalle;
	
	
}

?>