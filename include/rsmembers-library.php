<?php
/**
 * RS-members is wordpress most powerful membership plugin many many features are include there.
 *
 * @link       http://www.themexpo.net
 *
 * @package    rs-members
 */
class RsMembersLibrary
{
	private static $_instance = NULL;

	private $plugin = NULL;
	
	/*=== Validation  ====================*/
	private $_errors = array();

	protected $_error_messages = array();

	protected $_custom_callback = NULL;
	protected $_custom_error = NULL;

	public $options = array();
	public $type = NULL;
	public $param = NULL;

	private $_field = NULL;
	public $errorfound = 0;
	/*===  ==  ====================*/
	

	private function __construct($plugin)
	{
		$this->plugin = $plugin;
		// Set message per type
		$this->_error_messages = array(
			'required' => __('This field is required.', 'rsmembers'),
			'numeric' => __('This field must be a number.', 'rsmembers'),			
			'email' => __('This field must be an email address.', 'rsmembers'),
			'alphanumeric' => __('This field only accepts alphanumeric characters.', 'rsmembers'),
			'alpha' => __('This field only accepts alpha letters.', 'rsmembers'),
			'name' => __('This field only accepts alpha letters, spaces, dashes(-), and apostrophes(\').', 'rsmembers'),
			'past' => __('Please enter a date in the past.', 'rsmembers'),
			'maxlen' => __('This field is too long, should be no more than %d characters.', 'rsmembers'),
			'minlen' => __('This field is too short, should be at least %d characters.', 'rsmembers'),
			'website' => __('This field must be a valid website.', 'rsmembers'),
			'date' => __('This field must be a valid date.', 'rsmembers'),
			'positive' => __('This field must be positive.', 'rsmembers'),
			'int' => __('This field must be int value.', 'rsmembers'),
			'maxval' => __('This field value should be no more than %d.', 'rsmembers'),
			'minval' => __('This field value should be at least %d.', 'rsmembers'),
			'password' => __('The password should be at least %d characters.', 'rsmembers'),
			'custom' => '%s',
			'regex' => __('Failed regular expression %s', 'rsmembers'),
			'unknown' => __('Unrecognized validation rule: "%s"', 'rsmembers'),
			'userid' => __('Only use numbers and letters.', 'rsmembers'),
			'userexist' => __('Sorry, that username is taken, please try another.', 'rsmembers'),
			'emailexist' => __('Sorry, that email address already has an account.<br />Please try another.', 'rsmembers'),
			'passwordmatch' => __('Passwords did not match.', 'rsmembers'),
			
		);		
	}

