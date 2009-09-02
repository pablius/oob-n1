<?php
/**
########################################
#OOB/N1 Framework [©2004,2008]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/

/**
 This class defines the basic model for storable objects in the system

 */

abstract class OOB_model extends OOB_internalerror
{
	public $id;
	static $orders = array('id','status');
	static $states = array( 1 => 'enabled', 9 => 'deleted');

	abstract public function isValid();
	abstract public function allowDelete();
	abstract public function store();
	abstract protected function fill();
	abstract public function delete();
	abstract static public function getList();
	abstract static public function getListCount();
	abstract static public function getStates();
	abstract static public function getOrders();
	
	 public function id()
	 {
	 	return $this->id;
	 }
	
	public function get($var)
	{
		return $this->$var;
	}
	
	public function set($var,$value)
	{
		$this->$var = $value;
	}
	
	
}
?>