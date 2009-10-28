<?
#OOB/N1 Framework [©2004,2009 - Nutus]
/**
 @license: BSD
 @author: Pablo Micolini (pablo.micolini@nutus.com.ar)
 @version: 1.2

 Provides user validation (login/logout)
*/

//OOB_module :: includeClass ('personnel','personnel_employee');

class OOB_user extends OOB_internalerror {
	// @implementation cuando un usuario ha sido bloqueado, pero habia alguien logueado, q debe hacer el sistema? sacarlo? nada?
	
	private $user= ID_UNDEFINED;
	private $uname = '';
	private $password = '';
	private $email = '';
	private $session = '';
	private $status= 8; // 0 neeeds auth, 1 ok, 2 temp block, 9 deleted
	private $maxcon= 5;
	private $block_method= 1; // 0 no block, 1 time, 2 statuschange 
	private $block_time= "3600"; //10 minutos
	private $source= "db"; // db, imap
	private $new_validation= "no"; // 0 no, 1 mail, 3 admin
	private $employee = NO_OBJECT;
	
	public function id()
	{
		return $this->get('user');
	}
	
	public function name()
	{
		return $this->uname;
	}
	
	/** loads config details, and starts the user. 
 	    if no user set we must believe is a new one */
	public function __construct($user= ID_UNDEFINED) {
		global $ari;
		//load config vars!
		$this->block_method= $ari->config->get('block-method', 'user');
		$this->block_time= $ari->config->get('block-time', 'user');
		$this->source= $ari->config->get('validation-method', 'user');
		$this->new_validation= $ari->config->get('new_validation', 'user');
		if ($user > ID_MINIMAL) {
			$this->user= $user;
			
			if (!$this->fill ())
					{throw new OOB_exception("Invalir user {$user}", "403", "Invalid User", false);}
					
		}  
	}
		
	/** user validation made, username and password must be provided */
	public static function login($uname, $pass) {
		global $ari;
		
	
		
		// clean vars
		if (OOB_validatetext :: isClean($uname) && OOB_validatetext :: isClean($pass) && OOB_validatetext :: isPassword($pass)) {
			
			$imap_pass = $pass;
			$pass= md5($pass); 
		
		} else
			return false;
			
			// load config vars
			$source= $ari->config->get('validation-method', 'user');
			$blocktime= $ari->config->get('block-time', 'user');
			$savem = $ari->db->SetFetchMode(ADODB_FETCH_NUM);
			$uname = $ari->db->qMagic ($uname);
			
			// get id where user and pass matches (using proper auth method!)
		
			$sql= "SELECT id, password, status FROM oob_user_user WHERE uname = $uname";
			$ari->db->SetFetchMode($savem);
			$rs = $ari->db->Execute($sql);
			
			if (!$rs || $rs->EOF) {
				return false;
			
			} else {
				$userid= $rs->fields[0];
				$password= $rs->fields[1];
				$status= $rs->fields[2];
				$rs->Close();
				
			}
			switch ($source)
			{
				//lo de siempre es una db	
				case "db":
				default:
				 {
							
					if ($password != $pass) 
						{
						// add failed for uid! if pass not match!
						self::addFailed ($userid, "WRONGPASS");
					
						//contamos cuantos fallados tiene, y 
						// si son mas de los q deberia, lo sentimos mucho
						$fallados = self::getFailed ($userid, $blocktime); 
					
						if ($fallados > 10)
							{throw new OOB_exception("BLOCKED User ($uname)", "403", "Su usuario ha sido desactivado, contacte al administrador", true);}
					
						return false;				
				
				
						}
					break;
					}
					
//				case "imap": {
//					
//					$server = $ari->get("config")->get('imap-server', 'user');
//				
//					//if ok, continue, else add failed as pass not match
//					if ($mbox = @imap_open("{" . $server . "}INBOX", "$uname", "$imap_pass", OP_READONLY))
//					{
//						@imap_close ($mbox);
//						
//						 } else {
//						$now = $ari->db->DBTimeStamp(time());
//						$sql = "insert into oob_user_failed (id, user_id, timestamp , reason )
//								values ('','$userid',$now,'IMAP_INVALID')";
//						if ($ari->db->Execute($sql) === false)
//						throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false);
//					
//						return false;
//					
//					}
//				
//				}
			}
			


			if ($status != 1) {
			throw new OOB_exception("DISABLED", "403", "Su usuario no está activo, contacte al administrador", false);
			}
			
//valida el permiso de acceso al modulo admin		
			if ($ari->mode == 'admin' ) 
			{
				//verificar permiso
						if ( !seguridad :: isAllowed(seguridad_action :: nameConstructor('adminaccess','access','admin'), '',  new oob_user ($userid)) )
				{
					throw new OOB_exception("Acceso denegado a Usuario {$uname}", "403", "Acceso denegado. Consulte con su administrador", true);
						
				}					
			}		
			
			
			$sessionkey = session_id();
			
			// Con esto grabamos los datos de sesion, para poder loguear al usuario sin problemas 
			session_write_close(); 
			
			
			//stores the login->session key relation (if there is one previously, updates the record.)
			$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
			$sql= "SELECT user_id FROM oob_user_online WHERE sesskey  = '$sessionkey'";
			$ari->db->SetFetchMode($savem);
			$rs = $ari->db->Execute($sql);

			if (!$rs || $rs->EOF) {
				
					$sql = "INSERT INTO oob_user_online (user_id,sesskey )
							values ('$userid','$sessionkey')";

					if ($ari->db->Execute($sql) === false)
					
					throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos, o en la asignación de Sesion, recargue la página", false);

				
								
					return  new oob_user ($userid);
			
			} else {
				$useridlogued= $rs->fields[0];
				$rs->Close();
				
							}
						
					if ($useridlogued == $userid)
						return   new oob_user ($userid);
					else {
						$sql= "UPDATE oob_user_online SET  user_id = '$userid' WHERE sesskey  = '$sessionkey'";

						if ($ari->db->Execute($sql) === false)
							throw new OOB_exception("Error en DB: ". $ari->db->ErrorMsg(), "010", "Error en la Base de Datos", false);
					
						return  new oob_user ($userid);
					
						}					


	}	
	
