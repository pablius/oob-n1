<?php
// Notificacion (origen, destino, mensaje, tipo, estado)

/// guardamos el elemento que le da origen a la notificacion, normalmente va a ser un perfil_mensaje. 
// La idea es poder eliminar todas las notificaciones si se borra el mensaje, o se actualiza.

class perfil_notificacion extends OOB_model_type
{

	static protected $public_properties = array(
	
		'id_origen' 		=> 'object-relation',
		'class_origen' 		=> 'isClean,isCorrectLength-0-255',
		'id_destino' 		=> 'object-perfil_perfil',
		'mensaje' 			=> 'isClean,isCorrectLength-0-9999',
		'fecha'				=> 'object-Date',
		'id_tipo' 			=> 'object-perfil_notificacion_tipo',
		'enviado'			=> 'isBool'

	); // property => constraints
	
	static protected $table = 'perfil_notificacion';
	static protected $class = __CLASS__;	
	static $orders = array('fecha','mensaje'); 
	protected $hard_delete = true;
	
	// definimos los attr del objeto
	public $origen;
	public $destino;
	public $mensaje;
	public $fecha;
	public $tipo;
	public $enviado;
	
	static public function enviar_notificaciones()
	{
	}
	
	static public function get_novedades_usuario()
	{
		global $ari;
		$return = array();
		$i = 0;
		
		$limit = 20;
		
		$perfil = perfil_perfil::existe_usuario($ari->user);
		$perfil = $ari->db->qMagic($perfil[0]->id());
		
		if ($datos = static::getList(0,$limit,'fecha','DESC',false,false,false,"AND id_destino = $perfil"))
		{
			// class, mensaje, link, usuario, fecha
			foreach ($datos as $d)
			{
				$return[$i]['class'] =  $d->get('tipo')->get('css_class');
				$return[$i]['mensaje'] = $d->get('tipo')->get('mensaje');
				
				// @fixme: cada objeto tiene que tener un metodo link_novedad que te de el link para ir a ver esa novedad, asi no tenemos un if acá misterioso.
				if ($d->get('tipo')->id() == 3)
				{
					$return[$i]['link'] = '/perfil/perfil/ver/' . $d->get('origen')->get('perfil')->id(); 
				}
				else
				{
					$return[$i]['link'] = '/perfil/perfil/ver/' . $d->get('origen')->id(); 
				}
				$return[$i]['nombre'] = $d->get('origen')->name();
				$return[$i]['fecha'] = $d->get('fecha')->format("%d-%m-%Y");
				$i++;
			}
		}
			
		return $return;
	}
	
	static public function get_timeline(perfil_perfil $perfil, $offset = 0)
	{
		// buscamos todas las notificaciones que tengamos del tipo "perfil_mensaje", y mostramos los mensajes.
		global $ari;
		$limit = 20;
		$return = array();
		$i = 0;
		$filtro = array();
		$filtro[] = array('value' => $perfil->id(),'field' => 'destino::id','type' => 'list');
		$filtro[] = array('value' => 3,'field' => 'tipo','type' => 'list');
		
		if (!oob_validatetext::isNumeric($offset))
		{
			$offset = 0;
		}
		
		if ($datos = static::getFilteredList((int)$offset, (int)$limit, 'fecha', 'DESC', $filtro))
		{
			foreach ($datos as $d)
			{
				$return[$i]['mensaje'] = $d->get('origen')->interpretar_mensaje();
				$return[$i]['foto'] = $d->get('origen')->foto();
				$return[$i]['fecha'] = $d->get('origen')->get('fecha')->format("%d-%m-%Y");
				$i++;
			}
		}
		return $return;
	}
	
	static public function get_timeline_count(perfil_perfil $perfil)
	{
		// buscamos todas las notificaciones que tengamos del tipo "perfil_mensaje", y mostramos los mensajes.
		global $ari;
	
		$return = array();
		$i = 0;
		$filtro = array();
		$filtro[] = array('value' => $perfil->id(),'field' => 'destino::id','type' => 'list');
		$filtro[] = array('value' => 3,'field' => 'tipo','type' => 'list');
		
		
		return static::getFilteredListCount($filtro);
		
	}
	
}
?>