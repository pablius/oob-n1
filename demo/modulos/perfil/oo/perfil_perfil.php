<?php
class perfil_perfil extends OOB_model_type
{

	static protected $public_properties = array(
	
		'nombre' 				=> 'isClean,isCorrectLength-1-255',
		'fecha_nacimiento' 		=> 'object-Date',
		'telefono' 				=> 'isClean,isCorrectLength-0-255',
		'bio'					=> 'object-reservas_agencia',
		'url' 					=> 'isClean,isCorrectLength-0-500',
		'foto_perfil' 			=> 'isClean,isCorrectLength-0-255',
		'id_usuario'			=> 'object-oob_user',
		'array_amigos'			=> 'manyobjects-perfil_amigo'

	); // property => constraints
	
	static protected $table = 'perfil_perfil';
	static protected $class = __CLASS__;	
	static $orders = array('nombre','telefono'); 
	
	// definimos los attr del objeto
	public $nombre;
	public $fecha_nacimiento;
	public $telefono;
	public $bio;
	public $url;
	public $foto_perfil;
	public $id_usuario;
	
	public $array_amigos = array();
	
	static public function existe_usuario(oob_user $user)
	{
		global $ari;
			
		$string = $ari->db->qMagic($user->id());
		
		if (count($result = static::getList(false, false, false, false, false, false, false, "AND id_usuario = $string")) == 1)
		{
			return $result;
		}
		else
		{
			return false;
		}
	}
	
	protected  function isDuplicated()
	{
		
		global $ari;
		$id = $this->id;
		 
		if ($id < ID_UNDEFINED) 					
		{	
			$clausula = "";
		}
		else
		{	
			$clausula = " AND id <> $id ";	
		}
		
		$nombre = $ari->db->qMagic($this->nombre);		
		
		if (static::getListCount(false, false, false, "AND nombre = $nombre $clausula") == 0)
		{
			return false;						
		}
		else
		{
			return true;
		}

	}

	public function delete()
	{
		// borramos el usuario y mandamos el mail
		$this->get('usuario')->delete();
		
		$this->enviar_mail_perfil_borrado();
			
		// borramos este objeto
		return parent::delete();
		
	}
	
	public function enable()
	{
		global $ari;
		$usuario = $this->get('usuario');
		
		if ($usuario->get('status') != 1)
		{
			$usuario->set('status',1);
			$usuario->store();
			
			if ($this->get('status') != 1)
			{	
				$this->set('status',1);
				$this->store();
			}
			
			$this->enviar_mail_perfil_activado();
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function store()
	{
		
		$is_new = true;
		if ($this->id > ID_UNDEFINED)
		{
			$is_new = false;
		}
		
		if(parent::store())
		{
			
			if (!$is_new)
			{
				// para cada seguidor no bloqueado, crear notificacion, el usuario ha actualizado sus datos de perfil.
				if ($amigos = perfil_amigo::get_todos_mis_seguidores())
				{
					foreach ($amigos as $amigo)
					{
						if ($amigo->get('bloqueo_destino') == false)
						{
							$notificacion = new perfil_notificacion();
							$notificacion->set('origen', $this);
							$notificacion->set('destino', $amigo->get('origen'));
							$notificacion->set('fecha', new Date());
							$notificacion->set('tipo', new perfil_notificacion_tipo(2));
							$notificacion->set('enviado', 0);
							$notificacion->store();
						}
					}
				}
				
				
				
			}
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function suspend()
	{
		global $ari;
		$usuario = $this->get('usuario');
		
		if ($usuario->get('status') == 1)
		{
			$usuario->set('status',0);
			$usuario->store();

			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function foto()
	{
		if ($this->foto_perfil == '')
		{
			return "/images/talkmee/foto_usuario.jpg";
		}
		else
		{
			// devolvemos una foto en tamaño estandarizado del perfil.
			return "/archivos/fotos/" . $this->foto_perfil;
		}
	}
	
	public function foto_miniatura()
	{
		if ($this->foto_perfil == '')
		{
			return "/images/talkmee/flanders.jpg";
		}
		else
		{	
			// devolvemos una foto en tamaño miniatura del perfil.
			return "/archivos/fotos/" . $this->foto_perfil;
		}
	}
	
	
	public function name()
	{
		return $this->get('nombre');
	}
	
	private function enviar_mail($titulo,$template)
	{
		global $ari;
		
		$plantilla = $ari->newTemplate();
		$plantilla->caching = 0; 
		
		$from_address = $ari->config->get('email', 'main');
		$from_name = $ari->config->get('name', 'main');
		
		$usuario = $this->get('usuario');

		// datos del operador
		$to_address = $usuario->get('email');
		$to_name = $this->name();
	
		// datos del perfil
		$plantilla->assign('nombre' ,$this->name());
		$plantilla->assign('telefono',$this->get('telefono'));
		
		// datos del usuario
		$plantilla->assign('usuario',$usuario->get('uname'));
		$plantilla->assign('email',$usuario->get('email'));
		
		
		//////////// mail send
		require_once ($ari->get('enginedir').DIRECTORY_SEPARATOR .'librerias'.DIRECTORY_SEPARATOR.'mimemessage'.DIRECTORY_SEPARATOR.'smtp.php');
		require_once ($ari->get('enginedir').DIRECTORY_SEPARATOR .'librerias'.DIRECTORY_SEPARATOR.'mimemessage'.DIRECTORY_SEPARATOR.'email_message.php');
		require_once ($ari->get('enginedir').DIRECTORY_SEPARATOR .'librerias'.DIRECTORY_SEPARATOR.'mimemessage'.DIRECTORY_SEPARATOR.'smtp_message.php');
		$email_message=new smtp_message_class;
		$email_message->localhost="";
		$email_message->smtp_host=$ari->config->get('delivery', 'main');
		$email_message->smtp_direct_delivery=0;
		$email_message->smtp_exclude_address="";
		$email_message->smtp_user="";
		$email_message->smtp_realm="";
		$email_message->smtp_workstation="";
		$email_message->smtp_password="";
		$email_message->smtp_pop3_auth_host="";
		$email_message->smtp_debug=0;
		$email_message->smtp_html_debug=1;
	
		$email_message->SetEncodedEmailHeader("To", $to_address,'"'.$to_name.'" <' . $to_address .'>'); // al perfil
		$email_message->SetEncodedEmailHeader("Cc",$from_address,'"'.$from_name.'" <' . $from_address .'>'); // al sitio
		$email_message->SetEncodedEmailHeader("From", $from_address,'"'.$from_name.'" <' . $from_address .'>'); // del sitio
		$email_message->SetEncodedHeader("Subject", $from_name . ' - ' . $titulo);
		$email_message->AddQuotedPrintableHTMLPart($plantilla->fetch($ari->module->usertpldir(). DIRECTORY_SEPARATOR . $template));
		

		return $email_message->Send();
	}
	
	public function enviar_mail_perfil_nuevo()
	{
		return $this->enviar_mail('Nuevo Usuario Registrado', "mail_perfil_nuevo.tpl");
	}
	
	public function enviar_mail_perfil_borrado()
	{
		return $this->enviar_mail('Perfil Borrado', "mail_perfil_borrado.tpl");
	}
	
	public function enviar_mail_perfil_activado()
	{
		return $this->enviar_mail('Perfil Activado', "mail_perfil_activado.tpl");
	}
	

}
?>