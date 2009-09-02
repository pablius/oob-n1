<?php
#OOB/N1 Framework [�2004,2005 - Nutus]
/*
 * Created on 03/03/2005
 * @author Pablo Micolini
 */
 $message = "[QUOTE]hola[QUOTE]hola[/QUOTE][/QUOTE]";
 
  	 $needles = "/(\[QUOTE\])([^\]]*?)(\[\/QUOTE\])/s";
   
        while (preg_match ($needles, $message))  {
        
        $haystacks = "Quote:<br /><div id=\"forumquote\">\$2</div>";
        $message = preg_replace( $needles, $haystacks, $message );
 
        } 
        
        print $message;
?>