	/** deletes the sessionid->login link.
	 * You can provide a redirection url, default is home  */
	public function logout($redirect ='/') {

				global $ari;
			$sessionkey = session_id();
			$sql= "DELETE FROM oob_user_online WHERE sesskey  = '$sessionkey' AND  user_id = '$this->user'";
			$ari->db->Execute($sql);
			
			session_destroy();
			
			if($redirect !== false)
			{	if ($ari->mode == 'admin')
				{
					header( "Location: " . $ari->adminaddress . $redirect);
					exit;	
				
				}
				else
				{
					header( "Location: " . $ari->webaddress . $redirect);
					exit;	
				}	
			}
	}

	/** 
 	look for session id->login link, 
 	if exist return user-object, else false.  */
	 public static function isLogued() {
		
		global $ari;
		
			$sessionkey = session_id();
			$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
			$sessionkey = $ari->db->qMagic ($sessionkey);
			$sql= "SELECT user_id FROM oob_user_online WHERE sesskey  = $sessionkey";
			$rs = $ari->db->Execute($sql); 
			
			$ari->db->SetFetchMode($savem);
					
				if (!$rs || $rs->EOF) {
				return false;
			}
			
			if (!$rs->EOF && $rs->RecordCount() == 1) {
			
			$userid = $rs->fields[0];
				$rs->Close();
				return new oob_user ($userid);

			}
		//	return false; // por las dudas?
			throw new OOB_exception("Mas de un Usuario para la misma Sesión", "501", "Ha ocurrido un error inesperado", true);
		
		
	}
	
	public static function lostPass($email) 
	{ 	
		global $ari;
		
		if ( !OOB_validatetext :: isEmail($email)  )
			return false;
		
		// valida que exista el usuario o mail
		$id = self::uniqueUser(null,$email, true);
		
		if ($id !== true)
		{
			// genera un codigo unico y lo almacena
			$return = array();
			$return[0] = new OOB_user ($id);
			$id =  $ari->db->qMagic($id);
			$string = md5(time() . $email . 'string aleatorio del programador');
			$return[1] =$string;
			$string = $ari->db->qMagic($string);
			
			$sql= "INSERT INTO oob_user_forgot 
						   ( userID, code)
						   VALUES ( $id, $string )
						   	";
			$ari->db->Execute($sql);
			return $return;
		} 
		else
		{
			return false;
		}
		
	}
	
	/** Verifica que el codigo exista, valida con el usuario y la fecha, 
	 * y si todo va bien, graba la clave nueva.
	 * Le tiene que pasar:
	 * code = md5 - 32 varchar
	 * email = el email registrado
	 * newpass = la clave nueva
	 */
	 public static function validateLost($code, $email, $newpass)
	{
		global $ari;
		// verifica que exista el codigo almacenado
			
			if ( !OOB_validatetext :: isEmail($email)  )
			return false;
			
			if ( !OOB_validatetext :: isClean($code) || !OOB_validatetext :: isCorrectLength($code,32,32))
			return false;
			
			$ari->db->StartTrans();
			$code = $ari->db->qMagic($code);
			
			$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
			$sql= "SELECT userID,date FROM oob_user_forgot WHERE code  = $code";
			$rs = $ari->db->SelectLimit($sql,1); 
			
			$ari->db->SetFetchMode($savem);
					
				if (!$rs || $rs->EOF) {
				$ari->db->CompleteTrans();
				return false;
			} 
			else 
			{
			$usuario =  new OOB_user ($rs->fields[0]);
			$date = $rs->fields[1];
			$rs->Close();
			}
			
			/////////////////////
			$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
			$uid = $ari->db->qMagic($usuario->id());
			$sql= "SELECT code FROM oob_user_forgot WHERE userID = $uid  ORDER BY date DESC";
			$rs = $ari->db->SelectLimit($sql,1); 
			
			$ari->db->SetFetchMode($savem);
					
			if (!$rs || $rs->EOF) {
				$ari->db->CompleteTrans();
				return false;
			} 
			else 
			{
			$lastcode = $rs->fields[0];
			$rs->Close();
			}
	
			
			if ($ari->db->qMagic($lastcode) != $code)
			{
				$ari->db->CompleteTrans(); 
				return false;
			}
			
			/////////////////////////
			
			
			
		// valida el email y el vencimiento
		if ($usuario->get('email') != $email)
		{
			$ari->db->CompleteTrans();
			return false;
		}
			
		$ahora = new Date();
		$sudate = new Date ($date);
		
		//@todo VERIFICAR!!! 
		$ahora->getTime();
		$sudate->getTime(); 
		$elminimo = $ahora->getTime() - 86400;
		
//		var_dump($ahora->getTime());
//		var_dump($sudate->getTime());
//		exit;
		
		if ($sudate->getTime() <= $elminimo)
		{$ari->db->CompleteTrans();
		return false;
		}
		else
		{
			// actualiza la clave
		$usuario->set('password', $newpass);
		if ($usuario->store())
			{
				
			// borra los codigos de la db
				
				$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
				$sql= "DELETE FROM `oob_user_forgot` WHERE `userID` = $uid";
				$ari->db->Execute($sql); 
				
				if ($ari->db->CompleteTrans())
				return true;
				else
				return false;
				
				
			}
			else
			{
				return false;
			}
		}

	} // end function
	
