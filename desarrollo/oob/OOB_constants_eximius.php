<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/

//Constants used in the modules >> 114

 define("NO_OBJECT",false);
 define("SQL_CACHE",00);
 define("DELETED",9);
 define("USED",1);
 define("ID_UNDEFINED",-1);
 define("ID_MINIMAL",0);
 define("MAX_LENGTH",255);
 define("MAX_LENGTH_TEXT",4294967295); 
 define("OPERATOR_DISTINCT","<>");
 define("OPERATOR_EQUAL","=");
 define("OPERATOR_GREATER",">");
 define("OPERATOR_SMALLER","<");

 define("ID_OPERATOR_GREATER","1");
 define("ID_OPERATOR_SMALLER","2");
 define("ID_OPERATOR_EQUAL","3");
 
 define("USER",'u');
 define("GROUP",'g');
 define("MODULE",'m');
 define("ROLE",'r');
 define("TEAM",'t');
 define("ACCOUNT",'a');
 define("EMPLOYEE",'e');
 
 define("NO",0);
 define("YES",1);
 
 define("ALL",'all');
 define("CHECKED",'checked');
 define("UNCHECKED",'unchecked');
 define("ANONIMO",1);
 define("NO_ANONIMO",0);
 define("NO_MENU",0);
 define("IN_MENU",1);
 define("ALL_MENU",-1);
 define("TREE_ROOT",0);
 
 define("ACCOUNT_COMPANY",1);
 define("ACCOUNT_PERSON",2);
 define("ACCOUNT_CONTACT",3);
 define("OPPORTUNITY_COMPANY",4);
 define("OPPORTUNITY_PERSON",5);
 
 //calendar
 define ("JANUARY",1);
 define ("FEBRUARY",2);
 define ("MARCH",3);
 define ("APRIL",4);
 define ("MAY",5);
 define ("JUNE",6);
 define ("JULY",7);
 define ("AUGUST",8);
 define ("SEPTEMBER",9);
 define ("OCTOBER",10);
 define ("NOVEMBER",11);
 define ("DECEMBER",12);
 
 //constantes de los dias
 //para date domingo es 0 y sabado 6
 define ("SUNDAY",0);
 define ("MONDAY",1);
 define ("TUESDAY",2);
 define ("WEDNESDAY",3);
 define ("THURSDAY",4);
 define ("FRIDAY",5);
 define ("SATURDAY",6);
 
 //controls
 define ("CONTROL_BOOL",1);
 define ("CONTROL_STRING",2);
 define ("CONTROL_DATE",3);
 define ("CONTROL_NUMBER",4);
 define ("CONTROL_OPTION",5);
 define ("CONTROL_FILE",6);
 define ("CONTROL_IMAGE",7);
 define ("CONTROL_CHECK",8);
 define ("CONTROL_AREA",9);
 define ("CONTROL_TIME",10);
 define ("CONTROL_EMPLOYEE",11);
 define ("CONTROL_CURRENCY",12);
 define ("CONTROL_CALIFICATION",13);
 
 //para los turnos de empleados
 define ("NO_SHIFT",-1);
 define ("CUSTOM_SHIFT",0); 
 
 //
 define ("ONLINE_EMAIL",2);
//
 define ("ACCOUNT_SEPARATOR",";");
 define ("EMPLOYEE_SEPARATOR",";");
 define ("NAME_SEPARATOR",",");
 define ("ID_SEPARATOR_URL","|");
 define ("ITEM_SEPARATOR_URL","_");
  
 //
 define ("FIELD_SEPARATOR","#");
 define ("ITEM_SEPARATOR","@"); 
 define ("ID_SEPARATOR","-");  
 //
 define("FIXED_CHANGE", "1");
 define("FLOAT_CHANGE", "2");
 
 //
  define ("ADDRESS_FACTURACION",1);
  define ("ADDRESS_ENTREGA",2);
  define ("ADDRESS_HOME",4);
  
 //
  define ("ACTION_NEW",'new');
  define ("ACTION_UPDATE",'update');
  define ("ACTION_DELETE",'delete'); 
  define ("ACTION_MOVE",'move'); 
  define ("ACTION_COPY",'copy'); 
  define ("ACTION_RELEASE",'release'); 
 
 //
  define ("SEARCH_NORMAL","normal");
  define ("SEARCH_ADVANCED","advanced"); 
 
 //
 define ("COLOR_DELETE",'#FFF0F5');
 define ("COLOR_WHITE",'#FFFFFF');
 define ("COLOR_ERROR",'#FF0000');
 define ("COLOR_COMPANY",'lightyellow');
 define ("COLOR_PERSON",'navajowhite');
 define ("COLOR_COMPANYPERSON",'lavender'); 
 
 //
 define ("APPOINTMENT_ALL",'0');
 define ("APPOINTMENT_OWNER",'1');
 define ("APPOINTMENT_ASIGNED",'2');
 
 //
 define ("CUSTOM_REPEAT", "0");
 define ("MONTHLY_REPEAT", "30");
 define ("ANNUAL_REPEAT", "365");
 define ("DAILY_REPEAT", "1");
 define ("WEEKLY_REPEAT", "7");
 define ("NO_REPEAT", "-1");
 
 //color
 define ("SELECTED_COLOR", "#00FFFF");
 
 //
 define ("PHONE_LOG", "1"); 
  
 //
 global $array_validity;
 $array_validity = array('0'=>'Sin Validez','365'=>'1 año','180'=>'6 meses','90'=>'3 meses','30'=>'1 mes'); 
 //
 
 define ("ORDER_ASC", "1"); 
 define ("ORDER_DESC", "0");
 
 //contantes para la visualizacion de mails
 define("MAIL_FROM_LENGTH", 20);  
 define("MAIL_SUBJECT_LENGTH", 50);  
 define("ENCRYPT_SEED", "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890");
 
 define ("CONTENT_HTML", "html"); 
 define ("CONTENT_TEXT", "text"); 
 
 //----- Eximius -----
