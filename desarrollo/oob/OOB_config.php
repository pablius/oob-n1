<?PHP
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini, based on original work by BennyZaminga
#  @license BSD
######################################## 
*/

/** 
 @author BennyZaminga <bzaminga@web.de> (v0.03 - 11-08-2004)
 */

/**

This class handles reading/writing of ini files
Example:
--------
$Config = new Config( 'configuracion.ini', true, true);
$Config->set( 'var_name', 'var_attribute', 'section');
$name = $Config->get( 'var_name', 'section');
$Config->save();
*/
class OOB_config{
	private  $PATH             = null;
	private  $VARS             = array();
	private  $SYNCHRONIZE      = false;
	private  $PROCESS_SECTIONS = true;
	private  $PROTECTED_MODE   = true;
	

	private  $ERRORS           = array();

	/**
	* @desc   Constructor of this class.
	* @param  string $path Path to ini-file to load at startup.
	* NOTE:   If the ini-file can not be found, it will try to generate a 
	*         new empty one at the location indicated by path passed to 
	*         constructor-method of this class.
	* @param  bool $synchronize TRUE for constant synchronisation of memory and file (disabled by default).
	* @param  bool $process_sections TRUE or FALSE to enable or disable sections in your ini-file (enabled by default).
	* @return void Returns nothing, like any other constructor-method ��] .
	*/
	public function __construct( $path="", $synchronize=false, $process_sections=true){
		// check whether to enable processing-sections or not
		if ( isset( $process_sections)) $this->PROCESS_SECTIONS = $process_sections;
		// check whether to enable synchronisation or not
		if ( isset( $synchronize)) $this->SYNCHRONIZE = $synchronize;
		// if a path was passed and file exists, try to load it
		if ( $path!=null) {
			// set passed path as class-var
			$this->PATH = $path;
			if ( !is_file( $path)) {
				// conf-file seems not to exist, try to create an empty new one
				$fp_new = @fopen( $path, 'w', false);
				if ( !$fp_new) {
					$err = "OOB_config : Could not create new config-file('$path')";
					throw new OOB_exception($err, "000", "Ha ocurrido un error de ejecución", true);
				}else{
					fclose( $fp_new);
				}
			}else{
				// try to load and parse ini-file at specified path
				$loaded = $this->load( $path);
				if ( !$loaded) exit();
			}
		}
	}

	/**
	* @desc					  Retrieves the value for a given key.
	* @param  string $key     Key or name of directive to set in current config.
	* @param  string $section Name of section to set key/value-pair therein.
	* NOTE:                   Section must only be specified when sections are used in your ini-file.
	* @return mixed           Returns the value or NULL on failure.
	* NOTE:                   An empty directive will always return an empty string.
	*                         Only when directive can not be found, NULL is returned.
	*/
	public function get( $key=null, $section=null){
		// if section was passed, change the PROCESS_SECTION-switch (FIX: 11/08/2004 BennyZaminga)
		if ( $section) $this->PROCESS_SECTIONS = true;
		else           $this->PROCESS_SECTIONS = false;
		// get requested value
		if ( $this->PROCESS_SECTIONS) {
			if (!isset($this->VARS[$section][$key]))
			{$err = "OOB_config : The key: {$key} was not found on section: {$section}, file: {$this->PATH}";
					throw new OOB_exception($err, "000", "Ha ocurrido un error de ejecución", true);
			} else
			$value = $this->VARS[$section][$key];
		}else{
			$value = $this->VARS[$key];
		}
		// if value was not found (false), return NULL (FIX: 11/08/2004 BennyZaminga)
		if ( $value===false) {
			return null;
		}
		// return found value 
		return $value;
	}

