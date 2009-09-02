<?php

class admin_session_state extends OOB_model_type
{

	//detalle
	static protected $public_properties = array(	
			'url' 		=> 'isClean',			
			'id_user'   => 'object-OOB_user',
			'params'    => 'isCorrectLength-0-4000',
			'title'     => 'isClean',
			'selected'  => 'isClean',
			'filters'   => 'isCorrectLength-0-4000'
	); // property => constraints
	
	static protected $table = 'admin_session_state';
	static protected $class = __CLASS__;	
	protected $hard_delete = true;
	static $orders = array('id');
	
	//definimos los attr del objeto
	public $url;
	public $id_user;
	public $selected;
	public $title;
	public $params;
	public $filters;
		
	// //borra el cache de tabs de un usuario determinado
	static public function clear_tab_cache( $tab_id = false ){
		global $ari;
		static::garbage_colector();
		if(!$tab_id){
		
				//filtro por usuario
				$filtros = array();
				$filtros[] = array( "field"=>"user", "type"=>"list", "value"=>$ari->user->id() );
				
					if( $cache_list = admin_session_state::getFilteredList( false, false, false, false, $filtros ) ){		
						foreach ( $cache_list as $cache ){							
							$cache->delete();							
						}
					}
					
		}
		else 
		{
				//creo el objeto con el id del tab 
				if( $tab = new admin_session_state( $tab_id ) ){ 				
					//elimino el obj
					return  $tab->delete();
				}
				else 
				{
					return false;
				}
		
		
		}
	
	}//end function
	
	//agrega al cache una tab y devuelve el id de la misma
	public function add_tab_cache( $url , $title , $params ){
		global $ari;
		static::garbage_colector();
		
		// protección para no querer grabar tabs si no tenes usuario (no se porque pasa, pero pasa).
		if ($ari->user == false)
		{
			return 9; // @fixme: diganle a JPC que me explique porque usamos el 9?
		}
		
		$tab_cache = new admin_session_state();
		$tab_cache->set( 'url', $url );
		$tab_cache->set( 'title', htmlspecialchars($title) );
		$tab_cache->set( 'params', htmlspecialchars($params) );		
		$tab_cache->set( 'user', $ari->user );
		$tab_cache->set( 'selected', true );
		if( $tab_cache->store() ){
			admin_session_state::set_active_tab( $tab_cache->id() );
			return $tab_cache->id();
		}
		else
		{
			return 9;
		}
		
	}//end function
	
	//devuelve todas las tab que hay abiertas
	public function get_cache(){
	global $ari;
	
	static::garbage_colector();
	//filtro por usuario
	$filtros = array();
	$filtros[] = array( "field"=>"user", "type"=>"list", "value"=>$ari->user->id());

	$i = 0;
	$return = array();
	
		if( $cache_list = admin_session_state::getFilteredList( false, false, "id", ASC, $filtros ) ){		
			foreach ( $cache_list as $cache ){
				$return[$i]['id'] =	$cache->id();
				$return[$i]['title'] = htmlspecialchars_decode($cache->get('title'));
				$return[$i]['url'] = $cache->get('url');				
				$return[$i]['params'] = htmlspecialchars_decode($cache->get('params'));
				$i++;
			}
		}
		
		return json_encode($return);		
		
	}//end function
	
	
	//guarda una cache de los filtro y devuelve el array de filtro normalizado
	static public function cache_filters( $filters ){
		static::garbage_colector();
		if( $tab_cache = new admin_session_state( $filters['tabid'] ) ){
			$tab_cache->set( 'filters',htmlspecialchars( json_encode( $filters['filters'] ) ) );
		    $tab_cache->store();			
		}
		
		return $filters['filters'];
	
	}//end function
	
	//devuelve la cache de filtros de un tab determinado
	static public function get_cache_filters( $tab_id ){
			if( $tab_cache = new admin_session_state( $tab_id ) ){
				echo htmlspecialchars_decode( $tab_cache->get( 'filters' ) );		    
			}
	}//end function
	
	
	//establece como activa una tab determinada
	static function set_active_tab( $tab_id ){
			global $ari;
			//filtro por usuario
			$filtros = array();
			$filtros[] = array( "field"=>"user", "type"=>"list", "value"=>$ari->user->id() );
				
			if( $cache_list = admin_session_state::getFilteredList( false, false, false, false, $filtros ) ){		
				foreach ( $cache_list as $cache ){							
					$cache->set( 'selected', 0 );
					$cache->store();
				}
			}
			
			if( $tab_cache = new admin_session_state( $tab_id ) ){
					$cache->set( 'selected', 1 );
					$cache->store();			
			}	
	
	}//end function
	
	//limpia datos corruptos
	static public function garbage_colector(){
		
		if( $cache_list = admin_session_state::getFilteredList() ){		
				foreach ( $cache_list as $cache ){							
					if( trim($cache->get('url')) == "" ){
						$cache->delete();					
					}	
				}
	    }
	
		return true;
	}
		
}//end class


?>


