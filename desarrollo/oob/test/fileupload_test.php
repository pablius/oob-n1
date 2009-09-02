<?php
/*
 * Created on 03/02/2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
  include ("..\oob_fileupload.php");
  
 function wasSubmitted( $key = 'upload' ) {
    if (@$_POST[$key] == "true")
    return true;
    else
    return false;
}

function dumpAssociativeArray($array) {
    $res = '';
    $header = false;
    if (is_array($array) && sizeof($array)) {
        $res .= "<table border=1>\n";
        foreach(@$array as $values) {
            if (!$header) {
                $res .= "<th>" . implode("</th><th>", array_keys($values)) . "</th>\n";
                $header = true;
            }
            $res .= "<tr>\n";
            foreach($values as $key => $value) {
                $res .= "<td>" . ($value != '' ? $value : "&nbsp;") . "</td>";
            }
            $res .= "</tr>\n";
        }
        $res .= "</table>\n";
    }
    return $res;
}

function debug() {
    $str = '';
    $str .='safemode: "' . (ini_get('safe_mode') ? 'on" (bad idea!)' : 'off"') . "\n";
    $str .='upload_tmp_dir: "' . ini_get('upload_tmp_dir') . "\"\n";
    $str .='local TMP dir: "' . getenv('TMP') . "\"\n";
    $str .='local TEMP dir: "' . getenv('TEMP') . "\"\n";
    $str .='upload_max_filesize: "' . ini_get("upload_max_filesize") . "\"\n";
    $str .='post_max_size: "' . ini_get("post_max_size") . "\"\n";
    return $str;
}

////////////////////////////////////////////////////////////////////////
// show debug information
 echo nl2br(debug()) . "<br>"; 

// only images
$allowedTypes = array("image/bmp","image/gif","image/pjpeg","image/jpeg","image/x-png");
$uploadPath = 'c:/temp';
$overwrite = true;

$up = new OOB_fileupload();
 if (wasSubmitted('_uploaded')) {
    // files were submitted
    echo dumpAssociativeArray($up->upload($uploadPath, $overwrite, $allowedTypes));
 //display form
 } else {
?>
<form action="http://localhost/eclipse/oob/oob/test/fileupload_test.php" method="post" enctype="multipart/form-data">
  <p>
  <input name="archivoa" type="file" size="10">
    <input name="_uploaded" type="hidden" value="true">
  <br>
  <input name="archivob" type="file" size="10">
  </p>
  <p>
    <input name="ok" type="submit" value="ok" >
</p>
  </form>
<?
    
}
// display error


?>
