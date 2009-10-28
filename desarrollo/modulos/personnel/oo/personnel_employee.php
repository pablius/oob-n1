<?php
#OOB/N1 Framework [�2004,2005 - Nutus]
/*
 * Created on 01-jul-2005
 * @author Victor Stornati (victor.stornati@nutus.com.ar)
 */
 
  class personnel_employee {
 	
	private $personnel = ID_UNDEFINED;
	private $lastName = '';
	private $firstName = '';
	private $status = '';

	public function id ()
	{
		return $this->personnel;	
	}
	
	public function name ()
	{
		return $this->lastName . ", " . $this->firstName;
	}

	/** Starts the personnel. 
 	    if no personnel set we must believe is a new one */ 	
 	public function __construct ($personnel = ID_UNDEFINED)
	{
	
 	}
	
	/** Returs the value for the given var */ 	
 	public function get ($var)
 	{
 		if (isset ($this-> $var) && !empty ($this-> $var))
			return $this-> $var;
		else
			return false;
 	}

	/** Sets the variable (var), with the value (value) */ 	
 	public function set ($var, $value)
 	{
		if (isset ($this->$var))
			$this->$var= $value;
		else
			return false; 	
 	} 	 	
 	
	/** Stores/Updates personnel object in the DB */	 	
 	public function store ()
 	{
 	}
 	//end function
 	
	/** Fills the group with the DB data */ 	
 	private function fill ()
 	{
			
 	}




	/** Deletes group object from the DB*/ 	
 	public function delete ()
 	{
 		
 	}
 
 	 	
	/** Shows the available sorting ways for group */
	static public function getOrders()
	{
		$return[] = "lastname";
		$return[] = "id";
		$return[] = "firstname";
		$return[] = "status";
		
		return $return;
	} 	
 	//end function
 	
	/** Shows the group status or all available status 
	 * $one = ID or "status_string" returns the account group status; or "ALL" to return an array.
	 * $id => if true, returns a number, else a string.
	 * */
	static public function getStatus ( $one = "ALL" ,$id = true) 
	{ // 1 Used, 9 deleted

	$return[1] = "used";
	$return[9] = "deleted";
	
	if ($id != true)
		$return = array_flip ($return);
	
	if ($one != "ALL")
	{
		if ($return[$one] !== "" )
		{$return = $return[$one];}
		else
		{$return =  false;}
	}

	return $return;

	}
	//end function  	
 
  
 }//end class
 
?>
