<?php
#OOB/N1 Framework [�2004,2005 - Nutus]
/*
 * Created on 01-jul-2005
 * @author Victor Stornati (victor.stornati@nutus.com.ar)
 */
 
  class personnel_personnel {
 	
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
 		global $ari;

		if ($personnel > ID_MINIMAL) {
			$this->personnel= $personnel;
			
			if (!$this->fill ())
			{throw new OOB_exception("Invalir person {$person}", "403", "Invalid Person", false);}
					
		}  
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
		global $ari;

			//load info
			$id = $ari->db->qMagic($this->personnel);
			$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
			$sql= "SELECT FirstName, LastName, Status FROM personnel_personnel WHERE id = $id";
			$rs = $ari->db->Execute($sql);
			
			$ari->db->SetFetchMode($savem);
			if (!$rs || $rs->EOF) {
				return false;
			}
			if (!$rs->EOF) {
				$this->firstName = $rs->fields[0];
				$this->lastName = $rs->fields[1];
				$this->status = $rs->fields[2];
			}
			$rs->Close();
			return true; 		
 	}

	/** retorna el id del nombre pasado por parametro */ 	
 	static public function getIDByName ($lastName = '',$firstName = '' )
 	{
		global $ari;

		//load info
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		
		$firstName = trim($ari->db->qMagic($firstName));
		$lastName = trim($ari->db->qMagic($lastName));
		
		$sql= "SELECT ID FROM personnel_personnel  
			   WHERE LastName = $lastName
			   AND FirstName = $firstName 
			   ";

		$rs = $ari->db->SelectLimit($sql,1,0);
			
		$ari->db->SetFetchMode($savem);
		if (!$rs || $rs->EOF) 
		{	$return = false;	}
		
		if (!$rs->EOF) 
		{	$return = $rs->fields[0];	}
		
		$rs->Close();
		return $return; 		
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