	 /** valida que no haya otro usuario con el mismo user o email 
	 *  devuelve TRUE si NO existe el user.
	 * De existir devuelve el ID */
	public static function uniqueUser($user,$email, $both = false)
	{
		global $ari;
		
		$clausula = "OR";
		if ($both == true)
		{	$clausula = "AND";
		}
		
		if ($user == null || $email == null)
		{	$clausula = "";
		}
		
		if (!OOB_validatetext :: isEmail($email)  )
		{	return false;
		}
			
		if (!OOB_validatetext :: isClean($user))
		{	return false;	
		}
			
		if ($user != null)
		{	$uname = $ari->db->qMagic($user);
			$clausulaUser = "uname = $uname";
		}
		else
		{	$clausulaUser = "";
		}
		
		if ($email != null)
		{	$email = $ari->db->qMagic($email);
			$clausulaEmail = "email = $email";
		} 
		else
		{	$clausulaEmail = "";
		}		 
		
		$sql= "SELECT id FROM oob_user_user WHERE  $clausulaUser $clausula $clausulaEmail" ;
			
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);
		
//		var_dump ($rs->EOF);
//		var_dump($rs->fields[0]);
		if (!$rs->EOF && $rs->fields[0]!= 0) 
		{
			$rs->Close();
			return $rs->fields[0];
		} 
		else
		{
			return true;
		}
			

	}
	
	
	public function exist_user($user)
	{
	
		global $ari;
	
		if ($user != null)
		{	$uname = $ari->db->qMagic($user);
			$clausulaUser = "uname = $uname";
		}
		else
		{	$clausulaUser = "";
		}	
		 
		
		$sql= "SELECT id FROM oob_user_user WHERE  $clausulaUser " ;
			
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);

		if (!$rs->EOF && $rs->fields[0]!= 0) 
		{
			$rs->Close();
			return true;
		} 
		else
		{
			return false;
		}
		
	}//end function 

	 /** Valida que no haya otro usuario con el mismo nombre de usuario o email 
	  *  Devuelve TRUE si NO existe el user. De existir devuelve false
	  *  Tiene en cuenta si en nuevo usuario o existente
	  */
	public function isUnique()
	{
	
		global $ari;
		//validar variables
		if (!OOB_validatetext :: isClean($this->uname) || 
			!OOB_validatetext :: isCorrectLength ($this->uname, 4, 64) ||
			!OOB_validatetext :: isEmail($this->email) )
		{		
			return false;			
		} 
		
		if ($this->user <> ID_UNDEFINED)
		{//usr existente: verificar contra su mismo id	
			$id = $ari->db->qMagic($this->user);
			$clausula = " AND id <> $id ";
		}
		else
		{//usr nuevo: no importa el id
			$clausula = "";
		}
		
		$savem = $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		
		$uname = $ari->db->qMagic($this->uname);	
		$email = $ari->db->qMagic($this->email);
		
		$sql = "SELECT true as cuenta,status,id

				FROM oob_user_user 
				WHERE ( uname = $uname OR email = $email )
				$clausula
			   "; 
		
				

			$rs = $ari->db->Execute($sql);
			$ari->db->SetFetchMode($savem);
			
			if( !$rs->EOF && $rs->fields[0]!= 0 )
			{			
					//duplicado
					if(  $rs->fields[1] == 9 ){												
						$this->user = $rs->fields[2];
						$this->status = 1 ;


						return false;
					}
					else
					{
						return true;
					}	
			} 
			else
			{
					return true;	
			}//end if
		

	}//end function
	
	
	//devuelve un numero aleatorio de n caracteres
	public function randon( $longitud ){
		
		$exp_reg = "[^0-9]";    
		
		return substr(eregi_replace($exp_reg, "", md5(rand())) .
       eregi_replace($exp_reg, "", md5(rand())) .
       eregi_replace($exp_reg, "", md5(rand())),
       0, $longitud);
		
	}//end function

	/* genera un usuario con formato cuit, ademas verifica que no exista */
	public function get_rand_user()
	{
	
		while(true)
		{
			$str = $this->randon(2) . '-' . $this->randon(8) . '-' . $this->randon(1) ;	
			if( !$this->exist_user($str) )
			{
				break;
			}

		}
        
		
		return $str;
	
	}//end function
	
	//devuelve el contacto que esta asiganado al usuario
	public function get_contact(){
	
		$result = false;
	
		if( $contacto = contactos_contacto::getRelated($this) ){
			$result = $contacto[0];
		}
	
		return $result;
		
	}//end function
	
	/** Returs the value for the given var */ 	
 	public function get ($var)
 	{
 		return $this-> $var;
 	}

	/** Sets the variable (var), with the value (value) */ 	
 	public function set ($var, $value)
 	{
		$this->$var= $value;
 	} 	

	/** Stores/Updates user object in the DB */	
	/** Stores/Updates user object in the DB */	
	public function store() {
		global $ari;
		// clean vars !
		
			
		
		if (!OOB_validatetext :: isEmail($this->email))
		{   $this->error()->addError ("INVALID_EMAIL");					
		}
		
		if (!OOB_validatetext :: isClean($this->uname) || !OOB_validatetext :: isCorrectLength ($this->uname, 4, 64))
		{	$this->error()->addError ("INVALID_USER");			
		} 
			

		 	//validar que el nombre de usuario y password no se repitan para otro usuario
			if (OOB_validatetext :: isClean($this->uname) && 
				OOB_validatetext :: isCorrectLength ($this->uname, 4, 64) &&
				OOB_validatetext :: isEmail($this->email) )
			{
				if (!$this->isUnique())
				{$this->error()->addError ("INVALID_USER");					
				}
			} 
			 
			
			  if ($this->password != -1)
			  {
				  if (!OOB_validatetext :: isClean($this->password) ||!OOB_validatetext :: isPassword($this->password) ){
				 $this->error()->addError ( "INVALID_PASS"); 
				 }
			  }
			   
			  if ( 	!OOB_validatetext :: isNumeric($this->status) ){
			  	$this->error()->addError ("INVALID_STATUS", true);
			  }
				
			//empleado
			if (is_a ($this->employee, "personnel_employee") )
			{	$employee_id = $ari->db->qMagic($this->employee->get('id'));	
			}
			else
			{	$employee_id = $ari->db->qMagic(0);
			}
			
			$errorlist= $this->error()->getErrors();
		
			if (count ($errorlist) > 0)
			 { return false; //devuelve un objeto de error con los errores!
			 }
			 else
			{
			 	
			 	$uname =$ari->db->qMagic($this->uname);
			 	$email =$ari->db->qMagic($this->email);
		 		$status =$ari->db->qMagic($this->status);
			 	
			 	if ($this->password == -1)
			 		$passql = "";
			 	else
			 	{
			 	//	$this->password =$ari->db->qMagic( $this->password);
			 		$password = md5 ($this->password);
			 		$passql = "password = '$password',";
			 	
			 	}
				
				if ($this->user > 0)
				{
					// update data
					$ari->db->StartTrans();
					$sql= "UPDATE oob_user_user 
						   SET uname = $uname , 
						   $passql 
						   email = $email, 
						   status = $status, 
						   EmployeeID = $employee_id  
						   WHERE id = '$this->user'";
					$ari->db->Execute($sql);
					
					if (!$ari->db->CompleteTrans())
						throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); //return false;
					else
					{
						$this->password = -1;
						return true;
					}
					
				} else 
				{
					// insert new and set userid with new id
					$password =$ari->db->qMagic($password);
					$ari->db->StartTrans();
										
					$sql= "INSERT INTO oob_user_user 
						   ( uname, password, email, connections, status, EmployeeID)
						   VALUES ( $uname, $password, $email ,'1',$status, $employee_id)
						   	";
					$ari->db->Execute($sql);
					$this->user = $ari->db->Insert_ID();
				
					if (!$ari->db->CompleteTrans())
						throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); //return false;
					else
					{
						$this->password = -1;
						return true;
					}
				}
		
			}

	}//end function


	/** Deletes user object from the DB*/
	public function delete() {
		global $ari;
		// sets status 9 for a user-id
		if ($this->user > 0 && $this->status != 9) {

			$sql= "UPDATE oob_user_user SET  status = '9' WHERE id = '$this->user'";
			if ($ari->db->Execute($sql))
				return true;
			else
				throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false);
					

		} else {
			if ($this->status == 9)
			$this->error()->addError ("ALREADY_DELETED");
			else
					 $this->error()->addError ( "NO_SUCH_USER");
			
			return false;
		}
	}
	
	/** Returs the users on the system. status = (enabled/deleted/pending/bloqued/all) shows users */
	static public function userList ($status = 'enabled', $sort = 'uname', $pos = 0, $limit = 20)
	{
	global $ari;
	


	if (in_array ($status, oob_user::getStatus ("ALL", false)) && $status != "all")
	{
	$estado = "WHERE status = " . oob_user::getStatus($status, false);
	}
	else
	{
		if ($status == "all")
			{$estado = "";}
		else
			{$estado = "WHERE status = 1";}
	}

	if (in_array ($sort, oob_user::getOrders()))
		$sortby = "ORDER BY $sort";
	else
		$sortby = "ORDER BY uname";

			$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
			$sql= "SELECT id FROM oob_user_user  $estado $sortby";
			$rs = $ari->db->SelectLimit($sql, $limit, $pos);

			$ari->db->SetFetchMode($savem);
				if ($rs && !$rs->EOF) { // aca cambie sin probar, hay q ver si anda!
					while (!$rs->EOF) {
					$return[] = new oob_user ($rs->fields[0]);
					$rs->MoveNext();
					}			
			$rs->Close();
			} else
			{return false;}

		return $return;
	}

	/** Returs the users on the system. status = (enabled/deleted/pending/bloqued/all) shows users */
	static public function userCount ($status = 'enabled', $sort = 'uname')
	{
	global $ari;
	


	if (in_array ($status, oob_user::getStatus ("ALL", false)) && $status != "all")
	{
	$estado = "WHERE status = " . oob_user::getStatus($status, false);
	}
	else
	{
		if ($status == "all")
			{$estado = "";}
		else
			{$estado = "WHERE status = 1";}
	}

	if (in_array ($sort, oob_user::getOrders()))
		$sortby = "ORDER BY $sort";
	else
		$sortby = "ORDER BY uname";

			$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
			$sql= "SELECT id FROM oob_user_user  $estado $sortby";
			$rs = $ari->db->Execute($sql);

			$ari->db->SetFetchMode($savem);
				if ($rs && !$rs->EOF) { // aca cambie sin probar, hay q ver si anda!
					$return = $rs->RecordCount();	
			$rs->Close();
			} else
			{return false;}

		return $return;
	}

	/** Returs the users on the system. status = (enabled/deleted/pending/bloqued/all) shows users 
		search by $text in id, uname and email fields
	*/
	static public function search ($status = 'enabled', $sort = 'uname', $text = "", $pos = 0, $limit = 20)
	{
		global $ari;
		//status
		if (in_array ($status, oob_user::getStatus ("ALL", false)) && $status != "all")
		{	$estado = "AND status = " . oob_user::getStatus($status, false);
		}
		else
		{	if ($status == "all")
			{	$estado = "";
			}
			else
			{	$estado = "AND status = 1";
			}
		}
		//search text
		$searchText = "";
		if($text <> "")
		{	if(!OOB_validatetext::isClean($text) )
			{	return false;
			}
			else
			{	
				//saco esto para que anden los filtros y el buscador del listado de usuarios!
				//$text = trim($text);
				//$textID = $ari->db->qMagic($text);
				//$text = $ari->db->qMagic("%" . $text . "%");
				$searchText = "  $text ";
				//$searchText = " AND ( id = $textID OR uname LIKE $text OR email LIKE $text) ";
			}
		}
		//sort by	
		if (in_array ($sort, oob_user::getOrders()))
		{	$sortby = "ORDER BY $sort";
		}
		else
		{	$sortby = "ORDER BY uname";
		}
		
		$savem = $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$sql = "SELECT id FROM oob_user_user WHERE 1=1 $estado $searchText $sortby ";
		

		$rs = $ari->db->SelectLimit($sql, $limit, $pos);

		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{
			while (!$rs->EOF) 
			{	//@optimize => patron factory
				$return[] = new oob_user ($rs->fields[0]);
				$rs->MoveNext();
			}			
			$rs->Close();
		} 
		else
		{	return false;
		}

		return $return;
	}

	/** Count the users on the system. status = (enabled/deleted/pending/bloqued/all) shows users 
		by $text in id, uname and email fields
	*/
	static public function searchCount ($status = 'enabled', $text = "")
	{
		global $ari;
		//status
		if (in_array ($status, oob_user::getStatus ("ALL", false)) && $status != "all")
		{	$estado = "AND status = " . oob_user::getStatus($status, false);
		}
		else
		{
			if ($status == "all")
			{	$estado = "";
			}
			else
			{	$estado = "AND status = 1";
			}
		}
		//search text
		$searchText = "";
		if($text <> "")
		{	if(!OOB_validatetext::isClean($text) )
			{	return false;
			}
			else
			{	
				//saco esto para que anden los filtros y el buscador del listado de usuarios!
				//$text = trim($text);
				//$textID = $ari->db->qMagic($text);
				//$text = $ari->db->qMagic("%" . $text . "%");
				$searchText = "  $text ";
				//$searchText = " AND ( id = $textID OR uname LIKE $text OR email LIKE $text) ";
			}
		}
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$sql = "SELECT COUNT(*) 
				FROM oob_user_user 
				WHERE 1=1 
				$estado $searchText
				";
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{	$return = $rs->fields[0];	
			$rs->Close();
		} 
		else
		{	return 0;
		}

		return $return;
	}

	/** Shows the available sorting ways for users */
	static public function getOrders()
	{
	$return[] = "uname";
	$return[] = "id";
	$return[] = "email";
	$return[] = "status";
	
	return $return;
	}

	/** Shows the available status for users, or all */	
	static public function getMethods()
	{

	$return[] = "enabled";
	$return[] = "deleted";
	$return[] = "pending";
	$return[] = "bloqued";
	$return[] = "all";
	
	return $return;
	}
	
	/** Shows the user status or all available status 
	 * $one = ID or "status_string" returns the user status; or "ALL" to return an array.
	 * $id => if true, returns a number, else a string.
	 * */
		static public function getStatus ( $one = "ALL" ,$id = true) 
	{ // 0 neeeds auth, 1 ok, 2 temp block, 9 deleted

	$return[1] = "enabled";
	$return[2] = "bloqued";
	$return[0] = "pending";
	$return[9] = "deleted";
	
	if ($id != true)
	$return = array_flip ($return);
	
	if ($one !== "ALL")
	
	{
		if ($return[$one] !== "" )
		{$return = $return[$one];}
		else
		{$return =  false;}

	}
	
	
	return $return;
	}

	/** Updates a given array of users (id), with the status provided. */
	static public function updateStatusFor ($data, $status = false)
	{
	global $ari;
	
	if (!in_array($status, oob_user::getMethods()))
	{throw new OOB_exception("User gave a POST value that is unexistant", "403", "Información inválida desde el Usuario", true);}
		$ari->db->StartTrans();
		foreach ($data as $usuario)
		{
		$new = new oob_user ($usuario['id']);
			
		if (OOB_validatetext::isNumeric($status))
			{$new->set ('status', $status);}
		
		else
			{$new->set ('status', oob_user::getStatus($status, false));}
		
		if (!$new->store())
			{throw new OOB_exception("System Error - STATUS:{$status} is invalid", "501", "System Error", true);}
		}
		$ari->db->CompleteTrans();

	}
	
	/** Fills the user with the DB data */
	private function fill ()
	{
	global $ari;

			//load info
			$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
			$sql= "SELECT Uname, Password, Email, Connections, Status, EmployeeID 
				   FROM OOB_User_User 
				   WHERE id = '$this->user'";
			$rs = $ari->db->Execute($sql);
			
			$ari->db->SetFetchMode($savem);
			if (!$rs || $rs->EOF) 
			{
				return false;
			}
			if (!$rs->EOF) 
			{
				$this->uname= $rs->fields["Uname"];
				$this->password= -1;
				$this->email= $rs->fields["Email"];
				$this->maxcon= $rs->fields["Connections"];
				$this->status= $rs->fields["Status"];
				if (!empty($rs->fields['EmployeeID']) && OOB_validatetext :: isNumeric($rs->fields['EmployeeID']) && $rs->fields['EmployeeID'] > ID_MINIMAL)
				{	$this->employee = new personnel_employee($rs->fields['EmployeeID']);	}
			}
			$rs->Close();
			return true;
	
	}

	/** gets the amout of failed login attemps for a given user-id, with a block time */
	private function getFailed ($userid, $blocktime){
	global $ari;
	
					$userid = $ari->db->qMagic ($userid);
					$savem = $ari->db->SetFetchMode(ADODB_FETCH_NUM);
					$block = $ari->db->DBTimeStamp(time() - $blocktime );
					$sql= "SELECT count(id) as cuenta  FROM oob_user_failed WHERE timestamp > $block AND user_id = $userid";
					$ari->db->SetFetchMode($savem);
					$rs = $ari->db->Execute($sql);
					$cuenta = 0;
					if (!$rs->EOF) {
						$cuenta = $rs->fields[0];
						$rs->Close();
					}
	
	return $cuenta;
	}
	
	/** logs a failed attempted login to the Db for a given user */
	private function addFailed ($userid, $message = "UNDEFINED")
	{
		global $ari;
		
						$now = $ari->db->DBTimeStamp(time());
						$msg = $ari->db->qMagic ($message);
						$sql = "INSERT INTO oob_user_failed ( user_id, timestamp , reason )
								values ('$userid',$now,$msg)";
						if ($ari->db->Execute($sql) === false)
							throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false);
						
	}
	 

  	/**	Devuelve true si ya existe el nombre de usuario pasado como parametro */
	static public function exists ($uName, $clausula = '')
	{
		global $ari;
		
		$uName = $ari->db->qMagic(trim($uName));
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		
		$sql= "SELECT True as Existe 
			   FROM OOB_User_User
			   WHERE Uname = $uName $clausula";
		
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);

		if (!$rs->EOF && $rs->fields['Existe']) 
		{	$return = true;		}
		else
		{	$return = false;	}
		
		$rs->Close();
		return $return;
	}
	
	/**
     * Esta funcion valida q los empleados q vienen en el string existan
     * el formato de la cadena es la siguiente
     * LastNameEmployee1, FirstNameEmployee1 EMPLOYEE_SEPARATOR LastNameEmployee2, FirstNameEmployee2......
     * La funcion devuelve un array con los usuarios correspondientes a los empleados q se encontraron, 
     * en caso de q un empleado no exista su lugar en el array lo ocupara un valor booleano false
	 */
	static public function getUsersByString( $string )
	{
	  	$users = array();  	 
	  	$string = trim($string);
	  	$i = 0;
	  	 	
	  	if ($string == "")
	  	{	return false;	}
	  	 
	  	//creo un array con los nombres y apellidos de los empleados
	  	$array_employee = explode(EMPLOYEE_SEPARATOR,$string);
	  	
	  	if (! is_array($array_employee) )
	  	{
	  		$array_employee = array();	
	  		$array_employee[0] = $string;	
	  	}//end if

	  	//recorro el array y separo los nombres y los apellidos
	  	foreach($array_employee as $e)
		{	
			$e = trim($e);
			if ($e != "")
			{
				//busco la posicion del separador de nombres
		  	 	$pos_coma = strpos ($e, NAME_SEPARATOR);
		  	 	
		  	 	if ($pos_coma)
				{ //encontro la posicion
		 			$lastName = trim(substr($e,0,$pos_coma));
					$firstName = trim(str_replace( NAME_SEPARATOR, "", substr($e,$pos_coma+1,strlen($e)-1)));
				}
		  	 	else
				{
					//ver q pasa si no tiene coma
		 			$lastName = $e;
		 			$firstName = '';
				}//end if 
				$users[$i]['employeename'] = $lastName ." ". $firstName;
				$users[$i]['object'] = OOB_User :: getUserByEmployeeName($lastName, $firstName);
				$i++;
			}//end if		
		}//end foreach 				
		
		return $users;
	}//end fucntion	
	
	
	/** Retorna el usuario cuyo empleado tiene el nombre y apellido 
	 *  pasado por parametro 
	 */ 	
 	static public function getUserByEmployeeName ($lastName = '',$firstName = '' )
 	{
		global $ari;
		
		if ( $lastName == '' && $firstName == '' )
		{	return false;	}
		
		//load info
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		
		$firstName = trim($ari->db->qMagic($firstName));
		$lastName = trim($ari->db->qMagic($lastName));
		
	 	$sql = "SELECT `OOB_User_User`.`ID` AS `UserID`, `OOB_User_User`.`Uname`,
					   `OOB_User_User`.`Password`, `OOB_User_User`.`Email`,
					   `OOB_User_User`.`Connections`, `OOB_User_User`.`Status` AS `UserStatus`,
				       `OOB_User_User`.`EmployeeID`, `Personnel_Employee`.`LastName`, 
			 		   `Personnel_Employee`.`FirstName`, `Personnel_Employee`.`BirthDate`, 
					   `Personnel_Employee`.`EmployeeNumber`, `Personnel_Employee`.`Description`, 
					   `Personnel_Employee`.`Status` AS `EmployeeStatus`
				FROM `Personnel_Employee`, `OOB_User_User`	 		
				WHERE `OOB_User_User`.`EmployeeID` = `Personnel_Employee`.`ID`
			    AND `Personnel_Employee`.`LastName` = $lastName
			    AND `Personnel_Employee`.`FirstName` = $firstName
			    AND `Personnel_Employee`.`Status` = '" . USED . "'";
	
		$rs = $ari->db->SelectLimit($sql,1,0);
		$ari->db->SetFetchMode($savem);
		if (!$rs || $rs->EOF) 
		{	$return = false;	}
		else 
		{	
			$return = new oob_user (ID_UNDEFINED);
			$return->user = $rs->fields["UserID"];
			$return->uname = $rs->fields["Uname"];
			$return->password = $rs->fields["Password"];
			$return->email = $rs->fields["Email"];
			$return->maxcon = $rs->fields["Connections"];
			$return->status = $rs->fields["UserStatus"];				
				
			$employee = new personnel_employee(ID_UNDEFINED);
			$employee->set("id",$rs->fields["EmployeeID"]);
			$employee->set("lastName", $rs->fields["LastName"]);
			$employee->set("firstName",$rs->fields["FirstName"]);			
			$employee->set("birthDate", new Date($rs->fields['BirthDate']) );
			$employee->set("employeeNumber", $rs->fields["EmployeeNumber"]);	
			$employee->set("description", $rs->fields["Description"]);
			$employee->set("status",$rs->fields["EmployeeStatus"]);
				
			$return->employee = $employee;

		}//end if
		$rs->Close();
		return $return;
 	}//end function			    
		
