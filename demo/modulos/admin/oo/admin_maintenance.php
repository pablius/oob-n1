<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/
 
  /** this class handles the system maintenance tasks */
 class admin_maintenance {
 	
private function __construct () {}

public function clearCache ($kind = 'all') {} // all, templates, db

public function optimizeDB () {}

public function backupDB () {}


 }
?>
