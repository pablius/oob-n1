<?php
# Eumafes v2 [�2005 - Nutus, Todos los derechos reservados]
/*
 * Created on 06-ago-2005
 * @author Victor Stornati (victor.stornati@nutus.com.ar)
 */
 
 OOB_module :: includeClass ('currency', 'currency_currency');
 
 class currency_value
 {
 	private $id = ID_UNDEFINED;
 	//@todo: object datetime
 	private $date = '';
 	private $value = '';
 	private $currency = NO_OBJECT; 
 	
 	
 	/** Starts the currency value. 
 	if no currency value set we must believe is a new one */
   function __construct($id = ID_UNDEFINED)
   { 
     	global $ari;

		if ($id > ID_MINIMAL) 
		{
			$this->id = $id;
			
			if (!$this->fill ())
			{throw new OOB_exception("Invalid Currency Value {$id}", "403", "Invalid Currency Value", false);}
					
		} 
   } 
 	
 	/** Returs the value for the given var */ 	
 	public function get ($var)
 	{
 		if (isset ($this-> $var) && !empty ($this-> $var))
		{	return $this-> $var;	}
		else
		{	return false;	}
 	}

	/** Sets the variable (var), with the value (value) */ 	
 	public function set ($var, $value)
 	{
		if (isset ($this->$var))
		{	$this->$var= $value; 	}
		else
		{	return false;	} 	
 	}  	
 	

	/** Fills the currency with the DB data */ 	
 	private function fill ()
 	{
		global $ari;

		//load info
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql= "SELECT Currency_Value.Date, Currency_Value.Value, 
			   Currency_Value.CurrencyID 
			   FROM Currency_Value WHERE id = '$this->id'";
			   
		$rs = $ari->db->Execute($sql);
			
		$ari->db->SetFetchMode($savem);
		if (!$rs || $rs->EOF) 
		{	return false;	}
		
		if (!$rs->EOF) 
		{
			$this->value = $rs->fields['Value'];
			//@todo crear un objeto date/time
			$this->date = $rs->fields['Date'];
			$this->currency = new currency_currency($rs->fields['CurrencyID']);
		}
		$rs->Close();
		return true;
	
 	}//end function
 	
	/** Stores/Updates currency value object in the DB */	
	public function store ()
	{
		global $ari;

		$flagStore = true;
		
		//@todo: falta validar pto y coma segun config del idioma y guardar segun acepta MySQL
		if (!OOB_numeric :: isValid($this->value) )
		{	
			$flagStore = false;
			$ari->error->addError ("currency_value", "INVALID_VALUE");	
		}			

 		$this->value = OOB_numeric :: formatMySQL($this->value);	
 		
		if ($flagStore)
		{
			$this->value = $ari->db->qMagic($this->value);
			$this->date = $ari->db->qMagic(date('Y-m-d H:i:s'));
			$currency_id = $ari->db->qMagic($this->currency->get('id'));

			if ($this->id > ID_MINIMAL)
			{
				// update data
				$ari->db->StartTrans();
				$sql= "UPDATE Currency_Value
					   SET Currency_Value.Date = $this->date,
					   	   Currency_Value.Value = $this->value,
					   	   Currency_Value.CurrencyID = $currency_id
					   WHERE ID = '$this->id'";
				$ari->db->Execute($sql);
					
			
				if (!$ari->db->CompleteTrans())
				{	throw new OOB_exception("Error en DB: $ari->db->ErrorMsg()", "010", "Error en la Base de Datos", false);	} //return false;
				else
				{	$return = true;	}	
					
			} 
			else 
			{
				// insert new 
				$sql= "INSERT INTO Currency_Value
					   ( `Date`, `Value`, `CurrencyID`)
					   VALUES ( $this->date, $this->value, $currency_id )
						   	";
		   					
				$ari->db->StartTrans();
								
				$ari->db->Execute($sql);
				$this->id = $ari->db->Insert_ID();
			
				if (!$ari->db->CompleteTrans())
				{	throw new OOB_exception("Error en DB: $ari->db->ErrorMsg()", "010", "Error en la Base de Datos", false); 	}//return false;
				else
				{
					
					$return = true;
				}
			}//end if			
			return $return;
		} 
		else 
		{
			// no validan los datos
			 return false; //devuelve un objeto de error con los errores!
		}//end if
	}//end function

	/**  DEPRECIATED __ NO USAR __ !!! */	
	static public function getValue($value)
	{
		global $ari;
		
		if (!is_a($value, 'currency_value') )
		{	return false;	}
		
		//@todo => agregue esta linea, probar con lo demas
		$value_formateado = OOB_numeric::formatMySQL($value->get('value')); 
		
		if ( $lastValue = $value->get('currency')->getLastChange() )
		{	
			//var_dump($lastValue['value']);exit;
			//settype($value->get('value'),'float');
			$return =  $value_formateado * $lastValue['value'];	
		}
		else
		{	$return  = false;	} 
		
		//var_dump($return);exit;
		
		return  $return;
	}	
 	
 	/** Deletes currency_value object from the DB*/ 	
 	public function delete ()
 	{
		global $ari;
		
		$id = $ari->db->qMagic($this->get('id'));
		
		//para eliminar 
		$sql = "DELETE FROM Currency_Value 
				WHERE ID = $id ";
		
		$ari->db->StartTrans();

		$ari->db->Execute($sql);
	
		
		if ($ari->db->CompleteTrans())
		{	return true;	}
		else
		{	throw new OOB_exception("Error en DB: $ari->db->ErrorMsg()", "010", "Error en la Base de Datos", false);	}
					 		
 	}//end function


    /**
     * Compares two currency values
     *
     * @access public
     * @param object currency_value $currencyValue1 the first currency value
     * @param object currency_value $currencyValue2 the second currency value
     * @return int 0 if the currency values are equal, 
	 * 			  -1 if $currencyValue1 < $currencyValue2, 
	 * 			   1 if $currencyValue1 > $currencyValue2
     */
	 //@return int 0 if the dates are equal, -1 if d1 is before d2, 1 if d1 is after d2
	static public function compare($currencyValue1, $currencyValue2)
	{
		global $ari;
		
		if (!is_a($currencyValue1, 'currency_value') || 
			!is_a($currencyValue2, 'currency_value'))
		{	//return false;	
			throw new OOB_exception("Invalid Currency Object", "310", "Invalid Currency Object", false);
		}
		
/*		
		$value1 = OOB_numeric :: formatMySQL(currency_value :: getValue($currencyValue1));
		$value2 = OOB_numeric :: formatMySQL(currency_value :: getValue($currencyValue2));
		
		$value1 = OOB_numeric :: formatPrint(currency_value :: getValue($currencyValue1));
		$value2 = OOB_numeric :: formatPrint(currency_value :: getValue($currencyValue2));
*/		
		$value1 = (float) currency_value :: getValue($currencyValue1);
		$value2 = (float) currency_value :: getValue($currencyValue2);
		
		//echo "POST: "; var_dump($value1); echo " vs DB: "; var_dump($value2); echo "<br><br>";
		//exit;
		
		//returns!
		//@todo => no anda bien cuando compara 2 montos en el mismo idioma y son iguales!!
        if ($value1 < $value2) return -1;
        if ($value1 > $value2) return 1;
        return 0;
		
	}//end function	
 	
 	public function id(){
 		return $this->id;
 	}
 	
 	static public function precio_saved($currency, $value)
 	{
 			
		if (!is_a($currency,'currency_currency'))
		{
			$currency	= new currency_currency($currency);
		}

		$precio	= new currency_value();
		$precio->set('value',$value);
		$precio->set('currency',$currency);


		if(!$precio->store())
		{
			return false;
		}
		
		return $precio;
 		
 	}
	
	/* devuelve el valor original para imprimir segun comas y puntos de la configuración regional */
	public function getFormatedValue()
	{
		return OOB_numeric::formatPrint($this->value);
	}
 	
	/* este metodo no es publico, pero bueno, está acá por compatibilidad con oob_model_type */
	static public function __SQLsearchRemote($field, $comparison, $value, $connector, $type, $remote_class, $remote_attribute = false, $previous_join = false)
	{
		global $ari;
		$table = 'currency_value';
		$sql_data = $sql_join = array();
		
		$operadores = array();
		$operadores["eq"] = "=";
		$operadores["lt"] = "<";
		$operadores["gt"] = ">";
		
		if ($previous_join == false)
		{
			$remote_table = $remote_class::getTable();
		}
		else
		{
			$remote_table = $previous_join;
		}
		
		$join_name = $table. '_id';
		$sql_join[] = 'JOIN ' . $table . ' as '.$join_name.' ON (' . $join_name. '.id = ' .  $remote_table . '.' . $remote_attribute . ')';
		
		$operador_inicio = $operadores[$comparison];
		$operador_fin = "";	
				
		$sql_data[] = ' ' . $connector . ' ' . $join_name.'.value ' . $operador_inicio  . $value . $operador_fin;
			
		return array(
					'data' => $sql_data,
					'join' => $sql_join
					);
		
	}
	
	/*este metodo devuelve el valor con formato moneda, le concatena el simbolo de la moneda antes del valor*/
	/* si $negative es true se forza a que el numero salga en negativo */
	public function getPrintable( $negative = false )
	{
		return $this->Printable( $this->value, $this->get('currency'), $negative );		
	}//end function
	
	
	/*este metodo devuelve el valor con formato moneda, le concatena el simbolo de la moneda que se le pasa*/
	/* si $negative es true se forza a que el numero salga en negativo */
	public function Printable( $value, $currency = false , $negative = false )
	{
	
		global $ari;
		
		$separador = trim( $ari->locale->get('decimal', 'numbers') );
		
		if($currency){
			$moneda = $currency;
		}
		else
		{
			$moneda = currency_currency::getDefault();
		}		
		
		return $moneda->get('sign') . " " . (($negative)?'-':'') . number_format(  strval( $value ), 2, $separador,"" )  ;		
	
	}//end function
	
	
 }//end class
	
	
 
?>
