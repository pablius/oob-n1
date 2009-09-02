<?php

global $ari;
$ari->popup = 1;  // no mostrar el main_frame 


$controles = array();

$controles[0]['text'] = "TextField";
$controles[0]['iconCls'] =  "clstxtstring";
$controles[0]['leaf'] = true;
$controles[1]['text'] = "TextArea";
$controles[1]['iconCls'] =  "clstextarea";
$controles[1]['leaf'] = true;
$controles[2]['text'] = "NumberField";
$controles[2]['iconCls'] =  "clsnumberfield";
$controles[2]['leaf'] = true;
$controles[3]['text'] = "RadioGroup";
$controles[3]['iconCls'] =  "clsradiogroup";
$controles[3]['leaf'] = true;
$controles[4]['text'] = "CheckGroup";
$controles[4]['iconCls'] =  "clscheckgroup";
$controles[4]['leaf'] = true;
$controles[5]['text'] = "DateField";
$controles[5]['iconCls'] =  "clsdatefield";
$controles[5]['leaf'] = true;
$controles[6]['text'] = "TimeField";
$controles[6]['iconCls'] =  "clstimefield";
$controles[6]['leaf'] = true;

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $controles );
$obj_comunication->send(true,true);

?>