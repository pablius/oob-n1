<?php
class Logger { // <<<1
	var $err_stack;
	function Logger () {
	}
	function log ($logstring) { // <<<2
		$this->err_stack[]=date("Y-m-d H:i")." :: ".$logstring;
	}
	
	function dump_errors () { // <<<2
		print("<div id=\"errorlog\">");
		if (is_array($this->err_stack)) {
			foreach ($this->err_stack as $e) {
				print($e."<br>");
			}
		} else {
			print("nothing on the error stack!");
		}
		print("</div>");
	}
	
	function clear_errors () { // <<<2
		$this->err_stack=array();
	}
	//>>>2
}
?>
