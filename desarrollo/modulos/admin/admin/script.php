<?php
#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// Script que genera el MAIN FRAME

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

PhpExt_Javascript::sendContentType();



//--------------------------------------------------MENU TOP-----------------------------------------------------------------------------------------------------------//

//items del panel top
//Boton_Salir OnClick


$msg_config = PhpExt_MessageBoxOptions::createMsgOptions()
			  ->setTitle($ari->title)
			  ->setMsg("¿Est&aacute; seguro que desea salir?")
			  ->setWidth(300)
			  ->setButtons( new PhpExt_JavascriptStm("{ cancel:'Cancelar', yes:'Salir', ok:'Salir y Guardar' }") )
			  ->setMultiline(false)
			  ->setFn(PhpExt_Javascript::variable("function( Result ){
					if(Result == 'yes'){
						window.location = '/seguridad/logout';
					}
					
					if(Result == 'ok'){
						window.location = '/seguridad/logoutsave';
					}			  			  
				}")) ;
	
$msgbox = PhpExt_Javascript::functionDef(null, 
		PhpExt_MessageBox::show($msg_config)
    );

	

//Boton_Mi_Cuenta OnClick // function addTab( Title, Url, Add, Params, cache, tab_id ) // addTab(i.title,i.url,true,i.params,true,i.id);
$function_add_tab = PhpExt_Javascript::callfunction( "addTab",												   
											array( "'Mi Cuenta'",
												   "'/seguridad/user/update'",
												   "true" ) );
$handler_mi_cuenta = PhpExt_Javascript::functionDef("",$function_add_tab,array("e"));


$sub_menu_bienvenido = new PhpExt_Menu_Menu();
$sub_menu_bienvenido->addCheckItem( "Cuenta", "Mi Cuenta", $handler_mi_cuenta );
$sub_menu_bienvenido->addCheckItem( "Salir2", "Salir", $msgbox );
	
$menu = new PhpExt_Toolbar_Toolbar();
$menu->addFill("leftfill"); //para que los botones estena a la derecha
$menu->addButton( "welcome", "<u>Bienvenido ". $ari->get("user")->name()."</u>");
$menu->addButton( "Salir", "Salir", null, $msgbox );

//pongo los iconos
$boton_mi_cuenta=$sub_menu_bienvenido->getItem("Cuenta");
$boton_mi_cuenta->setIcon("/images/ext/user.png");

$BotonSalir2=$sub_menu_bienvenido->getItem("Salir2");
$BotonSalir2->setIcon("/images/ext/exit.png");

//agrego el submenu
$boton_welcome = $menu->getItem("welcome");
$boton_welcome->setMenu($sub_menu_bienvenido);
		   
//fin items

$menu_top = new PhpExt_Panel();
$menu_top->setHeader(true)
		 ->setBaseCssClass("PanelClass")
         ->setHeight(70)  
         ->setTopToolbar($menu)
         ->setIconCssClass("HeaderClass");


//--------------------------------------------MENU ACORDION-------------------------------------------------------------------------------------------------------//

//trae los items del menu
$tree_loader = new  PhpExt_Tree_TreeLoader();
$tree_loader->setDataUrl("/admin/menu");

//armo el nodo root (no es visible)
$root = new PhpExt_Tree_AsyncTreeNode();
$root->setText("Principal Node")
     ->setDraggable(false)	 
     ->setId("Principal_Node")
	 ->setExpanded(true)
	 ->setCssClass("feeds-node")
     ->expandChildNodes(false);

$menu_acordion = new PhpExt_Tree_TreePanel();
$menu_acordion->setTitle("Menu Principal")
	  		  ->setId("treePanel")			 
			  ->setWidth(250)
			  ->setHeight('fill')			 			  
			  ->setLines(false)
			  ->setAutoScroll(true)
			  ->setCollapseFirst(false)
			  ->setSingleExpand(true)
			  ->setUseArrows(true)
			  ->setRootVisible(false)			 
			  ->setRoot($root)					 			
			  ->setLoader($tree_loader);

//----------------------------------------PANTALLA PRINCIPAL-------------------------------------------------------------------------------------------------------

$tab_layout = new PhpExt_Layout_TabLayout();
$tab_layout->setDeferredRender(false);

$principal = new PhpExt_TabPanel();
$principal->setActiveTab(0)
		  ->setId("TabPanel")
		  ->setHideMode(PhpExt_Component::HIDE_MODE_OFFSETS)
		  ->setLayout($tab_layout)
		  ->setEnableKeyEvents(true)
		  ->setenableTabScroll(true);
		  
		  
$onrender = "




//beforeclose
t.on( 'beforeremove', function(t,c){

var cnx = new Ext.data.Connection();
Ext.Ajax.request({ url : '/admin/closetab',
				method : 'POST',
				params : 'tab_id=' + c.id 					   
				 });

});
";		  

$principal->attachListener("render", new PhpExt_Listener( PhpExt_Javascript::functionDef(null, $onrender, array("t","r","i") )));		  

$module = new OOB_module('About');

$template_dir = $module->admintpldir(). "/about.tpl";
$html = $ari->t->fetch( $template_dir ); 

$bienvenido = new PhpExt_Panel();
$bienvenido->setTitle("Bienvenido")
		   ->setId("panel_bienvenido")			
		   ->setAutoScroll(true)
		   ->setBodyStyle("padding:10px 10px 0")
		   ->setHtml($html);
		   
$principal->addItem($bienvenido, new PhpExt_Layout_TabLayoutData(true));



$add_tab_function = "

//oob_download
Ext.DomHelper.append(document.body, {
                    tag: 'form',
                    id:'download_form',
                    frameBorder: 0,
                    width: 0,
                    height: 0,
                    css: 'display:none;visibility:hidden;height:0px;'
					}); 

function oob_download( url, params ){

var form = document.getElementById('download_form');

var inputs = form.getElementsByTagName('input');

for (i = 0; i < inputs.length; i++){
   form.removeChild(inputs[i]); 
}

if( Ext.isArray(params) ){
	Ext.each( params, function( item, index ){
		el = document.createElement('input');
		el = form.appendChild(el);
		el.name = item.name;
		el.type = 'hidden';
		el.value = item.value;	
	});
}

form.method = 'post';
form.action = url;
form.submit();

}
Ext.apply( Ext,{ oob_download : oob_download } );
//fin oob_download					

var msgconfig = {
		   title :'Error',
		progress : false,
		    wait : false,
		     msg : 'Se produjo un error al cargar la pagina.',
		 buttons : Ext.Msg.OK,		 
		    icon : Ext.MessageBox.ERROR ,
			  fn : function(c,t,o){
				Ext.MessageBox.getDialog().setTitle(''); 
				Ext.MessageBox.getDialog().hide();
			  }	
		}



Ext.Ajax.on('requestexception',function(request,response,f,g,h){
	
		
	switch( response.status ){
	case 401:

	var loginDialog = new Ext.ux.form.LoginDialog({
				modal : true,
				title : 'Nutus Econom&iacute;a',
			  message : 'Por su seguridad debe logearse nuevamente,<br /> ya que no ha utilizado el sistema por más de 30 minutos',
		usernameLabel : 'Usuario',
		passwordLabel : 'Contrase&ntilde;a',
		 cancelButton : 'Cerrar',
		  loginButton : 'Enviar',
		  failMessage : 'Usuario o contrase&ntilde;a no v&aacute;lida.',
				  url : '/seguridad/login_ajax'			
			});

	
	loginDialog.show();

	loginDialog.on('success',function(){
	request.request(f);
	});

	break;
	case 400:
		
		var msgconfig = {
		   title :'Error 400',				    
		     msg : response.getResponseHeader['message'],
		 buttons : Ext.Msg.OK,		 
		    icon : Ext.MessageBox.ERROR ,
			  fn : function(c,t,o){
				Ext.MessageBox.getDialog().setTitle(''); 
				Ext.MessageBox.getDialog().hide();
			  }	
		}
	
		Ext.MessageBox.hide();	 
		var win = Ext.MessageBox.getDialog();		
		
		win.on('beforehide',function(){
			
			if( this.title == 'Error 400'){
					return false;
			}		
		});
		
		Ext.Msg.show(msgconfig);
		Ext.getCmp('status_bar').clearStatus({useDefaults:true});
		
	break;
	case 404:
		var msgconfig = {
		   title :'Error 404',				    
		     msg : response.getResponseHeader['message'],
		 buttons : Ext.Msg.OK,		 
		    icon : Ext.MessageBox.ERROR ,
			  fn : function(c,t,o){
				Ext.MessageBox.getDialog().setTitle(''); 
				Ext.MessageBox.getDialog().hide();
			  }	
		}
		Ext.Msg.show(msgconfig);
		Ext.getCmp('status_bar').clearStatus({useDefaults:true});	
	break;
	case 500:		
        Ext.MessageBox.hide();	 
		var win = Ext.MessageBox.getDialog();		
		
		win.on('beforehide',function(){
			
			if( this.title == 'Error'){
					return false;
			}		
		});
		
		Ext.Msg.show(msgconfig);
		Ext.getCmp('status_bar').clearStatus({useDefaults:true});
		 
	break;
	case 9001:
		Ext.MessageBox.alert('".$ari->title."',response.getResponseHeader['message']); 
		Ext.getCmp('status_bar').clearStatus({useDefaults:true});
	break;	
	}
});



Ext.Ajax.on('requestcomplete', function(request,response,f,g,h){
try
  { 
  
    //Ext.MessageBox.updateProgress(1);
    //Ext.MessageBox.hide();	
	//alert(response.getResponseHeader['Content-Type']);
	

	 
		
  }
catch(err)
  {
  //alert(err.description);
  }
  


}, this);



Ext.Ajax.request({url: '/admin/getcache',
		  method: 'POST',
  		 success: function(responseObject){												
							   json = Ext.decode(responseObject.responseText);							   
							   Ext.each( json, function(i){									
									addTab(i.title,i.url,true,i.params,true,i.id);
							   });						   
					   }				   
				});
				
				

 
//funcion para agregar las tabs 
function addTab( Title, Url, Add, Params, cache, tab_id ){

var panel_tabs = Ext.getCmp('TabPanel'); //obtengo el tabpanel que contiene todas las tabs
var cnx = new Ext.data.Connection(); //creo un nuevo objeto conexion
var tab_id; //defino la variable tab_id, tiene el id de la tab que se va agregar
			
	//pongo la barra de estado(cargando...)
	Ext.getCmp('status_bar').showBusy();
			
	//obtengo un id unico para el contenedor de los contenidos que voy a cargar
	var id = Ext.id();

	//function para agregar definitivamente la tab
	var add = function( tab_id ){
	
	
			//si quiero agregar una tab nueva
									

		Ext.Ajax.request( { url : Url,
						method : 'POST',						
						params : Params,
					   success : function( responseObject ){												
						var respuesta = responseObject.responseText;	

									if(Add){
									
										   var tab = new Ext.Panel({
														id : tab_id,
													 title : Title,
													layout : 'fit',													   
												  closable : true,
											deferredRender : false,
													  html : '<div style=\"height:100%;width:100%;\" id=\"' + id + '\"></div>',
												autoScroll : true							 							
										   });
										   
										   panel_tabs.add(tab);
										   tab.show();													   					
									}
									else //si quiero agregar en la tab que esta activa
									{
									
										var active_tab = panel_tabs.getActiveTab();
										
										var cnx = new Ext.data.Connection();
										Ext.Ajax.request({ url : '/admin/closetab',
														method : 'POST',
														params : 'tab_id=' + active_tab.id 					   
														 });
										
										
										active_tab.setTitle(Title);
										active_tab.body.dom.innerHTML = '<div style=\"height:100%;width:100%;\" id=\"' + id + '\"></div>';
									}
																		
									//una vez insertado el contenedor con el id unico , se procede a insertar los
									//datos en el mismo
									
									//si el contenido a cargar es un html
									if( responseObject.getResponseHeader['Content-Type'] == 'text/html' ){		
										Ext.get(id).dom.innerHTML = respuesta;			  
									}
									else //si el contenido es un json(extjs)
									{
										//ejecuto la respuesta y hago un render de la variable contenido
										//sobre el contenedor
										
										eval(respuesta);				
										contenido.render(Ext.get(id));									
										
										//llamo el evento para que aplique los filtros
																				
											contenido.fireEvent( 'applyfilters', tab_id );								
										
									}		
									
														
									Ext.getCmp('status_bar').clearStatus({useDefaults:true});
									
							return true;			
					}});


			}//end function addtab

		
		//si !cache , quiere decir que la tab no esta en cache, por lo tanto llamo a newtab
		//para que la cachee y me devuelve el tab_id
		if(!cache){
			  Ext.Ajax.request({url: '/admin/newtab',
						method: 'POST',
						params: 'url=' + Url + '&title=' + Title + '&params=' + Params ,
					   success: function(responseObject){												
							   json = Ext.decode(responseObject.responseText);							  							   
							   //este tab_id luego es usado por la funcion tab_id
							   tab_id = json.id;							   
							   add(json.id); 							   							   					   								
					   }
				});		
		}
		else
		{
					add(tab_id); 							   
		}		
	
		
	}
	
				
	
	Ext.apply( Ext,{ addTab : addTab } );
	
	
	var map = new Ext.KeyMap(document, [
				{
					key: \"t\",
					ctrl:true,
					shift:true,
					fn: function(){
						var panel_tabs = Ext.getCmp('TabPanel'); //obtengo el tabpanel que contiene todas las tabs
						var active_tab = panel_tabs.getActiveTab();
						 if(active_tab){
							panel_tabs.remove(active_tab);
						 }//end if
										
					}
				}
				]);
		

	//esto dejarlo siempre al ultimo por que hace el fadeout del precargador( osea lo oculta )
	var hideMask = function () {
        Ext.get('loading').remove();
        Ext.fly('loading-mask').fadeOut({
            remove:true
        });
    }

    hideMask.defer(250);			
	
	
	";
									


$add_tab_invoke = PhpExt_Javascript::callfunction("addTab",array("n.id[0]","n.id[1]","true","''","false"))->output()."return false;";									
$add_tab_invoke_html = PhpExt_Javascript::callfunction("addTab",array("n.id[0]","n.id[1]","false","''","false"))->output()."return false;";									

$get_tree_panel = PhpExt_Element::getCmp('treePanel');
$if_leaf=PhpExt_Javascript::functionNoDef("if",$add_tab_invoke,array("n.leaf"));

$if_leaf_html=PhpExt_Javascript::functionNoDef("if",$add_tab_invoke_html,array("n.leaf"));

$add_tab_onclick= PhpExt_Javascript::functionDef(null,$if_leaf,array("n"));   
$add_tab_oncontextmenu= PhpExt_Javascript::functionDef(null,$if_leaf_html,array("n"));   

$output_add_tab_onclick=$get_tree_panel->on("click", $add_tab_onclick);
$output_add_tab_oncontextmenu=$get_tree_panel->on("contextmenu", $add_tab_oncontextmenu);
												
$output_add_tab_function=new PhpExt_JavascriptStm($add_tab_function);


//-----------------------------------------------------------BARRA DE ESTADO--------------------------------------------------------------------------------------------

$function_win_open=PhpExt_Javascript::callfunction("window.open",array("'http://www.nutus.com.ar'"));
$handler_abrir_pagina=PhpExt_Javascript::functionDef("",$function_win_open,array("e"));

$function_win_open2=PhpExt_Javascript::callfunction("window.open",array("'http://soporte.nutus.info'"));
$handler_abrir_pagina2=PhpExt_Javascript::functionDef("",$function_win_open2,array("e"));

$barra_estado=new PhpExt_Toolbar_StatusBar();
$barra_estado->setId("status_bar");
$barra_estado->setDefaultText("Terminado");
$barra_estado->addButton("ayuda","Ayuda on-line",null,$handler_abrir_pagina2);
$barra_estado->addButton("Pagina","&#169; Nutus 2009",null,$handler_abrir_pagina);



$status_bar = new PhpExt_Panel();
$status_bar->setBottomToolbar($barra_estado);



//USO UN VIEWPORT YA QUE SE ADAPTA AL ANCHO DE LA PAGINA
//creo uno nuevo y le agrego todos los items setando el area
		  
$contenedor= new PhpExt_Viewport();
$contenedor->setLayout(new PhpExt_Layout_BorderLayout());
$contenedor->addItem($menu_top, PhpExt_Layout_BorderLayoutData::createNorthRegion());
$contenedor->addItem($menu_acordion,PhpExt_Layout_BorderLayoutData::createWestRegion());
$contenedor->addItem($principal, PhpExt_Layout_BorderLayoutData::createCenterRegion());
$contenedor->addItem($status_bar, PhpExt_Layout_BorderLayoutData::createSouthRegion());

//FUNCTIONS DE USO GENERAL

$format_money = "	
function FormatMoney(v,sign){				
    v = (Math.round((v-0)*100))/100;
    v = (v == Math.floor(v)) ? v + '.00' : ((v*10 == Math.floor(v*10)) ? v + '0' : v);
    v = String(v);        
        if(v.charAt(0) == '-'){
            return '-' + sign + v.substr(1).replace('.',',');
        }
    return sign +  v.replace('.',',');
}
";

$unformat_money = "
function unformatMoney(num) {
	var value = num.replace(',','.');		
	return value.replace(/([^0-9\.\-])/g,'')*1;	
}
";

echo PhpExt_Ext::OnReady(
	 PhpExt_QuickTips::init(),
	 $contenedor->getJavascript(false, "Contenedor"),	
	 $contenedor->render(PhpExt_Javascript::inlineStm("document.body")),		
	 $output_add_tab_function->output(),	 
	 $output_add_tab_onclick,
	 $output_add_tab_oncontextmenu,
	 $unformat_money,
	 $format_money
	 );
?>
