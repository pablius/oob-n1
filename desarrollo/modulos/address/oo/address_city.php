<?php

//codigo por jpcoseani 
//obj address_city

/*
id  	int(14)
name 	varchar(70)
status 	int(1)
id_state 	int(14) 		
*/

 class address_city extends OOB_model_type
{
	
	static protected $public_properties = array(
		'name' 	 		=> 'isClean,isCorrectLength-0-255',
		'realname'		=> 'isClean,isCorrectLength-0-255',
		'id_state'		=> 'object-address_state',		
		'id_country'	=> 'object-address_country'
		); // property => constraints
	
	static protected $table = 'address_city_view';
	static protected $class = __CLASS__;	
	static $orders = array('name'); 
	
	// definimos los attr del objeto
	public $name;
	public $id_state;
	public $id_country;

	/* no se puede grabar este objeto porque utiliza una vista para los datos */
	public function store()
	{
		return false;
	}
}
 
/*
CREATE VIEW address_city_view AS 
SELECT address_city.id as id,
	   address_city.name as realname,
		CONCAT( address_city.name,
				' (', 
				address_state.name, 
				',', 
				address_country.name, 
				')' ) as name, 
				address_city.id_state as id_state, 
				address_state.id_country as id_country,
				address_city.status as status
FROM `address_city` 
INNER JOIN `address_state` ON address_city.id_state = address_state.id
INNER JOIN `address_country` ON address_state.id_country = address_country.id

*/
 
?>