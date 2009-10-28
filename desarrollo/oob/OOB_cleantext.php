<?php
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
 This class provides "cleaning" functionality for text/html fields.
Example:
| <?
| 	$ct = new OOB_cleantext();
| 	$text = '<a href="http://www.yahoo.com">http://www.yahoo.com/</a>'
| 			.'<br><b>Yahoo Server</b><br><i>Search Engine</i>';
| 	$allowed_tags = array('<br>','<a>');
| 	echo $ct->dropHTML($text,$allowed_tags);
| 	echo $ct->shortText($text,10);															
| ?>
@license BSD
 */
class OOB_cleantext {
		
	private $dropHTML;
	private $activateLinks;
	public $tags = "<a><br><b><h1><h2><h3><h4><h5><h6><i><img><li><ol><p><strong><table><tr><td><th><u><ul><span><div><em><font><hr><pre><style><sub><sup>";
	public $simpletags = "<b><br><p><span><strong><strike><italic><i><style><u><em>";
	
	public function __construct ($tags = null)
	{
		if ($tags != null)
		$this->tags= $tags;
	}
	
	/**
	* this function will activate all functions 
	* in this class except of shortText function
	*/
	public function cleanAll($str){
		$str = $this->dropHTML($str,$this->tags);
		$str = $this->activateLinks($str);
		return $str;
	}

	/**
	* Takes a string, and does the reverse of the PHP 
	* standard function htmlspecialchars().
	*/
	public function undo_htmlspecialchars($string) {
		$string = preg_replace("/&gt;/i", ">", $string);
		$string = preg_replace("/&lt;/i", "<", $string);
		$string = preg_replace("/&quot;/i", "\"", $string);
		$string = preg_replace("/&amp;/i", "&", $string);
		return $string;
	}

	/**
	* this function will activate all links and 
	* email addresses with <a> tag
	*/
	public function activateLinks($str) {
		$str = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)', '<a href="\\1" target="_blank">\\1</a>', $str); 
		$str = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)', '\\1<a href="http://\\2" target="_blank">\\2</a>', $str); 
		$str = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})','<a href="mailto:\\1">\\1</a>', $str); 
		return $str;
	}
	
	/**
	* this function will drop HTML tags except of
	* of tag in $allowed_tags varible
	* can be also array of tags
	*/
	public function dropHTML($str,$allowed_tags = NULL){
		// @todo : falta que saque los eventos de javascript que puedan existir
		if ($allowed_tags == NULL)
			$allowed_tags = $this->tags;
		
		if(is_array($allowed_tags))
		{
			foreach($allowed_tags as $key => $value)
				{$this->tags .= $value;}
				
			return strip_tags($str,$this->tags);
		}
		else
		{
			return strip_tags($str,$this->tags);
		}
	}
	
	/**
	* This function shortens text into $length at most. 
	* If the text is longer, puts an ellipsis at the end.
	*/
	public function shortText($str,$length){
		return strlen($str) > $length ? preg_replace('/\s\S*$/','...',substr($str,0,$length - 3)) : $str;
	}
	
/** Removes style and class attributes from a string */
public function removeAttributes($str)
{
       $stripAttrib = "' (style|class)=\"(.*?)\"'i";
       $str = stripslashes($str);
       $str = preg_replace($stripAttrib, '', $str);
       return $str;
}

public function cleanString($str) {
    return ereg_replace("[^[:alnum:]+]","",$str);
}

}
?>