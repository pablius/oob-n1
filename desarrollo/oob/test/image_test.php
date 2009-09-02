<?
include ("..\oob_imagehandling.php");


$a = new OOB_imagehandling('monte.jpg','300', '300' ,85,'"greyscale(32,22,22)", "bevel()", "roundedges()", "frame()", "dropshadow()", "motionblur()"', true);
//$a->bevel(8,'FFCCCC','330000');
$b = $a->create();
// $a->merge('overlay.png',5,-35,65,'FF0000');
//$b = $a->create();

if ($b != '')
echo "<img src=\"" . $b . "\">";

?>
