<?php 
/**
 OOB/N1 Framework [©2004, 2006 - Nutus]
 @license: BSD
 @author: Pablo Micolini / Nutus 2005 (adapted to OOB) // Original CopyRight Holder missing
 Recoverable Stream for STRINGS
*/

require_once(dirname(__FILE__) . '/RecoverStream.class.php');

class RecoverStringStream extends RecoverStream {

	public $FileName    = '';
	public $Content     = '';
	public $ContentType = '';
	private $_offset     = 0;
	
	public function __construct() {
		$this->__constructor();
	} 
	
	public function __constructor() {
		parent::__constructor();
	}
	
	public function Open() {
		return true;
	}
	
	public function Close() {
		return false;
	}

	public function Seek($offset) {
		$this->_offset = $offset;
		return true;
	}

	public function Read($length) {
		$result = substr($this->Content, $this->_offset, $length);
		$this->_offset += $length;
		return $result;
	}
	
	public function ContentType() {
		return $this->ContentType;
	}

	public function ContentLength() {
		return strlen($this->Content);
	}
	
	public function AdditionalHeaders() {
		return array('Content-Disposition: attachment; filename="' . $this->FileName . '"');
	}

}

?>