<?php

 //codigo por jpcoseani 
//obj address_state

/*
id  	int(11)
name 	varchar(70)
status 	int(1)
id_country 	int(14) 	
*/

 class address_state extends OOB_model_type
{
	
	static protected $public_properties = array(
		'name' 	 		=> 'isClean,isCorrectLength-0-70',
		'id_country'	=> 'object-address_country',		
	); // property => constraints
	
	static protected $table = 'address_state';
	static protected $class = __CLASS__;	
	static $orders = array('name'); 
	
	// definimos los attr del objeto
	public $name;
	public $id_country;
		
}
 
?>