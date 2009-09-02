<?php
/**
 * PHP-Ext Library
 * http://php-ext.googlecode.com
 * @author Sergei Walter <sergeiw[at]gmail[dot]com>
 * @copyright 2008 Sergei Walter
 * @license http://www.gnu.org/licenses/lgpl.html
 * @link http://php-ext.googlecode.com
 * 
 * Reference for Ext JS: http://extjs.com
 * 
 */

/**
 * @see PhpExt_Object
 */
include_once'PhpExt/Object.php';

/**
 * This class provides the default UI implementation for root Ext TreeNodes. The RootTreeNode UI implementation allows customizing the appearance of the root tree node.
 *
 * If you are customizing the Tree's user interface, you may need to extend this class, but you should never need to instantiate this class. 
 * 
 * @package PhpExt
 * @subpackage Tree
 */
abstract class PhpExt_Tree_RootTreeNodeUI extends PhpExt_Object 
{			
	public function __construct() {
		parent::__construct();
		//$this->setExtClassInfo("Ext.tree.AbstractSelectionModel", null);	
	}				
}