	/**
	 * Returns the singleton instance for this class
	 * @param Object $plugin The parent plugin's instance
	 * @return Object The single instance to the SlugPublic class
	 */
	public static function get_instance($plugin)
	{
		if (NULL === self::$_instance)
			self::$_instance = new self($plugin);
		return (self::$_instance);
	}
	/**
	 * @return form control
	 */
	function formcontrol( $formid, $name, $type, $value, $selectvalue, $postvalue, $rules, $posttype=0 ){
				
		switch( $type ) {
	
			case "checkbox":
				$selected = ( $value == 'on' ) ? 'checked="checked"' : '';				
				$str = "<input name=\"$name\" type=\"$type\" id=\"$formid\" " . $selected . " class='cmn-toggle cmn-toggle-round'  /><label for=\"$formid\"></label>";				
				if($posttype==1)
					$str .= $this->validate($name, $value, $rules);
				break;
		
			case "text":
				$class = 'text-control';
				$value =  ( !empty($postvalue) ) ? $postvalue : $value;
				$str = "<input name=\"$name\" type=\"$type\" id=\"$formid\" value=\"$value\" class=\"$class\" />";
				if($posttype==1)
					$str .= $this->validate($name, $value, $rules);
				break;
		
			case "textarea":
				$value =  ( !empty($postvalue) ) ? $postvalue : $value;
				$value = stripslashes( esc_textarea( $value ) );
				$class = "textarea"; 
				$str = "<textarea cols=\"20\" rows=\"5\" name=\"$name\" id=\"$formid\" class=\"$class\">$value</textarea>";
				if($posttype==1)
					$str .= $this->validate($name, $value, $rules);
				break;
		
			case "password":
				$value =  ( !empty($postvalue) ) ? $postvalue : $value;
				$class = 'text-control';
				$str = "<input name=\"$name\" type=\"$type\" id=\"$formid\" value=\"$value\"  class=\"$class\" />";
				if($posttype==1)
					$str .= $this->validate($name, $value, $rules);
				break;
		
			case "hidden":
				$value =  ( !empty($postvalue) ) ? $postvalue : $value;
				$str = "<input name=\"$name\" type=\"$type\" value=\"$value\" />";
				if($posttype==1)
					$str .= $this->validate($name, $value, $rules);
				break;
		
			case "option":
				$str = "<option value=\"$value\" " . $this->checkbox_selected( $valtochk, $value ) . " >$name</option>";
				if($posttype==1)
					$str .= $this->validate($name, $value, $rules);
				break;
		
			case "select":
				$class = "select-control";
				$str = "<select name=\"$name\" id=\"$formid\" class=\"$class\">\n";
				$selectsvalue = explode( '|', $selectvalue );			
				foreach( $selectsvalue as $option ) {
					$optionsvalue = explode( ',', $option );
					$str = $str . "<option value=\"$optionsvalue[1]\"" . $this->checkbox_selected( $optionsvalue[1], $value ) . ">" . __( $optionsvalue[0], 'wpsuperuser' ) . "</option>\n";
				}
				$str = $str . "</select>";
				if($posttype==1)
					$str .= $this->validate($name, $value, $rules);
				break;	
		}
		
		return $str;
	}
	
	function checkbox_selected( $value, $chkvalue )
	{
		$issame = ( $value == $chkvalue ) ? ' selected' : '';
		return $issame;
	}
	
	function fields_link_edit( $field_id ) {
		return '<a href="' . get_admin_url() . 'options-general.php?page=wpmem-settings&amp;tab=fields&amp;edit=' . $field_id . '">' . __( 'Edit' ) . '</a>';
	}

	
	
	
	




