<?php
// Amigo (origen, destino, bloqueo_en_destino)
class perfil_amigo extends OOB_model_type
{

	static protected $public_properties = array(
	
		'id_origen' 		=> 'object-perfil_perfil',
		'id_destino' 		=> 'object-perfil_perfil',
		'bloqueo_destino' 	=> 'isBool',
		'fecha'				=> 'object-Date'

	); // property => constraints
	
	static protected $table = 'perfil_amigo';
	static protected $class = __CLASS__;	
	static $orders = array('nombre','telefono'); 
	protected $hard_delete = true;
	
	// definimos los attr del objeto
	public $origen;
	public $destino;
	public $bloqueo_destino = 0;
	public $fecha;
	
	public function store()
	{
		global $ari;
		
		$is_new = true;
		if ($this->id > ID_UNDEFINED)
		{
			$is_new = false;
		}
		
		if (parent::store())
		{
			// le mandamos una notificacion al destinatario de que lo estan siguiendo
			if ($is_new)
			{
				$notificacion = new perfil_notificacion();
				$notificacion->set('origen', $this->get('origen'));
				$notificacion->set('destino', $this->get('destino'));
				$notificacion->set('fecha', new Date());
				$notificacion->set('tipo', new perfil_notificacion_tipo(1));
				$notificacion->set('enviado', 0);
				$notificacion->store();
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	
	static public function es_amigo(perfil_perfil $origen, perfil_perfil $destino)
	{
		global $ari;
			
		$origen = $ari->db->qMagic($origen->id());
		$destino = $ari->db->qMagic($destino->id());
		
		return( static::getList(0,0,false,false,false,false,false, "AND id_origen = $origen AND id_destino = $destino"));
		
	}

	protected  function isDuplicated()
	{
		
		global $ari;
		$table = static::getTable();
		$id = $this->id;
		 
		 if ($id < ID_UNDEFINED) 					
		{	
			$clausula = "";
		}
		else
		{	
			$clausula = " AND id <> $id ";	
		}
		
		$origen = $ari->db->qMagic($this->id_origen);
		$destino = $ari->db->qMagic($this->id_destino);
		
		
		if (static::getListCount(false, false, false, "AND id_origen = $origen AND id_destino = $destino $clausula") == 0)
		{
			return false;						
		}
		else
		{
			return true;
		}

	}
	
	
	static public function get_mis_amigos($offset = 0)
	{
		$limit = 20;
		return static::_get_mis_('amigos',$offset,$limit);
	}
	
	static public function get_mis_seguidores($offset = 0)
	{
		$limit = 20;
		return static::_get_mis_('seguidores',$offset, $limit);
	}
	
	static public function get_mis_amigos_count()
	{
		return static::_get_mis__count('amigos');
	}
	
	static public function get_mis_seguidores_count()
	{
		return static::_get_mis__count('seguidores');
	}
	
	static public function get_todos_mis_seguidores()
	{
		global $ari;
		
		$perfil = perfil_perfil::existe_usuario($ari->user);
		$perfil = $ari->db->qMagic($perfil[0]->id());
		
		return static::getList(0,0,'fecha','DESC',false,false,false,"AND id_destino = $perfil");
	}
	
	static protected function _get_mis_($que, $offset,$limit)
	{
		global $ari;
		$return = array();
		$i = 0;
		
		if (!oob_validatetext::isNumeric($offset))
		{
			$offset = 0;
		}
				
		$perfil = perfil_perfil::existe_usuario($ari->user);
		$perfil = $ari->db->qMagic($perfil[0]->id());
		
		if ($que == 'amigos')
		{
			$sql = "AND id_origen = $perfil";
			$objeto = 'destino';
		}
		else
		{
			$sql = "AND id_destino = $perfil";
			$objeto = 'origen';
		}
				
		if ($datos = static::getList((int)$offset,(int)$limit,'fecha','DESC',false,false,false,$sql))
		{
			
			foreach ($datos as $d)
			{
				$return[$i]['id'] =  $d->id();
				$return[$i]['perfil']['id'] =  $d->get($objeto)->id();
				$return[$i]['perfil']['nombre'] =  $d->get($objeto)->name();
				$return[$i]['perfil']['foto'] =  $d->get($objeto)->foto();
				$return[$i]['perfil']['bio'] =  $d->get($objeto)->get('bio');
				$return[$i]['bloqueado'] =  $d->get('bloqueo_destino');
				$return[$i]['fecha'] = $d->get('fecha')->format("%d-%m-%Y");
				$i++;
			}
		}
			
		return $return;
	}
	
	static protected function _get_mis__count($que)
	{
		global $ari;
		$return = array();
		
		$perfil = perfil_perfil::existe_usuario($ari->user);
		$perfil = $ari->db->qMagic($perfil[0]->id());
		
		if ($que == 'amigos')
		{
			$sql = "AND id_origen = $perfil";
		}
		else
		{
			$sql = "AND id_destino = $perfil";
		}
		
		return static::getListCount(false,false,false,$sql);
		
	}

	static public function get_mis_amigos_bloque($limit)
	{
		global $ari;
		$return = array();
		$i = 0;

		if (!oob_validatetext::isNumeric($limit))
		{
			$limit = (4*8)-1;
		}
		
		
				
		$perfil = perfil_perfil::existe_usuario($ari->user);
		$perfil = $ari->db->qMagic($perfil[0]->id());
						
		if ($datos = static::getList(0,(int)$limit,'fecha','ASC',false,false,false,"AND id_origen = $perfil"))
		{
			
			foreach ($datos as $d)
			{
				$return[$i]['perfil']['id'] =  $d->get('destino')->id();
				$return[$i]['perfil']['nombre'] =  $d->get('destino')->name();
				$return[$i]['perfil']['foto'] =  $d->get('destino')->foto_miniatura();
				$i++;
			}
		}
			
		return $return;
	}	
}
?>