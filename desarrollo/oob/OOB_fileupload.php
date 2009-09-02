<?
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini, based on original work by Lennart Groetzbach
#  @license LGPL
######################################## 
*/

# @author	    Lennart Groetzbach <lennartg@web.de> + Pablo Micolini
# @version 	    0.5 - 2003/01/13

/** Handles the upload of files to the system */ 
class OOB_fileupload extends OOB_internalerror {
    

// maximum file size
private $_size = 1048576;
private $formVar;

public function __construct($formVar = "file") {
    if (!ini_get("safe_mode")) {
    } else {
    			  $this->error()->addError ("CONFIG"); 
 //     $this->_error("Turn 'safemode' in your php.ini off!");
    }
    $this->formVar = $formVar;
}


////////////////////////////////////////////////////////////////////////
/**
* Uploads files

* @param    String      $path               dir to move files to
* @param    Boolean     $overwrite          overwrite existing file?   
* @param    Array       $allowedTypeArray   array of allowed file types, if empty all files are allowed
*
* @return   Array       array with file information
* @todo: get base path from config file.
*/
public function upload($path, $overwrite=false, $allowedTypeArray=null, $prefix = '',$md5 = false ) {
   global $ari;
    // fix path
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/') {
        $path .= '/';
    }
    if ($prefix != '')
    $prefix = $prefix . '_';
    
    // does upload path exists?
    if ((file_exists($path)) && (is_writable($path))) {
        // for all files
        $res = array();
  
        // get the file list
     // print $_FILES["file"]['error'];
 
         if ($_FILES[$this->formVar]['error'] == 4) // @todo TA MAL EL PRIMER INDICE EN EXPLORER
         { $this->error()->addError ("NO_FILE"); 
          return false;
         }
        //  $this->error .= $this->_error("No file Posted!");
          else
         $files = & $_FILES; 

        // for all files...
       // foreach($files as $key => $file) {
      $file = $_FILES[$this->formVar];
        	
            // does the file exist?
            if (!@$file['error'] && $file['size'] && $file['name'] != '') {
                // is the file type allowed?
                if (($allowedTypeArray == null) || (@in_array($file['type'], $allowedTypeArray))) {
                    // is it really an uploaded file?
                    if (is_uploaded_file($file['tmp_name'])) {
                        // does file exists?
                        $exists = file_exists($path . $prefix . $file['name']);
                        // overwrite file?
                        if ($overwrite || !$exists) {
                            // move file to new destination
                                                    
                            			if ($md5)
										{
											// sacamos la extensión
											$info = pathinfo($file['name']);
											move_uploaded_file($file['tmp_name'], $path . $prefix . md5($file['name']) . '.' . $info['extension']);
											$res = array('name' => $prefix . md5($file['name']) . '.' . $info['extension'], 'full_path' => $path . $prefix . md5($file['name']) . '.' . $info['extension'], 'type' => $file['type'], 'size' => $file['size'], 'overwritten' => $exists);
										}	
										else
										{
											move_uploaded_file($file['tmp_name'], $path . $prefix . $file['name']);
											$res = array('name' => $file['name'], 'full_path' => $path .  $prefix . $file['name'], 'type' => $file['type'], 'size' => $file['size'], 'overwritten' => $exists);
										}
      
                            // store name, path, type and size information
                           
                        } else {
                        	 $this->error()->addError ("FILE_EXISTS"); 
                          //  $this->error .= $this->_error("File \"" . $file['name'] . "\" already exists!");
                        }
                    } else {
                    	 $this->error()->addError ("NOT_A_FILE"); 
                    //    $this->error .= $this->_error("File \"" . $file['name'] . "\" is not a file!");
                    }
                } else {
                  $this->error()->addError ("NOT_ALLOWED"); 
                 //   $this->error .= $this->_error("Content Type \"" .  $file['type'] . "\" for file \"".$file['name']."\" not allowed!");
                }
            } else {
                if (@$file['error'] && $file['error'] != 4) {
                	$this->error()->addError ("UNEXISTANT"); 
                 //   $this->error .= $this->_error("File \"" .  $file['name'] . "\" does not exist!");
                }
            }
    //    } // end for
        if (!$this->error()->getErrors())
{
  if (count ($res) == 0)     
{
$this->error()->addError ("UNEXISTANT_1"); 
return false;
} else
{
 return $res;
}
}

        else
		{
			$this->error()->addError ("UNEXISTANT_2"); 
        	return false;
		}
    }
   $this->error()->addError ("CONFIG"); 
   // $this->_error("Path \"$path\" does not exist or is not writable!");
    return false;
}



/**
* Generates error message
* se elimino el uso del metodo, queda de recuerdo, ahora se usa el manejador de errores.
*/
private function _error($msg) {
    $this->error .= date('Y-m-d H:i:s') . ' | ' . basename($_SERVER['PHP_SELF'])  . ' | ' . $msg . "\n";
}


}


?>