	/**
	* @desc   Sets the value for a given key (in given section, if any specified).
	* @param  string $key     Key or name of directive to set in current config.
	* @param  mixed  $value   Value of directive to set in current config.
	* @param  string $section Name of section to set key/value-pair therein.
	* NOTE:   Section must only be specified when sections are enabled in your ini-file.
	* @return bool            Returns TRUE on success, FALSE on failure.
	*/
	public function set( $key, $value, $section=null){
		// when sections are enabled and user tries to genarate non-sectioned vars, 
		// throw an error, this is definitely not allowed.
		if ( $this->PROCESS_SECTIONS and !$section) {
			$err = "Config::set() - Passed no section when in section-mode, nothing was set.";
			throw new OOB_exception($err, "000", "Ha ocurrido un error de ejecución", true);
			return false;
		}
		// check if section was passed
		if ( $section===true) $this->PROCESS_SECTIONS = true;
		// set key with given value in given section (if enabled)
		if ( $this->PROCESS_SECTIONS) {
			$this->VARS[$section][$key] = $value;
		}else{
			$this->VARS[$key]           = $value;
		}
		// synchronize memory with file when enabled
		if ( $this->SYNCHRONIZE) {
			$this->save();
		}
		return true;
	}
	
	/**
	 * @desc   Remove a directive (key and it's value) from current config.
	 * @param  string $key     Name of key to remove form current config.
	 * @param  string $section Optional name of section (if used).
	 * @return bool            Returns TRUE on success, FALSE on failure.
	 */
	public function removeKey( $key, $section=null){
		// check if section was passed and it's valid
		if ( $section!=null){
			if ( in_array( $section, array_keys( $this->VARS))==false){
				$err = "OOB_config::removeKey() - Could not find section('$section'), nothing was removed.";
				throw new OOB_exception($err, "000", "Ha ocurrido un error de ejecución", true);
				return false;
			}
			// look if given key exists in given section
			if ( in_array( $key, array_keys( $this->VARS[$section]))===false) {
				$err = "OOB_config::removeKey() - Could not find key('$key'), nothing was removed.";
				throw new OOB_exception($err, "000", "Ha ocurrido un error de ejecución", true);
				return false;
			}
			// remove key from section
			$pos = array_search( $key, array_keys( $this->VARS[$section]), true);
			array_splice( $this->VARS[$section], $pos, 1);
			return true;
		}else{
			// look if given key exists
			if ( in_array( $key, array_keys( $this->VARS))===false) {
				$err = "OOB_config::removeKey() - Could not find key('$key'), nothing was removed.";
				throw new OOB_exception($err, "000", "Ha ocurrido un error de ejecución", true);
				return false;
			}
			// remove key (sections disabled)
			$pos = array_search( $key, array_keys( $this->VARS), true);
			array_splice( $this->VARS, $pos, 1);
			// synchronisation-stuff
			if ( $this->SYNCHRONIZE) $this->save();
			// return
			return true;
		}
	}
	
	/**
	 * @desc   Remove entire section from current config.
	 * @param  string $section Name of section to remove.
	 * @return bool            Returns TRUE on success, FALSE on failure.
	 */
	public function removeSection( $section){
		// check if section exists
		if ( in_array( $section, array_keys( $this->VARS), true)===false) {
			$err = "OOB_config::removeSection() - Section('$section') could not be found, nothing removed.";
			throw new OOB_exception($err, "000", "Ha ocurrido un error de ejecución", true);
			return false;
		}
		// find position of $section in current config
		$pos = array_search( $section, array_keys( $this->VARS), true);
		// remove section from current config
		array_splice( $this->VARS, $pos, 1);
		// synchronisation-stuff
		if ( $this->SYNCHRONIZE) $this->save();
		// return
		return true;
	}

	/**
	* @desc   Loads and parses ini-file from filesystem.
	* @param  string $path Optional path to ini-file to load.
	* NOTE:   When not provided, path passed to constructor will be used.
	* @return bool Returns TRUE on success, FALSE on failure.
	*/
	private function load( $path=null){

			$path = $this->PATH;

		/* 
		 * PHP's own method is used for parsing the ini-file instead of own code. 
		 * It's robust enough ;-)
		 */
		$this->VARS = parse_ini_file( $path, $this->PROCESS_SECTIONS);
		return true;
	}

