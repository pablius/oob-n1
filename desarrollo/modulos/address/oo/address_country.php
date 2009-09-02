<?php

 //codigo por jpcoseani 
//obj address_country

/*
id  	int(14)
iso 	char(2)
name 	varchar(100)
hasvat 	int(1)
status 	int(1) 
*/

 class address_country extends OOB_model_type
{
	
	static protected $public_properties = array(
		'iso' 	 				=> 'isClean,isCorrectLength-0-2',
		'name' 				    => 'isClean,isCorrectLength-0-100',
		'hasvat' 				=> 'isBool',
		'array_address_state'	=> 'manyobjects-address_state'
	); // property => constraints
	
	static protected $table = 'address_country';
	static protected $class = __CLASS__;	
	static $orders = array('name'); 
	
	// definimos los attr del objeto
	public $iso;
	public $name;
	public $hasvat;
	public $array_address_state = array();
	
}
 
?>

