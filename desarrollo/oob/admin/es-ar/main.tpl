<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>

<head>
<title>{ $title }</title>
<meta http-equiv="Content-Type" content="text/html; charset={$encoding}" />
<META name="Copyright" content="{$author}">
<meta name="description" content="{$description}" />
<meta name="keywords" content="{$keywords}" />

<link href="{$webdir}/oob/admin/styles.css" rel="stylesheet" type="text/css" />

</head>

<body>



<div id="loading-mask" style=""></div>
<div id="loading">
    <div class="loading-indicator">
		<img src="/images/extanim32.gif" width="32" height="32" style="margin-right:8px;float:left;vertical-align:top;"/>{$title}
		<br/>
		<a href="http://www.nutus.com.ar">Nutus</a><br /><span id="loading-msg">Cargando estilos...</span></div>
</div>


<!--LIBRERIAS EXT-JS-->
<link rel="stylesheet" type="text/css" href="{$webdir}/scripts/ext/resources/ext-2.0.2/resources/css/ext-all.css"> 
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/ext-2.0.2/adapter/ext/ext-base.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/ext-2.0.2/ext-all-debug.js"></script>

<script type="text/javascript">document.getElementById('loading-msg').innerHTML = 'Cargando motor del sistema...';</script>


<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/menu/EditableItem.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/menu/RangeMenu.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/menu/ListMenu.js"></script>

<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/grid/GridFilters.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/grid/filter/Filter.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/grid/filter/StringFilter.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/grid/filter/DateFilter.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/grid/filter/ListFilter.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/grid/filter/NumericFilter.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/grid/filter/BooleanFilter.js"></script>

<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/nutus/Ext.form.Action.Submit.js"></script>

<link rel="stylesheet" type="text/css" href="{$webdir}/scripts/ext/resources/extjs-ux/grid/filter/resources/style.css"> 

<link rel="stylesheet" type="text/css" href="{$webdir}/scripts/ext/resources/extjs-ux/Multiselect/Multiselect.css"> 
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/Multiselect/DDView.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/Multiselect/ItemSelector.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/Multiselect/Multiselect.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/Multiselect/Multiselectfield.js"></script>

<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/app/ext-searchfield.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/app/fit-to-parent.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/grid/CheckColumn.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/form/MaskFormattedTextField.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/form/InputTextMask.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/form/Ext.ux.UploadPanel.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/form/Ext.ux.FileUploader.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/form/Ext.ux.form.BrowseButton.js"></script>

<link rel="stylesheet" type="text/css" href="{$webdir}/scripts/ext/resources/extjs-ux/form/logindialog/Ext.ux.form.LoginDialog.css"> 
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/form/logindialog/Ext.ux.form.LoginDialog.js"></script>
<script type="text/javascript" src="{$webdir}/scripts/ext/resources/extjs-ux/form/logindialog/Ext.ux.form.IconCombo.js"></script>


<script type="text/javascript">document.getElementById('loading-msg').innerHTML = 'Cargando componentes de usuario...';</script>


<link rel="stylesheet" type="text/css" href="{$webdir}/scripts/ext/resources/extjs-ux/form/css/filetree.css"> 
<link rel="stylesheet" type="text/css" href="{$webdir}/scripts/ext/resources/extjs-ux/form/css/icons.css"> 


<script type="text/javascript">document.getElementById('loading-msg').innerHTML = 'Carga completa. Iniciando...';</script>

<!--CARGO EL SCRIPT GENERADO POR PHP-EXT-->
<script type="text/javascript" src="{$webdir}/admin/script"></script>

</body>
</html>