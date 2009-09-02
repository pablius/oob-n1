<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/

/**
 Provides safe-forms by checking that a form can't be sent twise avoiding duplicate data
 
 NOTA: No usar el método FormElements antes de Validar, ya que no funciona. // @optimize: se puede solucionar esto?
*/
class OOB_safepost {


	private $llave = array ();
	private $hayllave = false;
	private $form_name;


/** The form name must be provided to the constructor */	
	public function __construct ($form = 'form')
		{ global $ari;
		$this->form_name = $form;
		$ari->t->caching = 0; // dynamic content safety fix (else solo funciona desde la 2a vez)
		}

/** Start the safe form */	
	public function Iniciar() 
		{
			if (!$this->hayllave) 
				{
					$this->llave[0] = substr(md5(microtime()),0,10);
					$this->llave[1] = substr(md5(rand()),0,10);
					$_SESSION[$this->form_name .'_'.$this->llave[1]] = $this->llave[0];
				} 
			$this->hayllave = true;
			return $this->llave;
		} 

	
/** Provides the form code that must be passed to the output */
	public function FormElement() 
		{
			if (!$this->hayllave)
					$this->Iniciar();
			
			return  '<input name="' . $this->form_name . '-0" type="hidden" value="' . $this->llave[0] . '" />' .
					'<input name="' . $this->form_name . '-1" type="hidden" value="' . $this->llave[1] . '" />';
		} 

/** Validates the form, and returns false if it has been already posted */
	public function Validar() 
		{
			
			if (isset($_POST[$this->form_name . "-0"]) 
			&& isset($_POST[$this->form_name . "-1"]) 
			&& isset($_SESSION[$this->form_name . '_' .$_POST[$this->form_name . "-1"]]) 
			&& $_SESSION[$this->form_name . '_' .$_POST[$this->form_name . "-1"]] === $_POST[$this->form_name . "-0"])
				{
					unset ($_SESSION[$this->form_name . '_' .$_POST[$this->form_name . "-1"]]);
					return TRUE; 	
				}
				else
				{
					return FALSE;
				}
		} 
	

	

} 
?>
