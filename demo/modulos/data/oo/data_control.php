<?php
# Eumafes v2 [�2005 - Nutus, Todos los derechos reservados]
/*
 * Created on 18-ago-2005
 * @author Victor Stornati (victor.stornati@nutus.com.ar)
 */
 
 class data_control
 {
 	private $name = ''; //nombre del control
 	private $value = ''; // valor q contiene el control
 						 //en el caso de un link especifica lo q dice el link en la pantalla	
 	private $id = '';
 	private $size = ''; 
 	private $type = ''; //tipo de input para dichos controles pero para fecha dice el formato q tendra las misma
 	private $disabled = '';
 	private $maxlength = '';
 	private $checked = '';
 	private $class = '';
 	private $cols = ''; //para textarea catidad de columnas 
 						//para opciones (lista de optionButtons o checkbuttons) dice cuantas opciones entran en una fila 
 	private $rows = ''; //
 	private $wrap = '';
 	private $readonly = '';
 	private $textYes = ''; // texto q aparece en la opcion SI
 	private $textNo= '';   // texto q aparece en la opcion NO	
 	private $optionsList = false; //array asociativo q tiene las opciones
 	private $multiple = '';
 	private $href='';
 	private $module = '';
	private $simple = false;
 	
 	private $otherControl = '';

 	//se puede especificar en class
 	//private $height = '';
 	//private $width = '';
 	
 	
 	//ejemplo de atrributes['attributo']=>valor
 	//$attributes['name'] = 'nombre control'
 	//$attributes['checked'] = 'checked'
	function __construct($attributes = false, $type = ID_UNDEFINED)
	{ 
		if (is_array($attributes))
      	{
      		$i = 0;
      		$array_keys = array_keys($attributes); 
      		
			foreach($attributes as $a)
			{
				if (data_control :: isAttributeOfType($array_keys[$i], $type))
				{	//var_Dump($this);echo "<br><br>";
					$this->$array_keys[$i] = $attributes[$array_keys[$i]];	
				}
				$i++;
			}//foreach
    	} //end if
		
   	} //end function
 	
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
 	
 	static public function isAttributeOfType($attribute, $type)
 	{
 		global $ari;
		
		if (OOB_validatetext :: isCorrectLength ($attribute, 1, MAX_LENGTH) )
		{
			$attribute = $ari->db->qMagic($attribute);
			$type = $ari->db->qMagic($type);
			$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
			$sql = "SELECT True 
			        FROM Data_Attribute, Data_TypeAttribute  
	                WHERE Data_TypeAttribute.AttributeID = Data_Attribute.ID
	                AND Data_Attribute.Name = $attribute
	                AND Data_TypeAttribute.TypeID = $type
	                "; 
			    //echo $sql;echo "<br><br>";     
			$rs = $ari->db->Execute($sql);
			$i = 0;
			$ari->db->SetFetchMode($savem);
			if ($rs && !$rs->EOF) 
			{	$return = true;		} 
			else
			{	$return = false;	}
			$rs->Close();
	
			return $return;
		}		 		
 	}//end function
 	
 	/**	*/
 	public function display($type)
 	{
 		$return = "";
 		//todo: falta validar si tagBegin y tagEnd son HTML permitidos
 		
 		switch ($type)
 		{
			case CONTROL_STRING:
			case CONTROL_NUMBER:
			case CONTROL_EMAIL:
			{
				$return = $this->printTextBox();
				break;	
			}
			
			case CONTROL_BOOL:
			{
				$this->type = "radio";
				$return = $this->printOptionsList();
				break;				
			}
 			case CONTROL_CHECK:		
 			case CONTROL_OPTION:				
			{
				$return = $this->printOptionsList();
				break;	
			}					
							
			case CONTROL_DATE:
			{
				$return = $this->printDate();
				break;	
			}					

			case CONTROL_TIME:
			{
				$return = $this->printTime();
				break;	
			}
														 					
 			case CONTROL_IMAGE:
 			case CONTROL_FILE:
			{
				$return = $this->printFile();
				break;	
			}										
										
			case CONTROL_AREA:
			{
				$return = $this->printTextArea();
				break;	
			}
			
			case CONTROL_EMPLOYEE:
			{
				$return = $this->printEmployee();
				break;	
			}	
			
			case CONTROL_CURRENCY:
			{
				$return = $this->printCurrency();
				break;	
			}
			
			case CONTROL_CALIFICATION:
			{
				$this->type = "radio";				
				$return = $this->printOptionsList();	
				break;
			}				

			case CONTROL_COMPETITION:
			{
				$return = $this->printCompetition();	
				break;
			}				
						
 		}//end switch
 		
		return $return;
 	}//end function
 	 	
 	/**	*/
 	public function printTextArea()
 	{	global $ari;
 		$class = $this->class;
		require_once $ari->t->_get_plugin_filepath('function','editor');
 		$attributesText = array('InstanceName'=> $this->name,
                            'Width'=> $this->cols,
                            'Height'=> $this->rows,
							 'simple'=> $this->simple,
                            'Value'=> $this->value);
							
		
		return smarty_function_editor ($attributesText, $ari->t); 
//		return "<textarea class='$class' name='$this->name' id='$this->id' cols='$this->cols' rows='$this->rows' wrap='$this->wrap' $this->readonly $this->disabled>$this->value</textarea>"; 
 	}//end function 
 	
 	/**	*/
 	public function printTextBox()
 	{
 		$class = $this->class;
 		return "<input type='$this->type' class='$class' id='$this->id' name='$this->name' size='$this->size' value='$this->value' maxlength='$this->maxlength' $this->disabled>"; 
 	}//end function
 	
 	/**	*/
 	public function printDate()
 	{
 		global $ari;
 		require_once $ari->t->_get_plugin_filepath('function','html_select_date');
 		
 		$class = $this->class;
 		
 		$checked = '';
 		$disabled = '';	
		if ($this->value['check'] == '1')
		{	
			$checked = 'checked';
			$disabled = 'disabled';	
		}
		/* 	
 		$attributes = array('prefix'=> $this->name,
                            'time'=> $this->value['date'],
                            'field_order'=> $this->type,
                            'year_as_text'=>true,
                            'all_extra' => $disabled);
		*/	
 		$attributes = array('prefix'=> $this->name,
                            'time'=> $this->value['date'],
                            'field_order'=> $this->type,
                            'year_as_text'=>true,
							'day_extra' => "id='" . $this->name . "Day'",
							'month_extra' => "id='" . $this->name . "Month'", 
							'year_extra' => "id='" . $this->name . "Year'",
                            'all_extra' => $disabled);
		
	
        $name_check = $this->name . 'Check';                    
 		$return = smarty_function_html_select_date ($attributes, $ari->t); 
		$return .=  "<input type='checkbox' class='$class' id='$name_check' name='$name_check' value='1' $checked onclick=enabledDate('$this->name')>Sin especificar";

		return $return;
		
 	}//end function
 	
 	/**	*/
 	public function printOptionsList()
 	{
 		$class = $this->class;
 		if ( is_array($this->optionsList) )
 		{
 			$return = "<table width='100%'>\n";
 			$return .= "<tr>\n";
 			$i = 1;
 			foreach($this->optionsList as $option)
 			{ 
 				$return .= "<td>\n";
 				$return .= "<input type='$this->type' class='$class' id='$this->id' name='$this->name' value='". $option['id'] ."' ".$option['checked'].">".$option['name']."\n";
 				$return .= "</td>\n";
 				if ($i % $this->cols == 0)
 				{	$return .= "</tr><tr>";	}
 				$i++;		
 			}	
 			$return .= "</tr></table>";
 		}
		
 		return $return;  		
 	}//end function
 
  	/**	*/
	public function printTime()
	{
 		global $ari;
 		$function = "function";
 		
 		require_once $ari->t->_get_plugin_filepath($function,'html_select_time');
 		$class = $this->class;
 		$checked = '';
 		$disabled = '';	
		if ($this->value['check'] == '1')
		{	
			$checked = 'checked';
			$disabled = 'disabled';	
		} 		
 		
 		/*
		$attributes = array('prefix'=> $this->name,
                            'time'=> $this->value['time'],
                            'all_extra' => $disabled
                            ); 
		*/
		$attributes = array('prefix'=> $this->name,
                            'time'=> $this->value['time'],
							'hour_extra' => "id='" . $this->name . "Hour'",
							'minute_extra' => "id='" . $this->name . "Minute'", 
							'second_extra' => "id='" . $this->name . "Second'",
                            'all_extra' => $disabled
                            ); 
        
        $name_check = $this->name . 'Check';
 		$return = smarty_function_html_select_time ($attributes, $ari->t);
 		$return .=  "<input type='checkbox' class='$class' id='$name_check' name='$name_check' $checked value='1' onclick=enabledTime('$this->name')>Sin especificar";
 		return $return;
	}
	
  	/**	*/
	public function printFile()
	{
		$class = $this->class;
		$img_name = $this->name ."image";
		$file_name = $this->name ."file";
		$return = "";
		$return .= "<input type='image' class='$class' id='$this->id' name='$img_name' value='$this->value' onclick='return false;'>";
		$return .= "<input type='file' class='$class' id='$this->id' name='$file_name' size='$this->size' value='$this->value' $this->disabled>";

		return $return;
	}	
 	
 	/**	*/
	public function printSelect()
	{
		//
	}
 	
 	/**	*/
	public function printEmployee()
	{		
		$class = $this->class;
				
		$html_select = "<select name='" . $this->name . "' id='" . $this->name . "' size='1'>";
		$html_insert = "<a href=javascript:insertServicio('" . $this->name . "','" . $this->module . "','" . $this->otherControl . "','no','0','') class='$class'>Asignar Empleado</a> ";
		
		$html_options = "<option value='-1' style='visibility:hidden;min-height:0px;height:0px;'>--- Seleccione el Área ---</option>";
		
		$indexs = array();
		$i = 1;
		foreach($this->optionsList as $option)
 		{
 			$attributes = "";
 			if (is_array($this->value['servicio']))
 			{
	 			if (in_array((string)$option['id'],$this->value['servicio'] ))
	 			{	$attributes = "style='visibility:hidden;height:0px;height:0px;min-height:0px'";	}
 			}
 			$indexs[$option['id']] = $i;
	 		$html_options .= "<option value='". $option['id'] ."' ".$option['selected']." $attributes>".$option['name']."</option>";
	 		$i++;
 		}			
 		//var_dump(htmlspecialchars($html_options));
		$html_select .= $html_options . "</select>";
		
		$js = "";
		if (is_array($this->value))
		{
			if ( is_array($this->value['servicio']) && is_array($this->value['employee']) )
			{
				$js = "<script language='javascript' type='text/javascript'>";
				
				for ( $j=0; $j<count($this->value['servicio']); $j++)
				{
					$servicio = $indexs[$this->value['servicio'][$j]];
					$employee = $this->value['employee'][$j];
					$js .= "insertServicio('$this->name','$this->module','$this->otherControl','yes','$servicio','$employee'); ";
				}//end foreach
				$js .= "</script>";
			}//end if	
		}//end if
		
		$return = $html_select . " " . $html_insert . '<br>';
		$return .= "<table id='" . $this->name . "table'></table>";
		$return .= $js;
		  
		return $return;
	}

 	/**	*/
	public function printCurrency()
	{
		$class = $this->class;
		$name_input = $this->name . "input";
		$name_select = $this->name . "select";
		$html_input = "<input type='text' class='$class' id='$this->id' name='$name_input' size='$this->size' value='$this->value' maxlength='$this->maxlength' $this->disabled>";
		$html_select = "<select name='$name_select' id='$this->id' size='$this->size' $this->multiple>";
		$html_options = '';
		foreach($this->optionsList as $option)
 		{
 			$html_options .= "<option value='". $option['id'] ."' ".$option['selected'].">".$option['name']."</option>";
 		}			
		$html_select .= $html_options . "</select>";
		
		$return = "<table width='10%'><tr valign='top'><td valign='top' with='1%'>\n";
		$return.= $html_select . "</td><td valign='top' align='left' with='99%'>" . $html_input;  
		$return.= "</td></tr></table>";
		return $return;
		
	}

	public function printCompetition()
	{
		//var_dump($this); exit;
		$return = "<table width='100%'><tr><td>\n";
		$return.= "<select class='$this->class' id='$this->id' name='$this->name' size='$this->size'>\n";
		if (is_array($this->optionsList) && 
			isset($this->optionsList["options"]) && 
			isset($this->optionsList["selected"]))
		{
			foreach($this->optionsList["options"] as $keys => $values)
			{
				$return.= "<optgroup label=\"$keys\">\n";
				foreach ($values as $key => $value) 
				{	$return.= "<option label=\"$key\" value=\"$key\"";
					if ($key == $this->optionsList["selected"])
					{	$return.= " selected=\"selected\"";
					}
					$return.= ">$value</option>\n";
				}
				$return.= "</optgroup>\n";
			}
		}
		
		$return.= "</select></td></tr></table>";
 		return $return;  		
				
	}//end function

	
	/*
	public function printCompetition()
	{
		$return = "<table width='100%'><tr><td>\n";
		$return .= "<select class='$this->class' id='$this->id' name='$this->name' size='$this->size'>\n";
		if ( is_array($this->optionsList) )
 		{
 			foreach($this->optionsList as $option)
 			{	//$return .= "<option value='". $option['id'] ."' class ='". $option['class'] ."' ".$option['checked']." ".$option['disabled'].">".$option['name']."</option>\n";
				$return .= "<option value='". $option['id'] ."' class ='". $option['class'] ."' ".$option['selected']." ".$option['disabled'].">".$option['name']."</option>\n";
 			}	
 		}
		$return .= "</select></td></tr></table>";
 		return $return;  		

		//-- ejemplo de control competencia --
		//<select name="selectorComp" >
		//<option value="0" disabled class="disabledOption">Técnica</option>
		//<option value="1">--Reparacion Monitor</option>
		//<option value="2">--Reparacion CPU</option>
		//</select>
				
	}
	*/
 	
 }//end class
 
 
?>