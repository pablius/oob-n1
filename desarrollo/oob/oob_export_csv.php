<?php

class oob_export_csv
{

private $columns;
private $csv;

	function _construct(){
		$this->columns_name = array();
		$this->columns_title = array();
	}//end construct
	
	public function add_column( $title, $name ){
		$this->columns[] = $name;
		$this->columns_title[] = $title;
	}//end function
	

	public function array_to_csv( $values ){	
		
		$header = "";
		$body = "";
		
		$header = implode( CHAR_CSVEXPORT, $this->columns_title );
		
		foreach( $values as $value ){
		
			$rows = array();
			foreach( $this->columns as $name ){					
				if( isset($value[$name]) ){			
					$rows[] = OOB_validatetext::cleanToExport($value[$name]);				
				}else{
					$rows[] = "";
				}	
			}

			$body.= implode( CHAR_CSVEXPORT, $rows );
			$body.= "\n";
			
					
		}//end each	
		
		$this->csv = $header . "\n". $body;
			 
		
		
	}//end function
	
	public function send( $name ){
	

	
		global $ari;
		require_once($ari->get('libsdir'). DIRECTORY_SEPARATOR . 'RecoverStream' .DIRECTORY_SEPARATOR. 'RecoverStringStream.class.php');

		
		$archivo = new RecoverStringStream();
		$archivo->FileName = $name . '.csv';
		$archivo->Content = $this->csv;		
		$archivo->ContentType =  "text/x-csv";
		$archivo->Handle();		
	}

}//end class


?>