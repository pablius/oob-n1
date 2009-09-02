<?php

global $ari;
seguridad::RequireLogin();

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

include_once 'PhpExt/Javascript.php';
PhpExt_Javascript::sendContentType();

include_once 'PhpExt/Ext.php';
include_once 'PhpExt/Form/FormPanel.php';
include_once 'PhpExt/Config/ConfigObject.php';
include_once 'PhpExt/Form/TextField.php';
include_once 'PhpExt/Form/TimeField.php';
include_once 'PhpExt/Form/FieldSet.php';
include_once 'PhpExt/Form/HtmlEditor.php';
include_once 'PhpExt/Button.php';
include_once 'PhpExt/Panel.php';
include_once 'PhpExt/TabPanel.php';
include_once 'PhpExt/QuickTips.php';
include_once 'PhpExt/Layout/ColumnLayout.php';
include_once 'PhpExt/Layout/FormLayout.php';
include_once 'PhpExt/Layout/FitLayout.php';
include_once 'PhpExt/Layout/AnchorLayoutData.php';
include_once 'PhpExt/Layout/ColumnLayoutData.php';
include_once 'PhpExt/Form/PasswordField.php';
include_once 'PhpExt/Form/ComboBox.php';
include_once 'PhpExt/Layout/TabLayoutData.php';
include_once 'PhpExt/Layout/FormLayout.php';
include_once 'PhpExt/Layout/FormLayoutData.php';

//CONTROLES
$TxtUsuario  = PhpExt_Form_TextField::createTextField("TxtUsuario","Usuario")
               ->setAllowBlank(false);
$TxtPass     = PhpExt_Form_PasswordField::createPasswordField("TxtPass","Contrase&ntilde;a");
$TxtPassRep  = PhpExt_Form_PasswordField::createPasswordField("TxtPassRep","Repetir");
$TxtEmail    = PhpExt_Form_TextField::createTextField("TxtMail","Email", null, PhpExt_Form_FormPanel::VTYPE_EMAIL);               


$CboEstado = PhpExt_Form_ComboBox::createComboBox("CboEstado","Estado")
			->setEmptyText("Select a state...")
			->setEditable(false);

//FIN CONTROLES      

$Bienvenido = new PhpExt_Panel();
$Bienvenido->setAutoScroll(true)
		   ->setLayout(new PhpExt_Layout_FormLayout())
		   ->setBodyStyle("padding:10px 10px 0");			
			
$Bienvenido->addItem($TxtUsuario);
$Bienvenido->addItem($TxtPass);
$Bienvenido->addItem($TxtPassRep);
$Bienvenido->addItem($TxtEmail);
$Bienvenido->addItem($CboEstado);


												

echo $Bienvenido->getJavascript(false);

 
?>