/** Linkea con el grupo standard para usuarios que se generan solo,'
 * y hace falta q pertenezcan a algun lado para darles permisos, vio?
 */
	public function linkStandardGroup ()
{
	global $ari;
	 

	$id_grupo = $ari->config->get('new-group', 'user');
	$grupo = new seguridad_group ($id_grupo);

	
	if ($grupo->addUser ($this ))
	{		return true;}
	else
	{		return false;}

				
}

	
  	/**
  	 *  Devuelve todas las direcciones de correo eleectronicas
  	 *  pertenecientes al usuario pasado como parametro si esta seteado
  	 *  la bandera $all, en caso contrario devuelve solo las direcciones
  	 *  de correo del usuario q no esten asignadas en cuentas de correo
  	 */ 	 
  	public static function getEmailsFor($user, $all = true, $status = USED, $operator = OPERATOR_EQUAL )
  	{
  		global $ari;
  		
  		//valido q se haya pasado un usuario
  		if (! is_a($user, "OOB_user") )		
  		{
  			$ari->error->addError ("oob_user", "INVALID_USER");
  			return false;
  		}//end if

		if (strtolower($status) == "all")
		{	$estado = "";	}
		else
		{	$estado = " AND `Mail_Account`.`Status` $operator '". $status. "'";	}
  		
  		$userID = $ari->db->qMagic($user->user);
  		
  		$sql = "SELECT `OOB_User_User`.`Email`
  			     FROM `OOB_User_User`  
  			     WHERE `OOB_User_User`.`ID` = $userID
  			   ";
  		
  		if (!$all)
  		{
  			$sql.= " AND `OOB_User_User`.`Email` NOT IN
  					 (SELECT `Mail_Account`.`UserName`
  					  FROM `Mail_Account`
  					  WHERE `Mail_Account`.`OwnerID` = $userID	 
  					 )
  				   ";
  		}//end if
  		
  		//saco los emails asignado al empleado
  		$sql2 = "";
  		if ( is_a($user->employee, "personnel_employee") )
  		{
  			$employeeID = $user->employee->get("id");
  			//
  			$sql2 = "SELECT `Address_Online`.`Address` AS `Email`
  			         FROM `Address_Online`, `Personnel_EmployeeOnline`
  			         WHERE `Personnel_EmployeeOnline`.`OnlineID` = `Address_Online`.`ID`
  			         AND `Personnel_EmployeeOnline`.`EmployeeID` = $employeeID
  			         AND `Address_Online`.`OnlineTypeID` = '". ONLINE_EMAIL . "'
  					";
	  		if (!$all)
	  		{
	  			$sql2.= " AND `Address_Online`.`Address` NOT IN
	  					  (SELECT `Mail_Account`.`UserName`
	  					  FROM `Mail_Account`
	  					  WHERE `Mail_Account`.`OwnerID` = $userID	 
	  					 )
	  				    ";
	  		}//end if  			
  			
  		}//end if
  		
  		if ($sql2 != "")
  		{	$sql = "($sql) UNION ($sql2)";	}
  		
  		$sql .= " ORDER BY `Email` ";
  		
  		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
  		
  		$rs = $ari->db->Execute($sql);

		$ari->db->SetFetchMode($savem);
	
		if ($rs && !$rs->EOF) 
		{ 
			$i = 0;
			$return = array();
			$type = new address_onlinetype(ONLINE_EMAIL);
			while (!$rs->EOF) 
			{
				$return[$i] = new address_online (ID_UNDEFINED);
				$return[$i]->set('address', $rs->fields["Email"]);
				$return[$i]->set('type', $type );
				$rs->MoveNext();
				$i++;				
			}//end while
		}
		else
		{	$return = false;	}//end if
		$rs->Close();

		return $return;
  		
  	}//end function
  	
  	/** Retorna los usuarios cuyo valor de $field se encuentra entre los valores 
	 *  pasados por parametro 
	 */ 	
 	static public function getUsersIn ($field = '', $values = '' )
 	{
		global $ari;
		
		if ( $field == '' || $values == '' )
		{	return false;	}
		
		//si $values no es array lo transformo en uno
		if ( ! is_array($values) )
		{	$values = array($values);	}
		
		//armo la clasula in
		$in = "";
		foreach($values as $v)
		{
			if (OOB_validatetext :: isNumeric($v) )
			{
				if ($in == "")
				{	$in = $ari->db->qMagic($v);	}
				else
				{	$in .= "," . $ari->db->qMagic($v);	}//end if
			}//end if
		}//end foreach
		
		if ($in == "")
		{	return false;	}
		
		//load info
	 	$sql = "SELECT `OOB_User_User`.`ID` AS `UserID`, `OOB_User_User`.`Uname`,
					   `OOB_User_User`.`Password`, `OOB_User_User`.`Email`,
					   `OOB_User_User`.`Connections`, `OOB_User_User`.`Status` AS `UserStatus`,
				       `OOB_User_User`.`EmployeeID`, `Personnel_Employee`.`LastName`, 
			 		   `Personnel_Employee`.`FirstName`, `Personnel_Employee`.`BirthDate`, 
					   `Personnel_Employee`.`EmployeeNumber`, `Personnel_Employee`.`Description`, 
					   `Personnel_Employee`.`Status` AS `EmployeeStatus`
				FROM `OOB_User_User` LEFT JOIN `Personnel_Employee` 
				ON `OOB_User_User`.`EmployeeID` = `Personnel_Employee`.`ID`	 		
				WHERE `OOB_User_User`.`$field` IN ($in)
			    AND `Personnel_Employee`.`Status` = '" . USED . "'";
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);				
		$rs = $ari->db->Execute($sql);

		$ari->db->SetFetchMode($savem);
		$i= 0;
		if (!$rs || $rs->EOF) 
		{	$return = false;	}
		else
		{
			while (!$rs->EOF) 
			{
				$return[$i] = new oob_user (ID_UNDEFINED);
				$return[$i]->user = $rs->fields["UserID"];
				$return[$i]->uname = $rs->fields["Uname"];
				$return[$i]->password = $rs->fields["Password"];
				$return[$i]->email = $rs->fields["Email"];
				$return[$i]->maxcon = $rs->fields["Connections"];
				$return[$i]->status = $rs->fields["UserStatus"];				
				
				if ( is_null($rs->fields["EmployeeID"]) )
				{
					$return[$i]->employee = NO_OBJECT;
				}
				else
				{
					$return[$i]->employee = new personnel_employee(ID_UNDEFINED);
					$return[$i]->employee->set("id",$rs->fields["EmployeeID"]);
					$return[$i]->employee->set("lastName", $rs->fields["LastName"]);
					$return[$i]->employee->set("firstName",$rs->fields["FirstName"]);			
					$return[$i]->employee->set("birthDate", new Date($rs->fields['BirthDate']) );
					$return[$i]->employee->set("employeeNumber", $rs->fields["EmployeeNumber"]);	
					$return[$i]->employee->set("description", $rs->fields["Description"]);
					$return[$i]->employee->set("status",$rs->fields["EmployeeStatus"]);
				}//end if
			
				$rs->MoveNext();
				$i++;
			}//end while			
			$rs->Close();
		} //end if

		$rs->Close();
		return $return;
 	}//end function	
  	
	
	
	/** Retorna los usuarios habilitados (status USED) que no tienen asignado un empleado */
	static public function listNoAssigned($sort = 'uname', $pos = 0, $limit = 20)
	{
		global $ari;
		$estado = $ari->db->qMagic(USED);	
		
		if (in_array ($sort, oob_user::getOrders()))
		{	$sortby = "ORDER BY $sort";
		}
		else
		{	$sortby = "ORDER BY uname";
		}
			
		$sql= "SELECT * 
			   FROM oob_user_user  
			   WHERE status = $estado
			   AND EmployeeID =0
			   $sortby
			  ";

		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		if($pos === false || $limit === false)
		{	$rs = $ari->db->Execute($sql);
		}
		else
		{	$rs = $ari->db->SelectLimit($sql, $limit, $pos);
		}
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{ 
			$i=0;
			while (!$rs->EOF) 
			{
				$return[$i] = new oob_user (ID_UNDEFINED);
				$return[$i]->user = $rs->fields["id"];
				$return[$i]->uname = $rs->fields["uname"];
				$return[$i]->password = $rs->fields["password"];
				$return[$i]->email = $rs->fields["email"];
				$return[$i]->maxcon = $rs->fields["connections"];
				$return[$i]->status = $rs->fields["status"];				
				$i++;
				$rs->MoveNext();
			}			
			$rs->Close();
		} 
		else
		{	return false;
		}
	
		return $return;
		
	}//end function


	/** Retorna la cantidad de usuarios no asignados a un empleado, cuyo estado es USED */
	static public function userCountNoAsigned()
	{
		global $ari;
		$estado = $ari->db->qMagic(USED);

		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$sql= "SELECT COUNT(*) 
			   FROM oob_user_user  
			   WHERE status = $estado
			   AND EmployeeID = 0
			  ";
			  
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{	$return = $rs->fields[0];	
			$rs->Close();
		} 
		else
		{	$return = 0;
		}
		return $return;
	
	}//end function

	/** Lista los usuario on-line.
	 * 
	 */
	public function userOnLineList() 
	{
		global $ari;
		$sessionkey = session_id();
		$sql= "SELECT user_id FROM oob_user_online";
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{ 
			while (!$rs->EOF) 
			{
				$return[] = new oob_user ($rs->fields[0]);
				$rs->MoveNext();
			}			
			$rs->Close();
		}
		else
		{	return false;
		}

		return $return;
		
	}


	/** Retorna la cantidad de usuarios on-line */
	static public function userOnLineCount()
	{
		global $ari;
		$sql= "SELECT COUNT(*) FROM oob_user_online";
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);  
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{	$return = $rs->fields[0];	
			$rs->Close();
		} 
		else
		{	$return = 0;
		}
		return $return;
	
	}//end function
		
	static public function __SQLsearchRemote($field, $comparison, $value, $connector, $type, $remote_class, $remote_attribute = false)
	{
		global $ari;
		$table = 'oob_user_user';
		$sql_data = $sql_join = array();
		
		$operadores = array();
		$operadores["eq"] = "=";
		$operadores["lt"] = "<";
		$operadores["gt"] = ">";
		$operadores["eqgt"] = ">=";
		$operadores["ltgt"] = "<=";
		$operadores["neq"] = "!=";
		
		if ($field == '' || $field =='user') // cosas del diseño, el field es id, pero la variable es user
		{
			$field = 'id';
		}
		
		$remote_table = $remote_class::getTable();
			
		$sql_join[] = 'JOIN ' . $table . ' ON (' . $table. '.id = ' .  $remote_table . '.' . $remote_attribute . ')';
		
		$operador_inicio = $operadores[$comparison];
		$operador_fin = "";	
		

		$sql_data[] = ' ' . $connector . ' ' . $table.'.'. $field . $operador_inicio  . $value . $operador_fin;
			
		return array(
					'data' => $sql_data,
					'join' => $sql_join
					);
		
	}
	
	
}//end class
?>