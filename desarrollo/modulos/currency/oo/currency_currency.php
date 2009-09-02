<?php
# Eumafes v2 [ï¿½2005 - Nutus, Todos los derechos reservados]
/*
 * Created on 13-jul-2005
 * @author Victor Stornati (victor.stornati@nutus.com.ar)
 */
 class currency_currency 
 {
   private $id = ID_UNDEFINED;
   private $name = '';
   private $sign = '';
   private $value = '';
   private $type = '';
   private $default = NO;
   private $status = '';
   private $miles_separator = '';
   private $decimal_separator = '';
   
	/** Starts the currency. 
 	    if no currency set we must believe is a new one */
   function __construct($id = ID_UNDEFINED)
   { 
     	global $ari;

		if ($id > ID_MINIMAL) 
		{
			$this->id = $id;
			
			if (!$this->fill ())
			{throw new OOB_exception("Invalid Currency {$currency}", "403", "Invalid Currency", false);}
					
		} 
   } 
   
   public function id()
   {
		return $this->id;
   }

	/** Returs the value for the given var */ 	
 	public function get ($var)
 	{
 		if (isset ($this-> $var) && !empty ($this-> $var))
			return $this-> $var;
		else
			return false;
 	}

	/** Sets the variable (var), with the value (value) */ 	
 	public function set ($var, $value)
 	{
		if (isset ($this->$var))
			$this->$var= $value;
		else
			return false; 	
 	} 
 	
	/** */ 
 	static public function listCurrenciesForLanguage( $status = USED, $sort = 'name', $operator = OPERATOR_EQUAL, $language, $where = '')
	{
		global $ari;
/*
		if (in_array ($status, currency_currency :: getStatus ("ALL",true)) && $status != "all")
		{
			$estado = "WHERE status $operator '$status'";
		}
		else
		{
			if ($status == "all")
				{$estado = "";}
			else
				{$estado = "WHERE status $operator '". USED. "'";}
		}
*/
		if (strtolower($status) == "all")
			{$estado = "";}
		else
			{$estado = " AND Status $operator '". $status. "'";}
				
		//if (in_array ($sort, currency_currency::getOrders()))
			$sortby = "ORDER BY $sort";
		//else
		//	$sortby = "ORDER BY name";

		$language = $ari->db->qMagic($language);
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		
//		echo 
		
		$sql= "SELECT C.`ID`, C.`Name`, C.`Sign`, C.`Value`, C.`Type`, C.`Default`, C.`Status` 
			   FROM `Currency_Currency` C,`Currency_CurrencyLanguage` CL  
			   WHERE C.`ID` = CL.`CurrencyID`
			   AND CL.`Language` = $language  
			   $estado $where $sortby";
			   
	
		$rs = $ari->db->Execute($sql);

		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{ 
			$i = 0;
			while (!$rs->EOF) 
			{
				$return[$i] = new currency_currency (ID_UNDEFINED);
				$return[$i]->set('id', $rs->fields['ID']);
				$return[$i]->set('name', $rs->fields['Name']);
				$return[$i]->set('sign', $rs->fields['Sign']);
				$return[$i]->set('value', $rs->fields['Value']);
				$return[$i]->set('type', $rs->fields['Type']);
				$return[$i]->set('default', $rs->fields['Default']);
				$return[$i]->set('status', $rs->fields['Status']);
				$i++;
				$rs->MoveNext();
			}			
		} else
		{$return = false;}
		$rs->Close();

		return $return;
 
	}
	
	/** Stores/Updates currency object in the DB */	
	public function store ()
	{
		global $ari;
		// clean vars !

		$this->name = trim($this->name);
		$this->sign = trim($this->sign);
		$this->value = trim($this->value);
		$this->type = trim($this->type);
		$this->status = trim($this->status);
		$this->default = trim($this->default);		

		if (!OOB_validatetext :: isClean($this->name) || !OOB_validatetext :: isCorrectLength ($this->name, 1, MAX_LENGTH))
		{	$ari->error->addError ("currency_currency", "INVALID_NAME");	}

		if (!OOB_validatetext :: isClean($this->sign) || !OOB_validatetext :: isCorrectLength ($this->sign, 1, MAX_LENGTH))
		{	$ari->error->addError ("currency_currency", "INVALID_SIGN");	}

		if (!OOB_validatetext :: isCorrectLength ($this->value, 1, MAX_LENGTH))
		{$ari->error->addError ("currency_currency", "INVALID_VALUE");		}
		
		//@todo: falta validar pto y coma segun config del idioma y guardar segun acepta MySQL
		if (!OOB_numeric :: isValid($this->value) || $this->value <= 0  )
		{	$ari->error->addError ("currency_currency", "INVALID_VALUE");	}		
		
		//valido q no exista la moneda
		if ($this->id == ID_UNDEFINED) 					
		{//para nuevo busco uno con el mismo nombre
			$clausula = "";
		}
		else
		{//si actualizo busco con el mismo nombre pero con el mismo id
			$clausula = " AND id <> $this->id";	
		}
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$name = $ari->db->qMagic($this->name);
		$sql= "SELECT true FROM currency_currency WHERE name = $name $clausula";
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);

		if (!$rs->EOF && $rs) 
		{						
			if ($this->id == ID_UNDEFINED) 					
			{//para nuevo
			// si la moneda con el mismo nombre esta borrada la activo, sino da instancio un error
			   $sql= "SELECT ID FROM currency_currency WHERE Name = $name AND Status = '" . DELETED . "'";
			   $rs2 = $ari->db->Execute($sql);
				if (!$rs2->EOF && $rs) 
				{
					//asigno el id del el objeto que volvi a activar
					$this->id = $rs2->fields[0];
					$this->status = USED;
				}	
				else
				{
					$ari->error->addError ("currency_currency", "DUPLICATE_CURRENCY");
				}//end if !rs->EOF
			}//end if $this->group == ID_UNDEFINED
			else
			{
				$ari->error->addError ("currency_currency", "DUPLICATE_CURRENCY");
			}// end if
		}
		
		$rs->Close();		
			
		if (!$ari->error->getErrorsfor("currency_currency"))
		{
			$ari->db->StartTrans();
			$id = $ari->db->qMagic($this->id);
			$value = OOB_numeric :: formatMySQL($this->value);
			$value = $ari->db->qMagic($value);
			$sign = $ari->db->qMagic($this->sign);
			$type = $ari->db->qMagic($this->type);
			$status = $ari->db->qMagic($this->status);
			$name = $ari->db->qMagic($this->name);	
		 	$default = $ari->db->qMagic($this->default);
		 				
			if ($this->id > ID_MINIMAL)
			{
				// update data
				$sql= "UPDATE Currency_Currency
					   SET Currency_Currency.Name = $name,
					   	   Currency_Currency.Sign = $sign,
					   	   Currency_Currency.Value = $value,
						   Currency_Currency.Type = $type,
						   Currency_Currency.Default = $default,		
						   Currency_Currency.Status = $status
					   WHERE ID = $id";
				$ari->db->Execute($sql);
					
				$return = true;
				
					
			} 
			else 
			{
				// insert new 
				$sql= "INSERT INTO Currency_Currency
					   ( `Name`, `Sign`, `Value`, `Type`, `Default`, `Status`)
					   VALUES ( $name, $sign,$value, 
					   		   $type, $default, $status )
						   	";
								
				$ari->db->Execute($sql);
				$this->id = $ari->db->Insert_ID();
				$return = true;
			
			}//end if
			
			//si la moneda actual es la predeterminada debo setear como no prdeterminada a la anterior
			if ($this->default == YES)
			{
				$id = $ari->db->qMagic($this->id);
				$sql= "UPDATE Currency_Currency
					   SET `Default` = " . NO . "
					   WHERE `Default` = " . YES . " 
					   AND `ID` <> $id
					  ";
				$ari->db->Execute($sql);
			}
			
			if (!$ari->db->CompleteTrans())
			{	throw new OOB_exception("Error en DB: $ari->db->ErrorMsg()", "010", "Error en la Base de Datos", false); 	}//return false;
			else
			{	$return = true;	}			
			
			return $return;
		} 
		else 
		{
			// no validan los datos
			 return false; //devuelve un objeto de error con los errores!
		}//end if
	}//end function

 	/**	devuelve true si se puede borrar la moneda*/ 
 	public function allowDelete ()
 	{
		global $ari;
		
		if ($this->default == YES)
		{	return false;	}
					
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$sql= "SELECT True FROM Currency_Value WHERE CurrencyID = '$this->id' ";
		
		$rs = $ari->db->Execute($sql);
		
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{ 	$return = false;	}	 
		else
		{	$return = true;		}
		$rs->Close();
		return $return;		
		
 	}//end function 

	/** trae la moneda definida como predeterminada */	
	static public function getDefault()
	{
		global $ari;
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql= "SELECT `ID`, `Name`, `Sign`, `Value`, `Type`, `Default`, `Status`
			   FROM `Currency_Currency` 
			   WHERE `Default` = '" . YES . "'
			   AND `Status` = '" . USED . "'";
		
		$rs = $ari->db->Execute($sql);

		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{ 
			$return = new currency_currency (ID_UNDEFINED);
			$return->set('id', $rs->fields['ID']);
			$return->set('name', $rs->fields['Name']);
			$return->set('sign', $rs->fields['Sign']);
			$return->set('value', $rs->fields['Value']);
			$return->set('type', $rs->fields['Type']);
			$return->set('default', $rs->fields['Default']);
			$return->set('status', $rs->fields['Status']);
		} 
		else
		{$return = false;}
		$rs->Close();

		return $return;
	}	

 	/** Adds a language to the current currency.
 	 * Returns true if successful, false if not.
    */
 	public function addLanguage ($lang )
 	{
 		global $ari;
 		$lang = trim ($lang);
 		if (OOB_validatetext :: isCorrectLength ($lang, 1, MAX_LENGTH) )
        {
 			if ( $this->isMember($lang) )
 			{
 				$return = true; 
 			}
 			else
 			{
	 			//falta validar si existe el usuario
				$id = $ari->db->qMagic($this->id);
				$lang = $ari->db->qMagic($lang);
				
				$ari->db->StartTrans();						
				$sql= "INSERT INTO Currency_CurrencyLanguage
					  	(CurrencyID, Language)
					   VALUES 
					   	($id, $lang )
					  ";
				$ari->db->Execute($sql);
				if (!$ari->db->CompleteTrans())
				{	throw new OOB_exception("Error en DB: $ari->db->ErrorMsg()", "010", "Error en la Base de Datos", false); //return false;
				}
				else
				{	return true;
				}
			}
        }
        else
        {	$ari->error->addError ("currency_currency", "INVALID_LANGUAGE");            	
        }
        
        if (!$ari->error->getErrorsfor("currency_currency"))
        {	return $return;
        }
        else
        {	return false;
        }
        
 	}
 	//end function


 	/** 
 	 * Returns true if successful, false if not.
    */
 	static public function removeAllLanguages ($currency )
 	{
 		global $ari;
 		
 		if (!is_a($currency, 'currency_currency'))
 		{
 			$ari->error->addError ("currency_currency", "INVALID_CURRENCY");	
 			return false;
 		}

		$id = $currency->get('id');
				
		$sql= "DELETE FROM Currency_CurrencyLanguage
			  WHERE CurrencyID = $id";
 		
		$ari->db->StartTrans();
		$ari->db->Execute($sql);

		if (!$ari->db->CompleteTrans())
		{
			throw new OOB_exception("Error en DB: $ari->db->ErrorMsg()", "010", "Error en la Base de Datos", false); //return false;
		}
		else
		{
			return true;
		}        
 	}
 	//end function

    /**  Returns true if the languafe ê\ang is a member of the currency     */
    public function isMember( $lang )
    {
        global $ari;
        
 		if (OOB_validatetext :: isCorrectLength ($lang, 1, MAX_LENGTH) )
        {
        	$lang = $ari->db->qMagic($lang);
        	$sql= "SELECT True 
        		   FROM Currency_CurrencyLanguage 
        		   WHERE CurrencyID = '$this->id' 
        		   AND Language = $lang";
			$rs = $ari->db->Execute($sql);	
			if (!$rs->EOF && $rs) 
			{	$return = true;		}
			else
			{	$return = false;	}	
        }
        else
        {	$ari->error->addError ("currency_currency", "INVALID_LANGUAGE");	}
        
        $rs->Close();
        
        if (!$ari->error->getErrorsfor("currency_currency"))
        {
        	return $return;
        }
        else
        {
        	return false;
        }
    }
    
    //end function 	
    
	/** Fills the currency with the DB data */ 	
 	private function fill ()
 	{
		global $ari;

		//load info
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql= "SELECT Currency_Currency.Name, Currency_Currency.Sign, 
			   Currency_Currency.Value,Currency_Currency.Type, 
			   Currency_Currency.Default,Currency_Currency.Status
			   FROM Currency_Currency WHERE id = '$this->id'";
			   
		$rs = $ari->db->Execute($sql);
			
		$ari->db->SetFetchMode($savem);
		if (!$rs || $rs->EOF) 
		{	return false;	}
		
		if (!$rs->EOF) 
		{
			$this->name = $rs->fields['Name'];
			$this->sign = $rs->fields['Sign'];
			$this->value = $rs->fields['Value'];
			$this->type = $rs->fields['Type'];
			$this->default = $rs->fields['Default'];
			$this->status = $rs->fields['Status'];
		}
		$rs->Close();
		return true; 		
 	}//end function

 	/** */ 
 	static public function listCurrenciesForType ($status = USED, $sort = 'name', $operator = OPERATOR_EQUAL, $language, $type)
	{
		global $ari;
/*
		if (in_array ($status, currency_currency :: getStatus ("ALL",true)) && $status != "all")
		{
			$estado = "WHERE status $operator '$status'";
		}
		else
		{
			if ($status == "all")
				{$estado = "";}
			else
				{$estado = "WHERE status $operator '". USED. "'";}
		}
*/
		if (strtolower($status) == "all")
			{$estado = "";}
		else
			{$estado = " AND Status $operator '". $status. "'";}
				
		//if (in_array ($sort, currency_currency::getOrders()))
			$sortby = "ORDER BY $sort";
		//else
		//	$sortby = "ORDER BY name";

		$type_where = "";
		if ($type != FLOAT_CHANGE && $type != FIXED_CHANGE && $type!='all' & $type != '')
		{	return false;	}
		

		$language = $ari->db->qMagic($language);
		$type = $ari->db->qMagic($type);
		
		if ($type != FLOAT_CHANGE && $type != FIXED_CHANGE)
		{	$type_where = " AND C.`Type` = ".  $type;		}
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql= "SELECT DISTINCT C.`ID`, C.`Name`, C.`Sign`, C.`Value`, C.`Type`, C.`Default`, C.`Status` 
			   FROM `Currency_Currency` C,`Currency_CurrencyLanguage` CL  
			   WHERE C.`ID` = CL.`CurrencyID`
			   AND CL.`Language` = $language
			   AND C.`Default` = '" . NO . "' 
			   $estado $type_where $sortby";
		
		$rs = $ari->db->Execute($sql);

		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{ 
			$i = 0;
			while (!$rs->EOF) 
			{
				$return[$i] = new currency_currency (ID_UNDEFINED);
				$return[$i]->set('id', $rs->fields['ID']);
				$return[$i]->set('name', $rs->fields['Name']);
				$return[$i]->set('sign', $rs->fields['Sign']);
				$return[$i]->set('value', $rs->fields['Value']);
				$return[$i]->set('type', $rs->fields['Type']);
				$return[$i]->set('default', $rs->fields['Default']);
				$return[$i]->set('status', $rs->fields['Status']);
				$i++;
				$rs->MoveNext();
			}			
		} else
		{$return = false;}
		$rs->Close();

		return $return;
 
	}

	/** trae el ultimo valor de la moneda actual  */	
	public function getLastChange()
	{
		global $ari;
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql= "SELECT `Value`, `Date`
			   FROM `Currency_Change` 
			   WHERE `CurrencyID` = '$this->id'
			   AND `Date` = 
			   (SELECT MAX(`Date`)
			   FROM `Currency_Change`
			   WHERE `CurrencyID` = '$this->id')" 
			   ;

		$rs = $ari->db->Execute($sql);

		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{ 
			$return['value'] = $rs->fields['Value'];
			$return['date'] = $rs->fields['Date'];
		} 
		else
		{$return = false;}
		$rs->Close();

		return $return;
	}		

 	/** 
 	 * Returns true if successful, false if not.
    */
 	public function addChange ( $value, $date = false)
 	{
 		global $ari;
 		
 		$flagStore = true;
 		if (!OOB_numeric :: isValid ($value))
		{
			$flagStore = false; 	
			$ari->error->addError ("currency_currency", "INVALID_VALUE");	
		}			
		
		if(!$date)
		{	$date = new Date();
		}
		
		//valid and clean Date
		if(!OOB_validatetext :: isValidDate($date))
		{
			$flagStore = false; 	
			$ari->error->addError ("currency_currency", "INVALID_DATE");	
		}
		else
		{	$date = $ari->db->qMagic($date->format("%Y-%m-%d %H:%M:%S"));
		}
		
		$value = OOB_numeric :: formatMySQL(trim ($value));

		if ($flagStore)
		{
			$value = $ari->db->qMagic($value);
			$id = $ari->db->qMagic($this->id);

 			$ari->db->StartTrans();
			$sql= "INSERT INTO Currency_Change
				   ( Date, Value, CurrencyID)
				   VALUES 
				   ( $date,$value,$id )
				  ";
			//echo $sql; exit;
			$ari->db->Execute($sql);

			if (!$ari->db->CompleteTrans())
			{
				throw new OOB_exception("Error en DB: $ari->db->ErrorMsg()", "010", "Error en la Base de Datos", false); //return false;
			}
			else
			{
				return true;
			}
		}//end if
  		else        
        {	return false;	}
        
 	}
 	//end function

 	/** Deletes data object */ 	
 	public function delete ()
 	{
		global $ari;
		
		if (!$this->allowDelete())
		{
			$ari->error->addError ("currency_currency", "ACCESS_DENIED");		
			return false;	
		}
		
		// sets status DELETED for a currency
		if ($this->id > ID_MINIMAL && $this->status != DELETED) {
			
			$ari->db->StartTrans();
			$sql= "UPDATE currency_currency SET  status = '" . DELETED . "' WHERE id = '$this->id'";
			$ari->db->Execute($sql);
			
			//@todo: borro el historial de cambios pero dejo el ultimo
			/*tengo q probarlo
			$sql= "DELETE FROM `Currency_Change` 
			   	   WHERE `CurrencyID` = '$this->id'
			   	   AND `Date` <> 
			       (SELECT MAX(`Date`)
			       FROM `Currency_Change`
			       WHERE `CurrencyID` = '$this->id')" 
			
			$ari->db->Execute($sql);
			
			//@todo: borro los lenguages permitidos ?
			*/
					
			
			if ($ari->db->CompleteTrans())
				return true;
			else
				throw new OOB_exception("Error en DB: $ari->db->ErrorMsg()", "010", "Error en la Base de Datos", false);
					

		} else {
			if ($this->status == DELETED)
				$ari->error->addError ("currency_currency", "ALREADY_DELETED");
			else
				$ari->error->addError ("currency_currency", "NO_SUCH_CURRENCY");
			
			return false;
		} 		
				 		
 	}//end function

	/** trae los idiomas habilitados para la moneda  */	
	static public function getLanguages($currency)
	{
		global $ari;
		
		if(!is_a($currency, 'currency_currency'))
		{
			$ari->error->addError ("currency_currency", "INVALID_CURRENCY");
			return false;	
		}
		
		$id = $ari->db->qMagic($currency->get('id'));
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql= "SELECT `Language`
			   FROM `Currency_CurrencyLanguage` 
			   WHERE `CurrencyID` = $id";
		
		$rs = $ari->db->Execute($sql);
		
		$return = array();
		$i = 0;
		
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{ 
			while (!$rs->EOF) 
			{
				$return[$i] = $rs->fields['Language'];
				$i++;
				$rs->MoveNext();
			}
		} 
		else
		{$return = false;}
		$rs->Close();

		return $return;
	}		

	/** Retorna los valores de cambios de la moneda pasada, en el periodo pasado */	
	public function getChanges($desde = false, $hasta = false)
	{
		global $ari;
		$id = $ari->db->qMagic($this->id);
		$clausula = '';
		if($desde)
		{	$desde = $ari->db->qMagic($desde->getDate());
			if($hasta)
			{	$hasta = $ari->db->qMagic($hasta->getDate());
				$clausula = " AND date BETWEEN $desde AND $hasta ";
			}
			else
			{	$clausula = " AND date > $desde ";
			}
		}
		else
		{	
			if($hasta)
			{	$hasta = $ari->db->qMagic($hasta->getDate());
				$clausula = " AND date < $hasta ";
			}
		
		}
		
		$savem = $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql= "SELECT value, date
			   FROM currency_change 
			   WHERE currencyID = $id
			   $clausula
			   ORDER BY date DESC
			  ";		
		//echo $sql;
		//exit;
		
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{ 
			$i=0;
			while(!$rs->EOF)
			{	$return[$i]['value'] = $rs->fields['value'];
				$return[$i]['date'] = $rs->fields['date'];
				$i++;
				$rs->MoveNext();
			}
			$rs->Close();
		} 
		else
		{	$return = false;
		}

		return $return;
	
	}//end function								


	/** Retorna un array con las monedas y los valores de cambio, 
	 *  segun la moneda actual y el valor pasado. 
	 *	Estructura del array de retorno:
	 *	$return[]["currency"] = oCurrency;
	 *	$return[]["valueDesde"] = nDesde	
	 *	$return[]["valueHasta"] = nHasta	
	 */	
	public function arrayVariations($valueDesde = false, $valueHasta = false)
	{
		global $ari;
		$return = false;
		
		if(!$valueDesde && !$valueHasta)
		{	return $return;
		}
		
		//var_dump($valueDesde);
		//var_dump($valueHasta);exit;
		
		//traducir valor hacia la moneda predeterminada
		if($this->default)
		{	
			if($valueDesde)
			{	$patternDesde = $valueDesde;	
			}
			if($valueHasta)
			{	$patternHasta = $valueHasta;	
			}
		}
		else
		{	
			if($change = $this->getLastChange())
			{	$change = $change["value"];
			}
			else
			{	$change = 1;
			}
			
			//var_dump($change);exit;
			
			if($valueDesde)
			{	$patternDesde = $valueDesde * $change;	
			}
			if($valueHasta)
			{	$patternHasta = $valueHasta * $change;	
			}
			
		}
		
		//recorrer todas las monedas
		$language = $ari->get('agent')->getLang();
		if ($currencies = currency_currency :: listCurrenciesForLanguage (USED, 'name', OPERATOR_EQUAL, $language) )
		{	
			$i = 0;
			foreach($currencies as $c)
			{
				if($c->default)
				{	$change = 1;
				}
				else
				{	
					if($change = $c->getLastChange())
					{	$change = $change["value"];
					}
					else
					{	$change = 1;
					}
				}
				
				//var_dump($change);echo "<br /><br />";
				
				$return[$i]["currency"] = $c;
				if($valueDesde)
				{	$return[$i]["valueDesde"] = $patternDesde / $change;	
				}
				if($valueHasta)
				{	$return[$i]["valueHasta"] = $patternHasta / $change;	
				}
				
				$i++;
			}	
		}
		
		//var_dump($return);
		//exit;	
		return $return;
	
	}//end function			

	//Devuelve la cotizacion de la moneda
	public function get_quote( $currency_value ){
	
		global $ari;		
			
		$id = $ari->db->qMagic($this->get('id'));
		$fecha = $ari->db->qMagic($currency_value->get('date'));
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		
		
		$sql = " SELECT currency_change.Value, currency_change.Date 
				 FROM `currency_change`
				 WHERE currency_change.currencyid = $id and currency_change.DATE <= $fecha
				 ORDER BY currency_change.DATE DESC LIMIT 0,1 ";
		file_put_contents("holaaaaaaaa.txt",var_export($currency_value,true));
		
		$rs = $ari->db->Execute($sql);
		
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{ 
			$return['value'] = $rs->fields['Value'];
			$return['date'] = $rs->fields['Date'];
		} 
		else
		{$return = false;}
		$rs->Close();

		return $return;
	
	}//end function

	//metodo que convierte una moneda a la moneda predeterminada	
	public function ConvertToDefault( $currency_value, $previous_change = false ){
	
		return currency_currency::Convert( $currency_value, currency_currency::getDefault(), $previous_change );		
			
		
	}//end function
	
	// $currency_value -> valor moneda desde , $currency ->moneda hasta
	// previous_change -> true si se quiere convertir con la cotizacion del momento de creacion de el currency_value
	public function Convert( $currency_value, $currency , $previous_change = false ){
	
		$multiplicador = 0;		
		
		//moneda predeterminada
		$default = currency_currency::getDefault();
		
		if( $previous_change ){
			$last_change_from = $currency_value->get('currency')->get_quote($currency_value);						
			$last_change_from_value = $last_change_from[0]['value'];
			$last_change_to = $currency->get_quote($currency_value);						
			$last_change_to_value = $last_change_to[0]['value'];
		}
		else
		{
			$last_change_from = $currency_value->get('currency')->getLastChange() ;
			$last_change_from_value = $last_change_from['value'];
			$last_change_to = $currency->getLastChange() ;
			$last_change_to_value = $last_change_to['value'];
		}	
			
		$default_is_from = ( $currency_value->get('currency')->id() == $default->id() );
		$defailt_is_to = ( $currency->id() == $default->id() );
		
		$valor = $currency_value->get('value');
		
		$new_value = 0;
		
				
			//si la moneda desde es la predeterminada
			if( $default_is_from ){				
				//si tengo que convertir a una moneda fija(1)
				if( $currency->get('type') == 1 ){
					$new_value = $valor * $currency->get('value');
				}
				else
				{					
					$new_value = $valor * $last_change_to_value;
				}//end if			
			
			}
			else
			{	
			
			//NO PREDETERMINADA
			
				// //si tengo que convertir a una moneda fija(1)
				if( $currency_value->get('currency')->get('type') == 1 ){					
				
					if( $defailt_is_to ){
						$new_value =  $valor / $currency_value->get('currency')->get('value');
					}
					else
					{
						if( $currency->get('type') == 1 ){
							$new_value =  ($valor / $currency_value->get('currency')->get('value')) * $currency->get('value');
						}
						else
						{	
							$new_value =  ($valor / $currency_value->get('currency')->get('value')) * $last_change_to_value;
						}//end if
					
					}//end if
					
					
				}
				else
				{

					if( $defailt_is_to ){
						$new_value =  $valor / $last_change_from_value;
					}
					else
					{
						if( $currency->get('type') == 1 ){
							$new_value =  ($valor / $last_change_from_value) * $currency->get('value');
						}
						else
						{	
							$new_value = ($valor / $last_change_from_value) * $last_change_to_value;
						}//end if
					
					}//end if
				
				
				}//end if		
		
			}//end if			
			
				
		$new_currency_value = new currency_value();
		$new_currency_value->set( 'value', $new_value );		
		$new_currency_value->set( 'currency', $currency );
	
	
		return $new_currency_value;
	
	}//end function
	
	
}//end class
 
 
?>
