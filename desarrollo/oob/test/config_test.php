<?php
/*
 * Created on 03/02/2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include ("..\oob_config.php");
  
  $ini = new oob_config ("../configuracion/base.ini.php" ); // 
  print $ini->get( 'title', 'main');
  print "<br>";
  print $ini->get( 'uri', 'database');
    print "<br>";
      print $ini->get( 'accepted_lang', 'main');
//$ini->save();

      
?>
