<?php
# Eumafes v2 [�2005 - Nutus, Todos los derechos reservados]
/*
 * Created on 18-ago-2005
 * @author Victor Stornati (victor.stornati@nutus.com.ar)
 */

 class data_type extends OOB_type
 {
  
   function __construct($id = ID_UNDEFINED)
   {
      parent::__construct($id,'data_type','data_type'); 
   } 

	/** Returs the value for the given var */ 	
 	public function get ($var)
 	{	
 		return parent :: get ($var);
 	}
	
	/** Sets the variable (var), with the value (value) */ 	
 	public function set ($var, $value)
 	{
 		switch($var)
 		{
 			case 'control':
 			{
 				if (isset ($this-> $var) && !empty ($this-> $var))
				{	return $this-> $var;	}	
				else
				{	return false;		}			
 				//break;
 			}
 			default:
 			{
 				return parent :: set ($var, $value);		
 				//break;
 			}
 		}//end switch	
 	}//end function

	/**	*/
 	static public function listTypes ($status = USED, $sort = 'name', $operator = OPERATOR_EQUAL)
	{
		return parent :: listTypes($status,$sort,$operator,'data_type','data_type'); 
	}
 	
 	
}//end class
 
 
 
?>
