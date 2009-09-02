<?php 
/**
 OOB/N1 Framework [©2004, 2006 - Nutus]
 @license: BSD
 @author: Pablo Micolini / Nutus 2005 (adapted to OOB) // Original CopyRight Holder missing
 Recoverable Stream for FILES
*/

require_once(dirname(__FILE__) . '/RecoverStream.class.php');

class RecoverFileStream extends RecoverStream {

	public $FileName    = '';
	public $ContentType = '';	
	private $fh          = null;

	public function __construct() {
		$this->__constructor();
	} 
	
	public function __constructor() {
		parent::__constructor();
	}
	
	public function Open() {
		$this->fh = @fopen($this->FileName, 'r');
		if ($this->fh !== false) {
			return true;
		}
		return false;
	}
	
	public function Close() {
		return fclose($this->fh);
	}

	public function Seek($offset) {
		return fseek($this->fh, $offset, SEEK_SET) == 0;
	}

	public function Read($length) {
		return fread($this->fh, $length);
	}
	
	public function ContentType() {
		return $this->ContentType;
	}

	public function ContentLength() {
		return filesize($this->FileName);
	}
	
	public function AdditionalHeaders() {
		return array('Content-Disposition: attachment; filename="' . $this->FileName . '"');
	}

}

?>