	/**
	 * Validate value based on type
	 * @param  mixed $value The value to be validated
	 * @param  array $rules An array containing the validation rules to check against
	 * @param  array $field The array for the current form field
	 * @return boolean TRUE if the data is valid according to all the rules; otherwise FALSE
	 */
	public function validate($name, $value, $rules = array())
	{
		
		$results = '';
		$param = 0;

		foreach ($rules as $rule) {
			
			if (FALSE !== strpos($rule, ':'))
				list($type, $param) = explode(':', $rule, 2);
			else
				$type = $rule;
			
			
			switch ($type)
			{
			case 'passwordmatch':
				$comp = str_replace('_confirm', '', $name); 
				if (!empty($comp) &&  $value!=$_POST[$comp] )				
					$results .= $this->_add_message($type);
				break;
			
			case 'emailexist':
				if ( email_exists($value) )
					$results .= $this->_add_message($type);
				break;
			
			case 'userexist':
				if ( username_exists( $value ) )
					$results .= $this->_add_message($type);
				break;
			
			case 'userid':
				if(!preg_match("/^[a-zA-Z0-9]+$/", $value))
					$results .= $this->_add_message($type);
				break;
			
			case 'positive':
				if ($value < 0)
					$results .= $this->_add_message($type);
				break;

			case 'int':
				if (!ctype_digit($value))
					$results .= $this->_add_message($type);
				break;

			case 'required':
				if ('' === trim($value))
					$results .= $this->_add_message($type);
				break;
			
			case 'numeric':
				if (!is_numeric($value))
					$results .= $this->_add_message($type);
				break;

			case 'email':
				if (!is_email($value))
					$results .= $this->_add_message($type);
				break;

			case 'alphanumeric':
				$comp = str_replace('_', '', $value);
//				return (empty($comp) ? TRUE : ctype_alnum($comp));
				if (!empty($comp) && !ctype_alnum($comp))
					$results .= $this->_add_message($type);
				break;

			case 'alpha':
				$comp = str_replace(' ', '', $value); // allow spaces
//				return (empty($comp) ? TRUE : ctype_alpha($comp));
				if (!empty($comp) && !ctype_alnum($comp))
					$results .= $this->_add_message($type);
				break;

			case 'name':
				$comp = str_replace(array(' ', '-', '\''), '', $value); // allow spaces, dash and apostrophe
//				return (empty($comp) ? TRUE : ctype_alpha($comp));
				if (!empty($comp) && !ctype_alnum($comp))
					$results .= $this->_add_message($type);
				break;

			case 'maxlen':
				 if (strlen($value) > intval($param))
					 $results .= $this->_add_message($type, intval($param));
				 break;

			case 'minlen':
				if (strlen($value) < intval($param))
					$results .= $this->_add_message($type, intval($param));
				break;

			case 'maxval':
				if ($value > $param)
					$results .= $this->_add_message($type, $param);
				break;

			case 'minval':
				if ($value < $param)
					$results .= $this->_add_message($type, $param);
				break;

			case 'regex':
				if (!preg_match($param, $value)) {
					if (isset($field['error']))
						$results .= $this->_add_message('custom', $field['error']);
					else
						$results .= $this->_add_message($type, $param);
				}
				break;

			case 'past':
				if (strtotime($value) >= time())
					$results .= $this->_add_message($type);
				break;

			case 'website':
				$v = trim($value);
				if (!empty($v)) {		// accept empty values
					if (FALSE === strpos($value, '://'))
						$value = 'http://' . $value;

					if (FALSE === filter_var($value, FILTER_VALIDATE_URL))
						$results .= $this->_add_message($type);
				}
				break;

			case 'date':
//				$d = new DateTime($value);
//				return ($d && $d->ToString('Y-m-d') == $value);
				$comp = strtotime($value);
				if (0 === $comp)
					$results .= $this->_add_message($type);
				break;

			case 'password':
				$comp = trim($value);
				if (!empty($v) && strlen($comp) > intval($param))
					$results .= $this->_add_message($type, intval($param));
				break;

			case 'custom':
				if (NULL !== $this->_custom_callback && NULL !== $this->_custom_error &&
					call_user_func_array($this->_custom_callback, array($value)))
					$results .= $this->_add_message($type, $this->_custom_error);
				break;

			case 'striphtml':
				$value = strip_tags($value);
				break;

			/*default:
				$results .= $this->_add_message('unknown', $type);
				break;*/
			}
		}

		return ($results);
	}

	/**
	 * Sets up the callback to perform custom validation actions
	 * @param callback $callback The callback function used to validate the data
	 * @param string $error_msg The error message for failed validations
	 */
	public function set_custom_validation($callback, $error_msg)
	{
		$this->_custom_callback = $callback;
		$this->_custom_error = $error_msg;
	}

	/**
	 * Adds a message to this list of validation exceptions
	 * @param string $type The validation rule name to display the corresponding validation error for
	 * @param int $param The parameter value to display within the error message or NULL
	 * @return boolean Always returns a boolean FALSE
	 */
	private function _add_message($type, $param = NULL)
	{				
		if (NULL === $param)
			$msg = $this->_error_messages[$type];
		else
			$msg = sprintf($this->_error_messages[$type], $param);
		$this->errorfound = 1;		
		return ('<div class="control-error">'.$msg.'</div>');
	}

	/**
	 * Return error messages from validation
	 * @return array
	 */
	public function get_errors()
	{
		return ($this->_errors);
	}
	
	public function get_found_error()
	{
		return ($this->errorfound);
	}

}	//End Class

// EOF