	/**
	* @desc   Writes ini-file to filesystem as file.
	* @param  string $path Optional path to write ini-file to.
	* NOTE:   When not provided, path passed to constructor will be used.
	* @return bool Returns TRUE on success, FALSE on failure.
	*/
	public function save(){
	 $path = $this->PATH;

		$content  = "";
		
		// PROTECTED_MODE-prefix
		if ( $this->PROTECTED_MODE) {
			$content .= "<?PHP\n; /*\n; -- BEGIN PROTECTED_MODE\n";
		}
		
		// config-header
		$content .= "; Archivo generado automaticamente por OOB_Config\n";
		$content .= "; No modifique este archivo a mano.\n";
		$content .= "; Modificado: ".date('d M Y H:i s')."\n\n";
		
		// check if there are sections to process
		if ( $this->PROCESS_SECTIONS) {
			foreach ( $this->VARS as $key=>$elem) {
				$content .= "[".$key."]\n";
				foreach ( $elem as $key2=>$elem2) {
					$content .= $key2." = \"".$elem2."\"\n";
				}
			}
		}else{
			foreach ( $this->VARS as $key=>$elem) {
				$content .= $key." = \"".$elem."\"\n";
			}
		}
		
		// add PROTECTED_MODE-ending
		if ( $this->PROTECTED_MODE) {
			$content .= "\n; -- END PROTECTED_MODE\n; */\n?>\n";	
		}

		// write to file
		if ( !$handle = @fopen( $path, 'w')) {
			$err = "OOB_config::save() - Could not open file('$path') for writing, error.";
			throw new OOB_exception($err, "000", "Ha ocurrido un error de ejecución", true);
			return false;
		}
		if ( !fwrite( $handle, $content)) {
			$err = "OOB_config::save() - Could not write to open file('$path'), error.";
			throw new OOB_exception($err, "000", "Ha ocurrido un error de ejecución", true);
			return false;
		}else{
			// push a message onto error-stack
			$err = "OOB_config::save() - Sucessfully saved to file('$path').";
			array_push( $this->ERRORS, $err);
		}
		fclose( $handle);
		return true;
	}
	
	/**
	 * @desc   Renders this Object as formatted string (TEXT or HTML).
	 * @param  string $output_type Type of desired output. Can be 'TEXT' or 'HTML'.
	 * @return string Returns a formatted string according to chosen output-type.
	 */
	public function toString(){

			// render object as TEXT
			$out  = "";
			ob_start();
			print_r( $this->VARS);
			$out .= ob_get_clean();
			return $out;
		
	}
	
	/**
	 * @desc                   Lists all keys.
	 * @param  string $section Optional section (needed only when using sections).
	 * @return array           Returns a numeric array containing the keys as string.
	 */
	public function listKeys( $section=null){
		// check if section was passed
		if ( $section!==null){
			// check if passed section exists
			$sections = $this->listSections();
			if ( in_array( $section, $sections)===false) {
				$err = "Config::listKeys() - Section('$section') could not be found.";
				throw new OOB_exception($err, "000", "Ha ocurrido un error de ejecución", true);
				return false;
			}
			// list all keys in given section
			$list = array();
			$all  = array_keys( $this->VARS[$section]);
			foreach ( $all as $possible_key){
				if ( !is_array( $this->VARS[$possible_key])) {
					array_push( $list, $possible_key);
				}
			}
			return $list;
		}else{
			// list all keys (section-less)
			return array_keys( $this->VARS);
		}
	}
	
	/**
	 * @desc   List all sections (if any).
	 * @param  void
	 * @return array Returns a numeric array with all section-names as stings therein.
	 */
	public function listSections(){
		$list = array();
		// separate sections from normal keys
		$all  = array_keys( $this->VARS);
		foreach ( $all as $possible_section){
			if ( is_array( $this->VARS[$possible_section])) {
				array_push( $list, $possible_section);
			}
		}
		return $list;
	}
}
?>