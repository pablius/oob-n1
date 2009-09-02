<?
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
#  @version 1.1
######################################## 
*/

/**
 This class can handle user-errors that wont be throwing an exception. 
 That is, that have been taken in consideration by the programer.
 This error handler must be used from inside an object. 

 */
class OOB_IE 
{
	
	private $error = array();
	private $logfile;
	private $className = "";
	
	public function __construct ($className = NO_OBJECT) 	
	{
		global $ari;
		$this->className = $className;	
		$this->logfile = $ari->filesdir . DIRECTORY_SEPARATOR . "error.log";
	}
	/** Adds an error to the handler, you must provide  "message" and "log" (true-false) if you want to log the error. */
	public function addError ($msg, $log = false) 
	{
		$this->error[] = $msg;
	
		if ($log)
		{
			$today = date("d/m/Y@H:i:s");
			$logmessage = "[" . $today . "] - " . $_SERVER['REMOTE_ADDR'] . " |Error| " . $this->className . " :: " . $msg . "\r\n";
	
			error_log( $logmessage, 3, $this->logfile );
		}
	}
	
	/** Returs all the errors that the handler has */
	public function getErrors () {
	return $this->error; 
	}
	
	/** Returns true if there are any errors in the handler */ 
	public function areError () {
		if (count($this->error) > 0)
		return true;
		else
		return false;
	}
	
}

/** Esta es la clase de referencia, para utilizar internal error
 * ud debe extender esta clase, y nada más
 * 
 * Ejemplo:
 * class EE extends OOB_internalerror
 *	{
 *	 public function hola ()
 *	 {return "hola";
 *	 } 
 *  }
 * 
 * Y para utilizar el manejador de errores simplemente hará:
 * $a = new EE ();
 * $a->error()->addError ("LALALA");
 * $a->error()->getErrors();
 **/
abstract class OOB_internalerror
{
	private $internalerror = NO_OBJECT;

	public function error () 
	{
		if ($this->internalerror === NO_OBJECT)
		{
			$this->internalerror = new OOB_IE();
		}
		
		return $this->internalerror;
	}
	
	/** valida una variable dentro del objeto y agrega el error_code, si no valida
	 	variable : nombre de la variable
		constraints : nombre_restriccion-parametro-parametro (pueden ir varios separados por coma) 
		error_code: codigo error
		
		nota >> los nombres sde las restricciones corresponden a los métodos de OOB_validatetext
	*/
	protected function validateVariable ($variable, $constraints, $error_code)
	{
		// validamos que haya una regla @fixme:: esta validación tiene que ser un poco más estricta
		if (strlen($constraints) < 1)
		{
			throw new OOB_exception("Constraint undefined", "839", "Constraint undefined for " . $variable, true);
		}
		
		
		
		$constraints = explode (',',$constraints);
		$i = 0;
						
		foreach ($constraints as $constraint)
		{
			$valor_variable = $this->$variable;		
			
			$constraints_vars = explode ('-', $constraint);
			
			$constraint_action = array_shift($constraints_vars);
			
			switch(strtolower($constraint_action))
			{
				
				case 'trim':
				{
					
					$this->$variable = trim($this->$variable);
					return;
					break;
				}
				
				case 'plain':
				{
					return;
					break;
				}
				
				// CASO ESPECIAL MANEJA OBJETOS
				case 'object':
				{
					// caso especial DATE OBJECT
					if (is_a($this->$variable,'Date'))
					{
						$valor_variable = $valor_variable->getDate();
						$constraint_action = 'isDate';
						$constraints_vars =array();			
					}
					else 
					{
						return;
					}
					break;
				}
				
				// CASO ESPECIAL MANEJA OBJETOS
				case 'manyobjects':
				{
					if (count($this->$variable) > 0)
					{
						// si es un objeto lo deja pasar, hay que tener cuidado acá con lo que llega!
						foreach ($this->$variable as $object)
						{

							if (!is_a($object,$constraints_vars[0]))
							{
								$this->error()->addError ($error_code);
							}
							
						}
						return; 		
					}
					else 
					{	// está vacio
						return;
					}
					break;
				}
				
				// CASO ESPECIAL DIRECTORIOS
				case 'isvaliddir':
				{
					// @fixme:: falta parent
					$valor_variable = mysql_escape_string ($valor_variable);
					break;
				}
				
				// casos depreciados de DATE
				case 'isvaliddate':
				case 'isdate':
				{
					throw new OOB_exception("Define attribute as DATE object", "827", "Define as DATE object: " . $variable, true);
					break;
				}
				
				default:
				{
					break;
				}

			} // end switch
			

			// escapamos el string (y escapamos el signo de $ porque representa las variables en php)
			$valor_variable = '"'. str_replace('$','\$',$valor_variable) . '"';
			
			//
			array_unshift($constraints_vars,$valor_variable);
			$constraints_vars = join(',',$constraints_vars);
		
			
			
			if (!eval("return OOB_validatetext::$constraint_action($constraints_vars);"))
			{
				$this->error()->addError ($error_code);
				return;
			} 
		
		}		
		
	}// end function
} //end class

?>