define("ID_PASO_CANCELA",-2);
define("ID_PASO_TERMINA",-2);
define("STR_TRADUCIR","Traducir");
define("ID_PASO_TRADUCIR",0);
define("ID_ESTRUCTURA_TRADUCIR",0);
define("CLEARED",10);

define("UNSPECIFIED",0);
define("ESTRUCTURA",'e');
define("ID_SECCION_DEFINIBLE",-1);
define("ID_CONTENEDOR_DEFINIBLE",-1);
define("ID_SECCION_HEREDADA",-2);
define("NO_CONTENEDOR",-2);
define("NO_VERSION",0);
define("LAST_VERSION",-1);
define("ALL_STATUS",200);

define("ID_BAJA",1);
define("ID_MEDIA",2);
define("ID_ALTA",3);
define("FECHA_NULL","0000-00-00 00:00:00");
define("ID_GENERICO",-5);
define("ID_MODIFICA",2);

define("INACCESIBLE",-1);
define("INCONSISTENTE",-2);

//ESTADOS DEL WF
define("ID_ESTADO_PENDIENTE",100);
define("ID_ESTADO_PUBLICADO",101);
define("ID_ESTADO_PROCESO",102);
define("ID_ESTADO_EXPIRADO",103);
define("ID_ESTADO_RECHAZADO",104);
define("ID_ESTADO_APROBADO",105);


//orden de instancias publicadas
define("PUBLICACION_ASC",1);
define("PUBLICACION_DESC",2);
define("NOMBRE_ASC",3);
define("NOMBRE_DESC",4);
define("POSICION_ABSOLUTA",5);

//listar tipos elementos de columna
define("VISTA",1);
define("VISTA_FOTO",2);
define("LISTA",3);
define("BREVE",4);
define("VISTA_ELEM_REL",5);

define("VISTAS",1);
define("LISTAS",2);
define("BREVES",3);

define("ANONIMOUS",-2);

//Elementos Adjuntos
define("ARCHIVO",1);
define("BANNER",2);
define("IMAGEN",3);
define("FORMULARIO",4);

define("PORTADA",1);

define("MAX_LENGTH_LONGTEXT",4294967295);
define("OPERATOR_IN","IN");
define("OPERATOR_NOT_IN","NOT IN");

//mostrar elementos de formulario
define("TODOS","t");
define("INDEPENDIENTES","i");

//estados recordatorio
define("PENDIENTE", 0);
define("ENVIADO", 1);



//-------------------------------------------------------------------------
//-------------------------------------------------------------------------

//Medios Comunicacion
 define("COMUNICACION_NORMAL",1);
 define("COMUNICACION_AUTORIZACION",2);
 define("COMUNICACION_MENSAJERIA",3);

 //mensaje_solicitudautorizacion
 //perfil_respuesta
 define("V_PENDIENTE",1);
 define("RESPONDIDA",2);
 define("AUTORIZADO",2);
 define("RECHAZADO",3);
 define("V_ENVIADO", "4");
 define("REGISTRADA", "5");
 define("V_TODOS", "0");
 define("PENDIENTE_ENVIO", "6"); 
 define("ENVIADA", "4");

 //tipo relacion
 define ("AMISTAD",1);
 define ("MATRIMONIO",2);
 define ("ROMANCE",3);
 define ("AVENTURA",4);
 define ("VIAJE",5); 

 //intervalos de Edad y Altura permitidos para Perfil
 define("MAX_AGE",100);
 define("MIN_AGE",18); 
 define("MAX_HEIGHT",219);
 define("MIN_HEIGHT",100); 

 define("SIN_RESPUESTA",1);

 define ("MASCULINO",'0');
 define ("FEMENINO",'1');
 define ("V_UNSPECIFIED",'-2');


 //Tipos de usuarios para mailing
 define ("SIN_PERFIL",1);
 define ("CON_PERFIL",2);
 define ("ACT_1MES",3);
 define ("ACT_3MES",4);

 //periodo repeticion mailing
 define("SEMANAL",1);
 define("MENSUAL",2);
 define("TRIMESTRAL",3);

 //tipos de eventos
 define ("SOLICITUDES",1);
 define ("MENSAJES",2);
 define ("VISITAS",3);
 define ("PREGUNTAS",4);

 define("ENABLED",1);

?>