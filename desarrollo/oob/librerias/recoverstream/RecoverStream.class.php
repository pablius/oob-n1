<?php 
/**
 OOB/N1 Framework [©2004, 2006 - Nutus]
 @license: BSD
 @author: Pablo Micolini / Nutus 2005 (adapted to OOB) // Original CopyRight Holder missing
 Recoverable Stream ABSTRACT
*/

define('RANGE_PCRE', '/bytes=(\d*)-(\d*)/si');

abstract class RecoverStream {

	public $ErrNo      = -1;
	public $ErrStr     = '';
	private $BufferSize = -1;

	public function __construct() {
		$this->__constructor();
	}

	public function __constructor() {
		$_SERVER['HTTP_RANGE'] = isset($_SERVER['HTTP_RANGE']) ? $_SERVER['HTTP_RANGE'] : '';
		$this->BufferSize = 512 * 1024; // 512Kb
	}
	
	public function Error(&$errno, &$errstr) {
		$errno  = $this->ErrNo;
		$errstr = $this->ErrStr;
		return ($this->ErrNo !== -1);
	}
	
	public function HandleAndExit() {
		if ($this->Handle()) {
			exit();
		}
		return false;
	}
	
	public function Handle() {

		$this->ErrNo  = -1;
		$this->ErrStr = '';

		$offset = 0;
		$length = 0;
		
		if (preg_match(RANGE_PCRE, $_SERVER['HTTP_RANGE'], $matches)) {
			if (($matches[1] !== '') && ($matches[2] !== '')) {
				$offset = $matches[1];
				$length = $matches[2] + 1;
			} elseif ($matches[1] !== '') {
				$offset = $matches[1];
				$length   = $this->ContentLength() - $matches[1];
			} else { // if ($matches[2] !== '') {
				$offset = $this->ContentLength() - $matches[2];
				$length   = $matches[2]; 
			}
		} else {
			$offset = 0;
			$length = $this->ContentLength();
		}
		
		if ($this->Open()) {
			if ($this->Seek($offset)) {

				$headers = $this->AdditionalHeaders();
				foreach ($headers as $header) {
					if (is_array($header) && isset($header['header']) && isset($header['replace'])) {
						header($header['header'], $header['replace']);
					} else {
						header($header);
					}
				}
				header('Content-Type: '   . $this->ContentType());
				header('Content-Length: ' . $length);

				while ($length > $this->BufferSize) {
					$content = $this->Read($this->BufferSize);
					if ($content !== false) {
						echo($content);
					} else {
						return false;
					}
					$length = $length - $this->BufferSize;					
				}

				$content = $this->Read($length);
				if ($content !== false) {
					echo($content);
				} else {
					return false;
				}

				return $this->Close();
			}
		}
		return false;
	}
	
	// abstract
	 abstract public function AdditionalHeaders();

	// abstract
	abstract public function Open();
	
	// abstract
	abstract public function Close();

	// abstract
	 abstract public function Seek($offset);

	// abstract
	abstract public function Read($length);
	
	// abstract
	 abstract public function ContentType();

	// abstract
	abstract public function ContentLength();
	
}

?>