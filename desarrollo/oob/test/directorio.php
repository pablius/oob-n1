<?php
#OOB/N1 Framework [©2004,2005 - Nutus]
/*
 * Created on 21/04/2005
 * @author Pablo Micolini
 */
 
 if ($handle = opendir('C:/wamp2/eclipse/workspace/oob/modulos')) {
   while (false !== ($file = readdir($handle))) { 
       if ($file != "." && $file != "..") { 
       if (file_exists ("C:/wamp2/eclipse/workspace/oob/modulos/" . $file . "/module.conf"))
       $availables[] = $file;
       } 
   }
   closedir($handle); 
 }
 var_dump ($availables);
?>

/
 
 