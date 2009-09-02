<?php
//Mensaje (fecha-hora, mensaje, foto, info_exim_foto)
class perfil_mensaje extends OOB_model_type
{

	static protected $public_properties = array(
	
		'fecha' 		=> 'object-Date',
		'mensaje' 		=> 'isClean,isCorrectLength-0-140',
		'foto'			=> 'isClean,isCorrectLength-0-255',
		'exif' 			=> 'isClean,isCorrectLength-0-9999', // what we want here is: phone, location (GPS), gyroscope.
		'id_perfil'		=> 'object-perfil_perfil'
		
	); // property => constraints
	
	static protected $table = 'perfil_mensaje';
	static protected $class = __CLASS__;	
	static $orders = array('fecha','mensaje'); 
	protected $hard_delete = true;
	
	// definimos los attr del objeto
	public $fecha;
	public $mensaje;
	public $foto;
	public $exif;
	public $perfil;
	
	static public function procesar_mensajes_recibidos()
	{
		// abrimos el mail 
		
		// para cada mail, revisamos si el originador está registrado en el sitio
		
		// si está registrado, cargamos el mensaje
		
		// si no está registrado, le mandamos un mensaje diciendo que el celular no está registrado
	}
	
	public function interpretar_mensaje()
	{
		// convertimos el texto del mensaje en un html, donde los @ son para los usuarios, y los # para los grupos (con links a cada cosa).
		return $this->name() . ': ' . $this->get('mensaje');
	}
	
	static public function get_actualizaciones_usuario($offset = 0)
	{
		global $ari;
		$return = array();
		$i = 0;
		$limit = 20;
		
		if (!oob_validatetext::isNumeric($offset))
		{
			$offset = 0;
		}
				
		$perfil = perfil_perfil::existe_usuario($ari->user);
		$perfil = $ari->db->qMagic($perfil[0]->id());
				
		if ($datos = static::getList((int)$offset,(int)$limit,'fecha','DESC',false,false,false,"AND id_perfil = $perfil"))
		{
			
			foreach ($datos as $d)
			{
				$return[$i]['id'] =  $d->id();
				$return[$i]['mensaje'] =  $d->get('mensaje');
				$return[$i]['foto'] =  $d->foto();
				$return[$i]['fecha'] = $d->get('fecha')->format("%d-%m-%Y");
				$i++;
			}
		}
			
		return $return;
	}
	
	static public function get_actualizaciones_usuario_count()
	{
		global $ari;
		$return = array();
		
		$perfil = perfil_perfil::existe_usuario($ari->user);
		$perfil = $ari->db->qMagic($perfil[0]->id());
		
		return static::getListCount(false,false,false,"AND id_perfil = $perfil");
		
	}
	
	public function store()
	{
	
		if (parent::store())
		{
			// para cada amigo que no tenga bloqueado, crear una notificacion de que escribí el mensaje.
			
			if ($amigos = perfil_amigo::get_todos_mis_seguidores())
			{
			
				foreach ($amigos as $amigo)
				{
					if ($amigo->get('bloqueo_destino') == false)
					{
						
						$notificacion = new perfil_notificacion();
						$notificacion->set('origen', $this);
						$notificacion->set('destino', $amigo->get('origen')); // al que lo origina, por eso es un seguidor!
						$notificacion->set('fecha', new Date());
						
						// si el usuario está tagueado en la foto, mandar un mensaje adicional.
						$notificacion->set('tipo', new perfil_notificacion_tipo(3));
						$notificacion->set('enviado', 0);
						$notificacion->store();
					}
				}
				
			}
			
			// lo agregamos para que salga en nuestro propio timeline.
			$notificacion = new perfil_notificacion();
			$notificacion->set('origen', $this);
			$notificacion->set('destino', $this->get('perfil'));
			$notificacion->set('fecha', new Date());
			$notificacion->set('tipo', new perfil_notificacion_tipo(3));
			$notificacion->set('enviado', 1);
			$notificacion->store();
			
				
			
			return true;
		}
		else
		{
			return false;
		}
	
	}
	
	public function delete()
	{
		// cuando borramos  un mensaje, lo que hacemos es borrar todas las notificaciones relacionadas con ese mensaje.
		$id = $this->id();
		$filtro[] = array('value' => $id,'field' => 'origen(perfil_mensaje)','type' => 'list');
		
		if ($notificaciones = perfil_notificacion::getFilteredList(0, 0, false, false, $filtro))
		{

			foreach ($notificaciones as $notificacion)
			{
				$notificacion->delete();
			}
		}
		
		return parent::delete();
		
	}
	
	public function foto()
	{
		if ($this->foto != '')
		{
			return '/archivos/fotos/' . $this->foto;
		}
		else
		{
			return '';
		}
	}
	
	public function name()
	{
		return $this->get('perfil')->name();
	}
}
?>