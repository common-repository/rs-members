<?php
/*
	Plugin Name: RS Members
	Plugin URI: http://wordpress.org/plugins/rs-members/
	Description: RS-members is wordpress most powerful membership plugin many many features are include there.
	Version: 1.0.3
	Author: themexpo
	Author URI: http://www.themexpo.net/
	License: GPL3+
	Text Domain: rs-members
	*/
	
	/*
    Copyright Automattic and many other contributors.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 1 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.
*/

// start the user session for persisting user/login state during ajax, header redirect, and cross domain calls:
session_start();

class RsMembers{
	
	private static $_instance = NULL;

	const PLUGIN_NAME = 'RS Members';	// plugin's full name
	const PLUGIN_VERSION = '1.0.3';				// plugin version
	const PLUGIN_SLUG = 'rs-members';			// plugin slug name
	const PLUGIN_DOMAIN = 'rsmembers';			// the text domain used by the plugin
	

	private $dir_plugin = NULL;			// the directory where the plugin code is installed
	private $dir_include = NULL;		// the directory where the plugin include files are located
	private $dir_assets = NULL;			// the directory where the plugin assets are located
	private $url_assets = NULL;			// the URL to the plugin assets directory
	private $dir_rootplugin = NULL;		// the directory where the plugin assets are located
	
	public $library = NULL;				// the URL to the plugin assets directory
	public $haspaymentgetway = NULL;	// Has any payment getway addons installed
	public $rsmemberspaypal = NULL;		// Is paypal addons installed
	public $rsmembersmailchimp = NULL;	// Is mailchimp addons installed
	public $rsmembersaccountrenew = NULL;	// Is mailchimp addons installed
	
	// define the settings used by this plugin; this array will be used for registering settings, applying default values, and deleting them during uninstall:
	private $settings = array(
		'rs_show_login_messages' => 0,								// 0, 1
		'rs_login_redirect' => 'home_page',							// home_page, last_page, specific_page, admin_dashboard, profile_page, custom_url
		'rs_login_redirect_page' => 0,								// any whole number (wordpress page id)
		'rs_login_redirect_url' => '',								// any string (url)
		'rs_logout_redirect' => 'home_page',							// home_page, last_page, specific_page, admin_dashboard, profile_page, custom_url, default_handling
		'rs_logout_redirect_page' => 0,								// any whole number (wordpress page id)
		'rs_logout_redirect_url' => '',								// any string (url)
		'rs_logout_inactive_users' => 0,								// any whole number (minutes)
		'rs_hide_wordpress_login_form' => 0,							// 0, 1
		'rs_logo_links_to_site' => 0,									// 0, 1
		'rs_logo_image' => '',										// any string (image url)
		'rs_bg_image' => '',											// any string (image url)
		'rs_login_form_show_login_screen' => 'Login Screen',			// any string (name of a custom login form shortcode design)
		'rs_login_form_show_profile_page' => 'Profile Page',			// any string (name of a custom login form shortcode design)
		'rs_login_form_show_comments_section' => 'None',				// any string (name of a custom login form shortcode design)
		'rs_login_form_designs' => array(								// array of shortcode designs to be included by default; same array signature as the shortcode function uses
			'Login Screen' => array(
				'icon_set' => 'none',
				'layout' => 'buttons-column',
				'align' => 'center',
				'show_login' => 'conditional',
				'show_logout' => 'conditional',
				'button_prefix' => 'Login with',
				'logged_out_title' => 'Please login:',
				'logged_in_title' => 'You are already logged in.',
				'logging_in_title' => 'Logging in...',
				'logging_out_title' => 'Logging out...',
				'style' => '',
				'class' => '',
				),
			'Profile Page' => array(
				'icon_set' => 'none',
				'layout' => 'buttons-row',
				'align' => 'left',
				'show_login' => 'always',
				'show_logout' => 'never',
				'button_prefix' => 'Link',
				'logged_out_title' => 'Select a provider:',
				'logged_in_title' => 'Select a provider:',
				'logging_in_title' => 'Authenticating...',
				'logging_out_title' => 'Logging out...',
				'style' => '',
				'class' => '',
				),
			),
		'rs_suppress_welcome_email' => 0,								// 0, 1
		'rs_new_user_role' => 'contributor',							// role
		'rs_google_api_enabled' => 0,									// 0, 1
		'rs_google_api_id' => '',										// any string
		'rs_google_api_secret' => '',									// any string
		'rs_facebook_api_enabled' => 0,								// 0, 1
		'rs_facebook_api_id' => '',									// any string
		'rs_facebook_api_secret' => '',								// any string
		
		'rs_twitter_api_enabled' => 0,								// 0, 1
		'rs_twitter_api_id' => '',									// any string
		'rs_twitter_api_secret' => '',								// any string
		
		'rs_linkedin_api_enabled' => 0,								// 0, 1
		'rs_linkedin_api_id' => '',									// any string
		'rs_linkedin_api_secret' => '',								// any string
		'rs_github_api_enabled' => 0,									// 0, 1
		'rs_github_api_id' => '',										// any string
		'rs_github_api_secret' => '',									// any string
		'rs_reddit_api_enabled' => 0,									// 0, 1
		'rs_reddit_api_id' => '',										// any string
		'rs_reddit_api_secret' => '',									// any string
		'rs_windowslive_api_enabled' => 0,							// 0, 1
		'rs_windowslive_api_id' => '',								// any string
		'rs_windowslive_api_secret' => '',							// any string
		'rs_paypal_api_enabled' => 0,									// 0, 1
		'rs_paypal_api_id' => '',										// any string
		'rs_paypal_api_secret' => '',									// any string
		'rs_paypal_api_sandbox_mode' => 0,							// 0, 1
		'rs_instagram_api_enabled' => 0,								// 0, 1
		'rs_instagram_api_id' => '',									// any string
		'rs_instagram_api_secret' => '',								// any string
		'rs_battlenet_api_enabled' => 0,								// 0, 1
		'rs_battlenet_api_id' => '',									// any string
		'rs_battlenet_api_secret' => '',								// any string
		'rs_http_util' => 'curl',										// curl, stream-context
		'rs_http_util_verify_ssl' => 1,								// 0, 1
		'rs_restore_default_settings' => 0,							// 0, 1
		'rs_delete_settings_on_uninstall' => 0,						// 0, 1
	);
		
	private function __construct(){
		$this->dir_plugin = dirname(__FILE__) . DIRECTORY_SEPARATOR;
		$this->dir_include = $this->dir_plugin . 'include' . DIRECTORY_SEPARATOR;
		$this->dir_assets = $this->dir_plugin . 'assets' . DIRECTORY_SEPARATOR;
		$this->url_assets = plugin_dir_url(__FILE__) . 'assets/';
		$this->dir_rootplugin = str_replace( rsmembers::PLUGIN_SLUG ,'',dirname(__FILE__));
		
		register_activation_hook(__FILE__, array(&$this, 'install'));
		register_deactivation_hook(__FILE__, array(&$this, 'uninstall'));
		
		// hook load event to handle any plugin updates:
		add_action('plugins_loaded', array($this, 'rs_update'));
		// hook init event to handle plugin initialization:
		add_action('init', array($this, 'init'));
		
		
		$rsmembers_settings  = get_option( 'rsmembers_settings' );		
		( ! defined( 'modreg' ) ) ? define( 'modreg',      $rsmembers_settings[1][4]  ) : '';
		( ! defined( 'postrestrice' ) ) ? define( 'postrestrice',      $rsmembers_settings[5][4]  ) : '';
		( ! defined( 'pagerestrice' ) ) ? define( 'pagerestrice',      $rsmembers_settings[6][4]  ) : '';
				
		$this->load('rsmembers-library.php');
	 	$this->library = RsMembersLibrary::get_instance($this);
						
		$this->load('rsmembers-widget-login.php');
		$this->load('rsmembers-widget-signup.php');
				
		
		
		
		$this->loadpaypal();
		$this->loadmailchimp();
		$this->loadaccountrenew();
				
		if (is_admin()) {
			$this->wpuser_save();
			
			$this->load('rsmembers-admin.php');
			RsMembersAdmin::get_instance($this);
			
		} else {
			add_action('wp_head',array(&$this, 'hook_css' ) );
			
			$this->load('rsmembers-public.php');
			RsMembersPublic::get_instance($this);
		}
		
		// Enable automatic updates for plugins
		add_filter('auto_update_plugin', '__return_true');

		add_action('plugins_loaded', array(&$this, 'load_textdomain' ));
		add_action('init', array(&$this, 'rsmember_register_css' ));
	}

	
		
	// register our form css
	function rsmember_register_css() {
		wp_enqueue_style( rsmembers::PLUGIN_SLUG.'-main', $this->get_assets_url('css/main.css'), array(), rsmembers::PLUGIN_VERSION );
		wp_enqueue_script( rsmembers::PLUGIN_SLUG.'-rs-login-widget', $this->get_assets_url('js/rs-login-widget.js'), array(), rsmembers::PLUGIN_VERSION, TRUE );
		
		wp_enqueue_style( rsmembers::PLUGIN_SLUG.'-dtpickercss', $this->get_assets_url('css/dtpicker.css'), array(), rsmembers::PLUGIN_VERSION );
		wp_enqueue_script( rsmembers::PLUGIN_SLUG.'-dtpickerjs', $this->get_assets_url('js/dtpicker.js'), array(), rsmembers::PLUGIN_VERSION, TRUE );
						
	}
	
	/**
	 * Loads style in head
	 */
	function hook_css() {	
		$rsmembers_custom  = get_option( 'rsmembers_custom' );
		echo '<style>' . $rsmembers_custom[0][1] .'</style>';
	}
		
	/**
	 * Return a Singleton instance of the class
	 * @return object Returns the instance of the class
	 */
	public static function get_instance(){
		if (NULL === self::$_instance)
			self::$_instance = new self();
		return (self::$_instance);
	}
	
	/**
	 * Loads a specific class name
	 * @param string $file The name of the class file to load
	 */
	public function load_class($file){
		$this->load('classes' . DIRECTORY_SEPARATOR . $file);
	}
	public function load($file){
		include_once($this->dir_include . $file);
	}
		
	/**
	 * Any Payment getway installed
	 *
	*/
	public function getpaymentgetway(){		
		if($this->rsmemberspaypal == 1)
			$this->haspaymentgetway = 1;
		
		return $this->haspaymentgetway;
	}
	
	
	/**
	 * Plugin payment function
	 *
	*/
	public function loadpaypal(){		
		$path = 'rs-members-paymentgetway'; $file = 'rsmemberspaymentgetway-admin.php';	
		$PluginList = get_option('active_plugins');
		$Plugin = $path.'/rsmemberspaymentgetway.php'; 
		if ( in_array( $Plugin , $PluginList ) ) {			
			if( file_exists($this->dir_rootplugin . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file)  ){
				include_once($this->dir_rootplugin . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file);
				include_once($this->dir_rootplugin . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . 'rsmemberspaymentgetway-public.php');
				$this->rsmemberspaypal = 1;	
				update_option( 'rsmembers_haspayment', 'yes', '', 'yes' ); // using update_option to allow for forced update
			}			
		}else{
		
			$this->nopaymentget();
			
		}
	}
	public function getpaypal(){		
		return $this->rsmemberspaypal;			
	}
	public function getrootpath(){		
		return $this->dir_rootplugin;			
	}
	
	/**
	 * Plugin mailchimp function
	 *
	*/
	public function loadmailchimp(){		
		$path = 'rs-members-mailchimp'; $file = 'rsmembersmailchimp-admin.php';	
		$PluginList = get_option('active_plugins');
		$Plugin = $path.'/rsmembersmailchimp.php'; 
		if ( in_array( $Plugin , $PluginList ) ) {			
			if( file_exists($this->dir_rootplugin . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file)  ){
				include_once($this->dir_rootplugin . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file);
				$this->rsmembersmailchimp = 1;	
				//update_option( 'rsmembers_haspayment', 'yes', '', 'yes' ); // using update_option to allow for forced update
			}			
		}else{
			
		}
	}
	public function getmailchimp(){		
		return $this->rsmembersmailchimp;			
	}
	
	/**
	 * Plugin coupons function
	 *
	*/
	public function loadaccountrenew(){		
		$path = 'rs-members-accountrenew'; $file = 'rsmembeaccountrenew-admin.php';	
		$PluginList = get_option('active_plugins');
		$Plugin = $path.'/rsmembersaccountrenew.php'; 
		if ( in_array( $Plugin , $PluginList ) ) {			
			if( file_exists($this->dir_rootplugin . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file)  ){
				include_once($this->dir_rootplugin . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file);
				$this->rsmembersaccountrenew = 1;
			}			
		}else{
			
		}
	}
	public function getaccountrenew(){		
		return $this->rsmembersaccountrenew;			
	}
	
	
	
	/**
	 * Plugin activation callback. Called once when the plugin is installed.
	 */
	public function install(){
		$this->load('rsmembers-install.php');
		RsMembersInstall::install();
	}

	/**
	 * Plugin deactivation callback. Called once when the plugin is uninstalled.
	 */
	public function uninstall()
	{
		$this->load('rsmembers-uninstall.php');
		RsMembersUninstall::uninstall();
	}
	
	
	/**
	 * Returns one of the plugin's directories.
	 * @param string $dir The plugin subdirectory name
	 * @return string The directory with a trailing slash
	 */
	public function get_directory($dir = NULL)
	{
		$dir = $this->dir_plugin . (NULL === $dir ? '' : $dir . DIRECTORY_SEPARATOR);
		return ($dir);
	}

	/**
	 * Returns a URL to the plugin's assets directory
	 * @param string $asset The directory name nad file name of the asset
	 * @return string The URL referencing the plugin's asset
	 */
	public function get_assets_url($asset = NULL)
	{
		$url = $this->url_assets;
		if (NULL !== $asset)
			$url .= $asset;
		return ($url);
	}
	
	/**
	 * Returns a URL to the plugin's include directory
	 */
	public function get_include_url($includeurl = NULL)
	{
		$url = plugin_dir_url(__FILE__).'include/';
		if (NULL !== $includeurl)
			$url .= $includeurl;
		return ($url);
	}

	/**
	 * Loads the plugin's textdomain
	 */
	public function load_textdomain(){		
		load_plugin_textdomain(
			'rsmembers',					// the text domain (see Plugin Headers)
			FALSE,								// deprecated parameter
			$this->get_directory('language'));
	}

	
	/**
	 * Email send
	 */	
	function mail_send($to, $subject, $message, $headers){
		$sent_message = wp_mail( $to, $subject, $message, $headers );	
		if ( $sent_message ) {
			echo 'Message send to '.$to;
		} else {
			echo 'The message was not sent to '.$to;
		}
	}
	
	/**
	 * No payment getway
	 */	
	public function nopaymentget(){
		
		$rsmembers_haspayment = get_option( 'rsmembers_haspayment' );
		if($rsmembers_haspayment == 'yes'){
		
			$rsmembers_settings = get_option( 'rsmembers_settings' );
			for( $row = 0; $row < count( $rsmembers_settings); $row++ ) {				
				if($row==2)
					$set = array( $rsmembers_settings[$row][0],$rsmembers_settings[$row][1],$rsmembers_settings[$row][2],$rsmembers_settings[$row][3], 'on' );
				else
					$set = array( $rsmembers_settings[$row][0],$rsmembers_settings[$row][1],$rsmembers_settings[$row][2],$rsmembers_settings[$row][3], $rsmembers_settings[$row][4] );
				
				$rsmembers_newsettings[$row] = $set;
			}							
			update_option( 'rsmembers_settings', $rsmembers_newsettings );
			
			update_option( 'rsmembers_haspayment', 'no', '', 'yes' ); // using update_option to allow for forced update
		
		}
	
	}
	
	/**
	 * Saved the plugin's data
	 */
	public function wpuser_save(){
		
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['caseselect']) && $_POST['caseselect']=='news_letter'   ){
						
				require( ABSPATH . WPINC . '/pluggable.php' );
							
				$headers = 'From: User Registration '.  get_option( 'admin_email' ) . "\r\n";
				$headers .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
							
				$subject = sanitize_text_field( $_POST['subject'] );
				$message = sanitize_text_field( $_POST['message'] );
				$traditional = sanitize_text_field( $_POST['traditional'] );			
				for($i=0;$i<sizeof($traditional);$i++){
					$to = $traditional[$i];	
					$to = sanitize_email($to);							
					$this->mail_send($to, $subject, $message, $headers);	
				}
				die();
		}
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['caseselect']) && $_POST['caseselect']=='member_role' ){
			
			require_once(ABSPATH .  WPINC .'/pluggable.php');
			
			$userid = $_POST['userid'] ;
			$role =  $_POST['role'] ;
			
			for($i=0;$i<sizeof($userid);$i++){
				$users_id = $userid[$i];
				$userrole = $role[$i];		
				$user_id = wp_update_user( array( 'ID' => $users_id, 'role' => $userrole ) );		
			}
			
			echo'User role update successfully';
			die();
		}
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['caseselect']) && $_POST['caseselect']=='plugin_reset'   ){
		
			$this->load('rsmembers-install.php');
			RsMembersInstall::reset_options();
			
			$this->rs_restore_default_settings();
						
			echo'Reset all settings';
			die();
		}
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['caseselect']) && $_POST['caseselect']=='plugin_settings'   ){
						
				$rsmembers_settings = get_option( 'rsmembers_settings' );
				$value = '';
				for( $row = 0; $row < count( $rsmembers_settings)-2; $row++ ) {				
					$value = sanitize_text_field( $_POST[$rsmembers_settings[$row][2]] );				
					$set = array( $rsmembers_settings[$row][0],$rsmembers_settings[$row][1],$rsmembers_settings[$row][2],$rsmembers_settings[$row][3],$value  );
					$rsmembers_newsettings[$row] = $set;
				}
				$row = $row+1;			
				$set1 = array( $row, 'Terms & Condition Page','termcondi','text',$_POST["termcondi"]);
				$rsmembers_newsettings[$row] = $set1;										
							
				update_option( 'rsmembers_settings', $rsmembers_newsettings );
												
				$rsmembers_custom_options = array(
					array( 'Custom CSS',	sanitize_text_field( $_POST['custom_css'] ) ),
				);
				update_option( 'rsmembers_custom', $rsmembers_custom_options ); 
				
				update_option( 'rsmembers_registration_type', $_POST['rsmembers_registration_type'] ); 
								
				echo 'Settings updated';
				die();
			
		}
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['caseselect']) && $_POST['caseselect']=='required_message'   ){
			
				$rsmembers_messageoptions = get_option( 'rsmembers_messageoptions' );				
				for( $row = 0; $row < count( $rsmembers_messageoptions); $row++ ) {				
					$value = sanitize_text_field( $_POST["rmessage_".$row] );
					$set = array( $rsmembers_messageoptions[$row][0], $value );
					$rsmembers_newmessageoptions[$row] = $set;
				}		
				update_option( 'rsmembers_messageoptions', $rsmembers_newmessageoptions );
				echo 'Required message updated';
				die();
				
		}
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['caseselect']) && $_POST['caseselect']=='field_form'   ){
			$rsmembers_fieldoptions = get_option( 'rsmembers_fieldoptions' );
			update_option( 'rsmembers_fieldoptions', '' );
			
			for( $row = 0; $row < count( $rsmembers_fieldoptions); $row++ ) {
				$value1 = sanitize_text_field( $_POST['fieldtitle'][$row] );
				$value2 = sanitize_text_field( $_POST['fieldname'][$row] );
				$value3 = sanitize_text_field( $_POST['fieldtype'][$row] );
				$value4 = sanitize_text_field( $_POST['fieldaction'][$row] );
				$value5 = sanitize_text_field( $_POST['fieldrequired'][$row] );
				$value6 = sanitize_text_field( $_POST['fieldselectval'][$row] );
				$value7 = sanitize_text_field( $_POST['fieldvalidation'][$row] );
				$value8 = sanitize_text_field( $_POST['fieldsystemtype'][$row] );
								
				$set = array( $row+1 , $value1, $value2, $value3, $value4, $value5, $value6, $value7, $value8);
				$rsmembers_newfieldoptions[$row] = $set;
				
			}
			update_option( 'rsmembers_fieldoptions', $rsmembers_newfieldoptions );
			echo 'Required Field updated';
			die();
		}
		
		
		
		if($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_GET['type']) && $_GET['type']=='editfields' ){	
				
			$rsmembers_fieldoptions = get_option( 'rsmembers_fieldoptions' );
			$fieldsid = $_GET['fieldsid'];
							
			for( $row = 0; $row < count( $rsmembers_fieldoptions); $row++ ) {				
				if($row==$fieldsid){
				
				?>		
				<div style="height:410px; overflow:auto; margin:20px;">
                		 
				<form name="updateformfield" id="updateformfield" method="post" action="<?php echo $_SERVER['REQUEST_URI']?>" enctype="multipart/form-data">
					<input type="hidden" name="fieldposition" value="<?php echo $fieldsid; ?>">
					<input type="hidden" name="caseselect" value="field_update_form">	
					<input type="hidden" value="<?php echo $rsmembers_fieldoptions[$row][4]; ?>" id="field_name_action" name="field_name_action">
					<input type="hidden" value="<?php echo $rsmembers_fieldoptions[$row][8]; ?>" id="field_system_type" name="field_system_type">
                    <input type="hidden" value="<?php echo $rsmembers_fieldoptions[$row][5]; ?>" id="onregarea" name="onregarea">
						
						<h3 class="title">Edit Fields Info</h3><br>                
						<div class="form-inner15">
							<div class="left-col">Field Name</div>
							<div class="right-col">
								<input id="FieldName" name="FieldName" type="text" value="<?php echo $rsmembers_fieldoptions[$row][1]; ?>" class="text-control">
								<div class="clr"></div>
								<div class="r-c-note"></div>
							</div>
							<div class="clr"></div>
						</div>
						<div class="form-inner15">
							<div class="left-col">Field Type</div>
							<div class="right-col">
								<select name="FieldType" id="FieldType" class="select-control">
									<option value="text" <?php if($rsmembers_fieldoptions[$row][3]=='text') echo'selected';?>>Text</option>
									<option value="textarea" <?php if($rsmembers_fieldoptions[$row][3]=='textarea') echo'selected';?>>Textarea</option>
									<option value="password" <?php if($rsmembers_fieldoptions[$row][3]=='password') echo'selected';?>>Password</option>
									<option value="checkbox" <?php if($rsmembers_fieldoptions[$row][3]=='checkbox') echo'selected';?>>Checkbox</option>
									<option value="select" <?php if($rsmembers_fieldoptions[$row][3]=='select') echo'selected';?>>Drop Down</option>
								</select>
								<div class="clr"></div>
								<div class="r-c-note"></div>
							</div>
							<div class="clr"></div>
						</div>
						
						<h3 class="title">Additional information for dropdown fields</h3><br>
						<div class="form-inner15">
							<div class="left-col">Only for dropdown values:</div>
							<div class="right-col">
								<textarea name="selectval" id="selectval" class="text-control" rows="5"><?php echo $fieldvalue = ( $rsmembers_fieldoptions[$row][3]=='select' ) ?  $rsmembers_fieldoptions[$row][6] : '';?></textarea>
								<div class="clr"></div>
								<div class="r-c-note"> Options should be Option Name,option_value| <br><strong>Ex:</strong> <---- Select One ---->,|Position One,1|Position Two,2|Position Three,3|Position Four,4</div>
							</div>
							<div class="clr"></div>
						</div>
						<?php $validationrule = $rsmembers_fieldoptions[$row][7];				
						if(strpos($validationrule ,'maxlen')>0){
							$maxlen = substr( $validationrule, strpos($validationrule ,'maxlen')+7 , strlen($validationrule));
							if(strpos($maxlen ,'|')>0){
								$maxlen = substr( $maxlen, 0 , strpos($maxlen ,'|'));
							}
						}				
						if(strpos($validationrule ,'minlen')>0){
							$minlen = substr( $validationrule, strpos($validationrule ,'minlen')+7 , strlen($validationrule));
							if(strpos($minlen ,'|')>0){
								$minlen = substr( $minlen, 0 , strpos($minlen ,'|'));
							}
						}
						?>                
						<h3 class="title">Field Validation Rules</h3><br>
						<div class="form-inner15">
							<div class="left-col">Required</div>
							<div class="right-col">
								<input name="required" id="required" type="checkbox" <?php if(strpos($validationrule ,'required')==0 and !empty($validationrule)) echo'checked="checked"';?> class="cmn-toggle cmn-toggle-round" />
								<label for="required"></label>
								<div class="clr"></div>
								<div class="r-c-note"></div>
							</div>
							<div class="clr"></div>
						</div>                
						
                        <div class="form-inner15">
                            <div class="left-col">Validation Type</div>
                            <div class="right-col">
                                <select name="CustomValidation" id="CustomValidation" class="select-control">
                                    <option value="">Select Type</option>
                                    <option value="numeric" <?php if(strpos($validationrule ,'numeric')>0 and !empty($validationrule)) echo'selected';?>>Numeric Value</option>
                                    <option value="email" <?php if(strpos($validationrule ,'email')>0 and !empty($validationrule)) echo'selected';?>>Email</option>
                                    <option value="date" <?php if(strpos($validationrule ,'date')>0 and !empty($validationrule)) echo'selected';?>>Date</option>
                                    <option value="website" <?php if(strpos($validationrule ,'website')>0 and !empty($validationrule)) echo'selected';?>>Website</option>
                                </select>
                                <div class="clr"></div>
                                <div class="r-c-note"></div>
                            </div>
                            <div class="clr"></div>
                        </div>
                                               
						<div class="form-inner15">
							<div class="left-col">Maximum length</div>
							<div class="right-col">
								<input id="maxlen" name="maxlen" type="text" value="<?php echo $maxlen;?>" class="text-control">
								<div class="clr"></div>
								<div class="r-c-note"></div>
							</div>
							<div class="clr"></div>
						</div>
						
						<div class="form-inner15">
							<div class="left-col">Minimum length</div>
							<div class="right-col">
								<input id="minlen" name="minlen" type="text" value="<?php echo $minlen;?>" class="text-control">
								<div class="clr"></div>
								<div class="r-c-note"></div>
							</div>
							<div class="clr"></div>
						</div>
						
						<div class="form-inner15">
							<div class="left-col">&nbsp;</div>
							<div class="right-col" id="uffloaderdiv">
                                <input type="submit" value="<?php _e( 'Update Fields', 'rsmembers' ); ?> &raquo;" class="button button-primary" id="uffsubmitbtn" name="uffsubmitbtn">
								<div class="clr"></div>
								<div class="r-c-note"></div>
							</div>
							<div class="clr"></div>
						</div>
				</form>
		
					<div class="clr"></div>    
			   </div>          
				<?php
				
				}		
			}
			die();	
		}
		
		
		
		
		if($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_GET['type']) && $_GET['type']=='deletefields'   ){	
			
			$rsmembers_fieldoptions = get_option( 'rsmembers_fieldoptions' );
			$fieldsid = $_GET['fieldsid'];
							
			for( $row = 0; $row < count( $rsmembers_fieldoptions); $row++ ) {				
				if($row<$fieldsid){	
					$set = array( $row,$rsmembers_fieldoptions[$row][1],$rsmembers_fieldoptions[$row][2],$rsmembers_fieldoptions[$row][3],$rsmembers_fieldoptions[$row][4],$rsmembers_fieldoptions[$row][5],$rsmembers_fieldoptions[$row][6],$rsmembers_fieldoptions[$row][7] ,$rsmembers_fieldoptions[$row][8] );
					$rsmembers_newfieldoptions[$row] = $set;
				}else if($row==$fieldsid){
				
				}else{
					$set = array( $row-1,$rsmembers_fieldoptions[$row][1],$rsmembers_fieldoptions[$row][2],$rsmembers_fieldoptions[$row][3],$rsmembers_fieldoptions[$row][4],$rsmembers_fieldoptions[$row][5],$rsmembers_fieldoptions[$row][6],$rsmembers_fieldoptions[$row][7] ,$rsmembers_fieldoptions[$row][8] );
					$rsmembers_newfieldoptions[$row-1] = $set;		
				}		
			}
			
			update_option( 'rsmembers_fieldoptions', $rsmembers_newfieldoptions );
			
			$rsmembers_fields = get_option( 'rsmembers_fieldoptions' );		
								
				$class = '';
				for( $row = 0; $row < count($rsmembers_fields); $row++ ) {
					$class = ( $class == 'alternate' ) ? '' : 'alternate'; ?>
					<tr id="list_item_<?php echo $row; ?>" class="<?php //echo $class; ?>" valign="top" style="cursor:move; border-bottom:1px solid #666 !important;" >						
                    <input type="hidden" name="caseselect" value="field_form">    
                        <input type="hidden" name="fieldposition" id="fieldposition<?php echo $row; ?>">
                        <input type="hidden" name="fieldtitle[]" id="fieldtitle<?php echo $row; ?>" value="<?php echo $rsmembers_fields[$row][1]; ?>">
                        <input type="hidden" name="fieldname[]" id="fieldname<?php echo $row; ?>" value="<?php echo $rsmembers_fields[$row][2]; ?>">
                        <input type="hidden" name="fieldtype[]" id="fieldtype<?php echo $row; ?>" value="<?php echo $rsmembers_fields[$row][3]; ?>">
                        <input type="hidden" name="fieldselectval[]" id="fieldselectval<?php echo $row; ?>" value="<?php echo $rsmembers_fields[$row][6]; ?>">
                        <input type="hidden" name="fieldvalidation[]" id="fieldvalidation<?php echo $row; ?>" value="<?php echo $rsmembers_fields[$row][7]; ?>">
                        <input type="hidden" name="fieldsystemtype[]" id="fieldsystemtype<?php echo $row; ?>" value="<?php echo $rsmembers_fields[$row][8]; ?>">                      
                        <td width="20%" style="border-bottom:1px solid #e1e1e1;"><?php 
							_e( $rsmembers_fields[$row][1], 'rsmembers' );
							if( $rsmembers_fields[$row][4] == 'no' ){ ?><font color="red">*</font><?php }
							?>
						</td>
                        <td width="20%" style="border-bottom:1px solid #e1e1e1;"><?php echo $rsmembers_fields[$row][2]; ?></td>
                        <td width="20%" style="border-bottom:1px solid #e1e1e1;"><?php echo $rsmembers_fields[$row][3]; ?></td>
                        <?php if( $rsmembers_fields[$row][4]!='no') { ?>
                            <td width="20%" style="border-bottom:1px solid #e1e1e1;">
                            <a onclick="editfields(<?php echo $row; ?>);" href="javascript:void(0)"><?php _e( 'Edit', 'rsmembers' ); ?></a>
                            <?php if($rsmembers_fields[$row][8] == 'u'){?>
                             / 
                            <a onclick="deletefields(<?php echo $row; ?>);" href="javascript:void(0)"><?php _e( 'Delete', 'rsmembers' ); ?></a>
                            <?php }?>
                            <input type="hidden" name="fieldaction[]" id="fieldaction<?php echo $row; ?>" value=""></td>						
						<?php } else { ?>
                            <td width="20%" style="border-bottom:1px solid #e1e1e1;">-<input type="hidden" name="fieldaction[]" id="fieldaction<?php echo $row; ?>" value="no"></td>
                        <?php } ?>
                         <?php if( $rsmembers_fields[$row][4]!='no') {?>
                            <td width="20%" style="border-bottom:1px solid #e1e1e1;"><?php
								$selected = ( $rsmembers_fields[$row][5] == 'on' ) ? 'checked="checked"' : '';	
								?>								
                                <input class="cmn-toggle cmn-toggle-round" type="checkbox" name="fieldcheckbox" id="fieldcheckbox<?php echo $row; ?>" <?php echo $selected; ?> onClick="setfieldvilue('fieldcheckbox<?php echo $row; ?>','fieldrequired<?php echo $row; ?>')">
                                <label for="fieldcheckbox<?php echo $row; ?>"></label>
                                
                                <input type="hidden" name="fieldrequired[]" id="fieldrequired<?php echo $row; ?>" value="<?php echo $rsmembers_fields[$row][5]; ?>">                                								
								</td>
                        <?php } else { ?>
                            <td width="20%" style="border-bottom:1px solid #e1e1e1;">-<input type="hidden" name="fieldrequired[]" id="fieldrequired<?php echo $row; ?>" value="on"></td>                           
                        <?php } ?>
                        
					</tr><?php
				} 
		die();
		}		
		
		
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['caseselect']) && $_POST['caseselect']=='field_update_form'   ){
		
				$rsmembers_fieldoptions = get_option( 'rsmembers_fieldoptions' );
				$fieldsid = sanitize_text_field( $_POST['fieldposition'] );
								
				for( $row = 0; $row < count( $rsmembers_fieldoptions); $row++ ) {				
					if($row<$fieldsid){	
						$set = array( $row,$rsmembers_fieldoptions[$row][1],$rsmembers_fieldoptions[$row][2],$rsmembers_fieldoptions[$row][3],$rsmembers_fieldoptions[$row][4],$rsmembers_fieldoptions[$row][5],$rsmembers_fieldoptions[$row][6],$rsmembers_fieldoptions[$row][7] ,$rsmembers_fieldoptions[$row][8] );
						$rsmembers_newfieldoptions[$row] = $set;
					}else if($row==$fieldsid){
										
						$fieldvalue='';
						$FieldName = sanitize_text_field( $_POST["FieldName"] );			
						$OptionName= strtolower( preg_replace("![^a-z0-9]+!i", "_", $FieldName) );
						$FieldType = sanitize_text_field( $_POST["FieldType"] );
						$field_name_action = sanitize_text_field( $_POST["field_name_action"] );
						$field_system_type = sanitize_text_field( $_POST["field_system_type"] );
						$onregarea = sanitize_text_field( $_POST["onregarea"] );		
						$ondefault = '';
						$textval = '';
						$selectval = sanitize_text_field( $_POST["selectval"] );
						
						if($ondefault!='on') $textval='';		
						$fieldvalue =  ( $FieldType=='select' ) ? $selectval : $textval;
						
						$validation='';
						$required = sanitize_text_field( $_POST["required"] );
						if($required=='on') $validation .='required';
						
						$CustomValidation = sanitize_text_field( $_POST["CustomValidation"] );
			  			if(!empty($CustomValidation)) $validation .='|'.$CustomValidation;
						
						
						$maxlen = sanitize_text_field( $_POST["maxlen"] );
						if(!empty($maxlen)) $validation .='|maxlen:'.$maxlen;
						$minlen = sanitize_text_field( $_POST["minlen"] );
						if(!empty($minlen)) $validation .='|minlen:'.$minlen;
						
						
						$validationrule='';
						if(strpos($validation ,'equired')==0 and !empty($validation))
							$validationrule = 'required|' . $validation;	
						else
							$validationrule = $validation;
						
						if($field_system_type=='u')				
							$set = array( $row,$FieldName,$OptionName,$FieldType,$field_name_action,$onregarea,$fieldvalue,$validationrule,$field_system_type );
						else						
							$set = array( $row,$FieldName,$rsmembers_fieldoptions[$row][2],$FieldType,$field_name_action,$onregarea,$fieldvalue,$validationrule,$field_system_type );
						$rsmembers_newfieldoptions[$row] = $set;
						
						
					}else{
						$set = array( $row,$rsmembers_fieldoptions[$row][1],$rsmembers_fieldoptions[$row][2],$rsmembers_fieldoptions[$row][3],$rsmembers_fieldoptions[$row][4],$rsmembers_fieldoptions[$row][5],$rsmembers_fieldoptions[$row][6],$rsmembers_fieldoptions[$row][7] ,$rsmembers_fieldoptions[$row][8] );
						$rsmembers_newfieldoptions[$row] = $set;		
					}		
				}		
				update_option( 'rsmembers_fieldoptions', $rsmembers_newfieldoptions );
				
		}
	
	
		/*====================================================================
			New field information added
		====================================================================*/
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['caseselect']) && $_POST['caseselect']=='field_new_form'   ){
				
				$error='';
				$fieldvalue='';
				$FieldName = sanitize_text_field( $_POST["FieldName"] );			
				$OptionName= strtolower( preg_replace("![^a-z0-9]+!i", "_", $FieldName) );
				$FieldType = sanitize_text_field( $_POST["FieldType"] );
				
				if(empty($FieldName)) $error='Field Name Required !';
				else if(empty($FieldType)) $error='Field Type Required !';
				
				if($error!=''){
					echo $error; 
					exit;	
				}else{
				
					  $rsmembers_fieldoptions = get_option( 'rsmembers_fieldoptions' );
					  
					  for( $row = 0; $row < count( $rsmembers_fieldoptions); $row++ ) {				
						  $set = array( $rsmembers_fieldoptions[$row][0],$rsmembers_fieldoptions[$row][1],$rsmembers_fieldoptions[$row][2],$rsmembers_fieldoptions[$row][3],$rsmembers_fieldoptions[$row][4],$rsmembers_fieldoptions[$row][5],$rsmembers_fieldoptions[$row][6],$rsmembers_fieldoptions[$row][7] ,$rsmembers_fieldoptions[$row][8] );
						  $rsmembers_newfieldoptions[$row] = $set;
					  }
								  
					  
					  $field_name_action = sanitize_text_field( $_POST["field_name_action"] );
					  $onregarea = ''; 	
					  $ondefault = '';
					  $textval = '';
					  $selectval = sanitize_text_field( $_POST["selectval"] );
					  if($selectval == '<---- Select One ---->,|Position One,1|Position Two,2|Position Three,3|Position Four,4')  $selectval=''; 
					  
					  
					  
					  if($ondefault!='on') $textval='';		
					  $fieldvalue =  ( $FieldType=='select' ) ? $selectval : $textval;
					  
					  $validation='';
					  $required = sanitize_text_field( $_POST["required"] );
					  if($required=='on') $validation .='required';		
					  
					  $CustomValidation = sanitize_text_field( $_POST["CustomValidation"] );
					  if(!empty($CustomValidation)) $validation .='|'.$CustomValidation;
					  
					  
					  $maxlen = sanitize_text_field( $_POST["maxlen"] );
					  if(!empty($maxlen)) $validation .='|maxlen:'.$maxlen;
					  $minlen = sanitize_text_field( $_POST["minlen"] );
					  if(!empty($minlen)) $validation .='|minlen:'.$minlen;
					  
					  
					  $validationrule='';
					  if(strpos($validation ,'equired')==0 and !empty($validation))
						  $validationrule = 'required|' . $validation;	
					  else
						  $validationrule = $validation;
									  
					  $setopt = array( $row,$FieldName,$OptionName,$FieldType,$field_name_action,$onregarea,$fieldvalue,$validationrule,'u' );
					  $rsmembers_newfieldoptions[$row] = $setopt;
						  
					  update_option( 'rsmembers_fieldoptions', $rsmembers_newfieldoptions );
					  
					  echo'Added successfully';
							
				}
				// End code	
				die();
		}
		
		
		/*====================================================================
			Field List
		====================================================================*/
		
		
		if($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_GET['caseselect']) && $_GET['caseselect']=='field_list_form'   ){
							
				$rsmembers_fields = get_option( 'rsmembers_fieldoptions' );		
						
				$class = '';
				for( $row = 0; $row < count($rsmembers_fields); $row++ ) {
					$class = ( $class == 'alternate' ) ? '' : 'alternate'; ?>
					<tr id="list_item_<?php echo $row; ?>" class="<?php //echo $class; ?>" valign="top" style="cursor:move; border-bottom:1px solid #666 !important;" >						
					<input type="hidden" name="caseselect" value="field_form">    
						<input type="hidden" name="fieldposition" id="fieldposition<?php echo $row; ?>">
						<input type="hidden" name="fieldtitle[]" id="fieldtitle<?php echo $row; ?>" value="<?php echo $rsmembers_fields[$row][1]; ?>">
						<input type="hidden" name="fieldname[]" id="fieldname<?php echo $row; ?>" value="<?php echo $rsmembers_fields[$row][2]; ?>">
						<input type="hidden" name="fieldtype[]" id="fieldtype<?php echo $row; ?>" value="<?php echo $rsmembers_fields[$row][3]; ?>">
						<input type="hidden" name="fieldselectval[]" id="fieldselectval<?php echo $row; ?>" value="<?php echo $rsmembers_fields[$row][6]; ?>">
						<input type="hidden" name="fieldvalidation[]" id="fieldvalidation<?php echo $row; ?>" value="<?php echo $rsmembers_fields[$row][7]; ?>">
						<input type="hidden" name="fieldsystemtype[]" id="fieldsystemtype<?php echo $row; ?>" value="<?php echo $rsmembers_fields[$row][8]; ?>">                      
						<td width="20%" style="border-bottom:1px solid #e1e1e1;"><?php 
							_e( $rsmembers_fields[$row][1], 'rsmembers' );
							if( $rsmembers_fields[$row][4] == 'no' ){ ?><font color="red">*</font><?php }
							?>
						</td>
						<td width="20%" style="border-bottom:1px solid #e1e1e1;"><?php echo $rsmembers_fields[$row][2]; ?></td>
						<td width="20%" style="border-bottom:1px solid #e1e1e1;"><?php echo $rsmembers_fields[$row][3]; ?></td>
						<?php if( $rsmembers_fields[$row][4]!='no') { ?>
							<td width="20%" style="border-bottom:1px solid #e1e1e1;">
							<a onclick="editfields(<?php echo $row; ?>);" href="javascript:void(0)"><?php _e( 'Edit', 'rsmembers' ); ?></a>
							<?php if($rsmembers_fields[$row][8] == 'u'){?>
							 / 
							<a onclick="deletefields(<?php echo $row; ?>);" href="javascript:void(0)"><?php _e( 'Delete', 'rsmembers' ); ?></a>
							<?php }?>
							<input type="hidden" name="fieldaction[]" id="fieldaction<?php echo $row; ?>" value=""></td>						
						<?php } else { ?>
							<td width="20%" style="border-bottom:1px solid #e1e1e1;">-<input type="hidden" name="fieldaction[]" id="fieldaction<?php echo $row; ?>" value="no"></td>
						<?php } ?>
						 <?php if( $rsmembers_fields[$row][4]!='no') {?>
							<td width="20%" style="border-bottom:1px solid #e1e1e1;"><?php
								$selected = ( $rsmembers_fields[$row][5] == 'on' ) ? 'checked="checked"' : '';	
								?>								
								<input class="cmn-toggle cmn-toggle-round" type="checkbox" name="fieldcheckbox" id="fieldcheckbox<?php echo $row; ?>" <?php echo $selected; ?> onClick="setfieldvilue('fieldcheckbox<?php echo $row; ?>','fieldrequired<?php echo $row; ?>')">
								<label for="fieldcheckbox<?php echo $row; ?>"></label>
								
								<input type="hidden" name="fieldrequired[]" id="fieldrequired<?php echo $row; ?>" value="<?php echo $rsmembers_fields[$row][5]; ?>">                                								
								</td>
						<?php } else { ?>
							<td width="20%" style="border-bottom:1px solid #e1e1e1;">-<input type="hidden" name="fieldrequired[]" id="fieldrequired<?php echo $row; ?>" value="on"></td>                           
						<?php } ?>
						
					</tr><?php
				} 	
				// End code	
				die();
		}
		
		
		
		/*====================================================================
			Csv download
		====================================================================*/
		
		if($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_GET['caseselect']) && $_GET['caseselect']=='csvdownload'   ){
			
				$args = array(
					'fields' => 'all_with_meta'
				);
				$users = get_users(  );
					
				if ( ! $users ) {
					$referer = add_query_arg( 'error', 'empty', wp_get_referer() );
					wp_redirect( $referer );
					exit;
				}
			
				$filename = 'users.' . date( 'Y-m-d-H-i-s' ) . '.csv';
				
				header( 'Content-Description: File Transfer' );
				header( 'Content-Disposition: attachment; filename=' . $filename );
				header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );
			
				$exclude_data = apply_filters( 'exclude_data', array() );
				
				global $wpdb;
			
				$user_data = array(
					'ID', 'user_login', 'user_pass',
					'user_nicename', 'user_email', 'user_url',
					'user_registered', 'user_activation_key', 'user_status',
					'display_name'
				);
				$user_meta_datas = $wpdb->get_results( "SELECT distinct(meta_key) FROM $wpdb->usermeta" );
				$user_meta_datas = wp_list_pluck( $user_meta_datas, 'meta_key' );
				$fields = array_merge( $user_data, $user_meta_datas );
			
				$headers = array();
				foreach ( $fields as $key => $field ) {
					if ( in_array( $field, $exclude_data ) )
						unset( $fields[$key] );
					else
						$headers[] = '"' . strtolower( $field ) . '"';
				}
				echo implode( ',', $headers ) . "\n";
			
				foreach ( $users as $user ) {
					$data = array();
					foreach ( $fields as $field ) {
						$value = isset( $user->{$field} ) ? $user->{$field} : '';
						$value = is_array( $value ) ? serialize( $value ) : $value;
						$data[] = '"' . str_replace( '"', '""', $value ) . '"';
					}
					echo implode( ',', $data ) . "\n";
				}
			
				// End code	
				die();
		}
					
		/*====================================================================
			Payment Getway
		====================================================================*/
		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['caseselect']) && $_POST['caseselect']=='payment_getway' ){			
			if(method_exists(RsMemberspaymentgetwayAdmin, 'paygetsave')){
				RsMemberspaymentgetwayAdmin::paygetsave();
			}			
		}
		
		/*====================================================================
			Coupon Codes
		====================================================================*/
		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['caseselect']) && $_POST['caseselect']=='couponcode' ){			
			if(method_exists(RsMemberscouponsAdmin, 'couponcodesave')){
				RsMemberscouponsAdmin::couponcodesave();
			}			
		}
		
	
	
	
	
	
	
	
	
	} // End function
	
		
	function rsmembers_ajaxpost($form_name,$loader_div,$loading_div,$loading_image,$submit_button,$form_redirect_js_function){		
		?>
			<style type="text/css">.loading{position:absolute; left:-32px; top:2px; visibility:hidden;}</style>
			<script type="text/javascript">	
            jQuery(function(){
                jQuery("#<?php echo $form_name;?> #<?php echo $loader_div;?>").append('<div id="<?php echo $loading_div;?>" class="loading"><img src="<?php echo $loading_image;?>" alt="loader" align="left" /></div>');
                jQuery("#<?php echo $form_name;?> #<?php echo $loader_div;?>").css({position:'relative'});                        
                jQuery('#<?php echo $form_name;?>').submit(function(e){
                    e.preventDefault();						
                    var form = jQuery(this);
                    var post_url = form.attr('action');
                    var formData = new FormData(jQuery(this)[0]);
					      
                    jQuery.ajax({
                        type: 'POST',
                        url: post_url, 
                        data: formData,
                        async: false,
                        cache: false,
                        contentType: false,
                        processData: false,
                        beforeSend:function(){             
                            jQuery("#<?php echo $form_name;?> #<?php echo $submit_button;?>").attr("disabled", 'false');
                            jQuery("#<?php echo $form_name;?> #<?php echo $loading_div;?>").css({visibility:'visible'});
                        },
                        success: function(msg){
                            <?php echo $form_redirect_js_function.'(msg);';?>
							jQuery.notify({
								inline: true,
								html: '<p>'+msg+'<p>'
							}, 2500);
							setTimeout(function(){
								jQuery("#<?php echo $form_name;?> #<?php echo $submit_button;?>").attr("disabled", false);
								jQuery("#<?php echo $form_name;?> #<?php echo $loading_div;?>").css({visibility:'hidden'});					
							},2500);
                                               
                        },
                        error: function(){
                            
                        },
                        complete: function(){
                            
                        }
                    });
                });
            });
            </script>
		<?php	
	} // End function
	
	function ajaxpost_showvalue($form_name,$loader_div,$loading_div,$loading_image,$submit_button,$container){
		
		?>
			<style type="text/css">.loading{position:absolute; left:-32px; top:2px; visibility:hidden;}</style>
			<script type="text/javascript">	
            jQuery(function(){
                jQuery("#<?php echo $form_name;?> #<?php echo $loader_div;?>").append('<div id="<?php echo $loading_div;?>" class="loading"><img src="<?php echo $loading_image;?>" alt="loader" align="left" /></div>');
                jQuery("#<?php echo $form_name;?> #<?php echo $loader_div;?>").css({position:'relative'});                        
                jQuery('#<?php echo $form_name;?>').submit(function(e){
                    e.preventDefault();						
                    var form = jQuery(this);
                    var post_url = form.attr('action');
                    var formData = new FormData(jQuery(this)[0]);
                            
                    jQuery.ajax({
                        type: 'POST',
                        url: post_url, 
                        data: formData,
                        async: false,
                        cache: false,
                        contentType: false,
                        processData: false,
                        beforeSend:function(){             
                            jQuery("#<?php echo $form_name;?> #<?php echo $submit_button;?>").attr("disabled", 'false');
                            jQuery("#<?php echo $form_name;?> #<?php echo $loading_div;?>").css({visibility:'visible'});
                        },
                        success: function(msg){                           
							jQuery("#<?php echo $container;?>").html(msg);							
							setTimeout(function(){
								jQuery("#<?php echo $form_name;?> #<?php echo $submit_button;?>").attr("disabled", false);
								jQuery("#<?php echo $form_name;?> #<?php echo $loading_div;?>").css({visibility:'hidden'});					
							},2500);
                                               
                        },
                        error: function(){
                            
                        },
                        complete: function(){
                            
                        }
                    });
                });
            });
            </script>
		<?php	
	}
	
	
	
	
	
	// do something during plugin update:
	function rs_update() {
		$plugin_version = RsMembers::PLUGIN_VERSION;
		$installed_version = get_option("rs_plugin_version");
		if (!$installed_version || $installed_version <= 0 || $installed_version != $plugin_version) {
			// version mismatch, run the update logic...
			// add any missing options and set a default (usable) value:
			$this->rs_add_missing_settings();
			// set the new version so we don't trigger the update again:
			update_option("rs_plugin_version", $plugin_version);
			// create an admin notice:
			add_action('admin_notices', array($this, 'rs_update_notice'));
		}
	}
	
	// indicate to the admin that the plugin has been updated:
	function rs_update_notice() {
		//$settings_link = "<a href='options-general.php?page=WP-OAuth.php'>Settings Page</a>"; // CASE SeNsItIvE filename!
		?>
		<div class="updated">
			<p>RS Members has been updated! Please review .</p>
		</div>
		<?php
	}
	
	// adds any missing settings and their default values:
	function rs_add_missing_settings() {
		foreach($this->settings as $setting_name => $default_value) {
			// call add_option() which ensures that we only add NEW options that don't exist:
			if (is_array($this->settings[$setting_name])) {
				$default_value = json_encode($default_value);
			}
			$added = add_option($setting_name, $default_value);
		}
	}
	
	// restores the default plugin settings:
	function rs_restore_default_settings() {
		foreach($this->settings as $setting_name => $default_value) {
			// call update_option() which ensures that we update the setting's value:
			if (is_array($this->settings[$setting_name])) {
				$default_value = json_encode($default_value);
			}
			update_option($setting_name, $default_value);
		}
		add_action('admin_notices', array($this, 'rs_restore_default_settings_notice'));
	}
	
	// indicate to the admin that the plugin has been updated:
	function rs_restore_default_settings_notice() {
		//$settings_link = "<a href='options-general.php?page=WP-OAuth.php'>Settings Page</a>"; // CASE SeNsItIvE filename!
		?>
		<div class="updated">
			<p>The default settings have been restored.</p>
		</div>
		<?php
	}

	
	// initialize the plugin's functionality by hooking into wordpress:
	function init() {
			
		
		add_filter('query_vars', array($this, 'rs_qvar_triggers'));
		add_action('template_redirect', array($this, 'rs_qvar_handlers'));
		// hook scripts and styles for frontend pages:
		add_action('wp_enqueue_scripts', array($this, 'rs_init_frontend_scripts_styles'));
		// hook scripts and styles for backend pages:
		add_action('admin_enqueue_scripts', array($this, 'rs_init_backend_scripts_styles'));
		//add_action('admin_menu', array($this, 'rs_settings_page'));
		add_action('admin_init', array($this, 'rs_register_settings'));
		$plugin = plugin_basename(__FILE__);
		add_filter("plugin_action_links_$plugin", array($this, 'rs_settings_link'));
		// hook scripts and styles for login page:
		add_action('login_enqueue_scripts', array($this, 'rs_init_login_scripts_styles'));
		if (get_option('rs_logo_links_to_site') == true) {add_filter('login_headerurl', array($this, 'rs_logo_link'));}
		
		//add_filter('login_message', array($this, 'rs_customize_login_screen'));
		
		
		
		// hooks used globally:
		add_filter('comment_form_defaults', array($this, 'rs_customize_comment_form_fields'));
		//add_action('comment_form_top', array($this, 'rs_customize_comment_form'));
		add_action('show_user_profile', array($this, 'rs_linked_accounts'));
		add_action('wp_logout', array($this, 'rs_end_logout'));
		add_action('wp_ajax_rs_logout', array($this, 'rs_logout_user'));
		add_action('wp_ajax_rs_unlink_account', array($this, 'rs_unlink_account'));
		add_action('wp_ajax_nopriv_rs_unlink_account', array($this, 'rs_unlink_account'));
		add_shortcode('rs_login_form', array($this, 'rs_login_form'));
		// push login messages into the DOM if the setting is enabled:
		if (get_option('rs_show_login_messages') !== false) {
			add_action('wp_footer', array($this, 'rs_push_login_messages'));
			add_filter('admin_footer', array($this, 'rs_push_login_messages'));
			add_filter('login_footer', array($this, 'rs_push_login_messages'));
		}
	}
	
	
	// init scripts and styles for use on FRONTEND PAGES:
	function rs_init_frontend_scripts_styles() {
		// here we "localize" php variables, making them available as a js variable in the browser:
		$rs_cvars = array(
			// basic info:
			'ajaxurl' => admin_url('admin-ajax.php'),
			'template_directory' => get_bloginfo('template_directory'),
			'stylesheet_directory' => get_bloginfo('stylesheet_directory'),
			'plugins_url' => plugins_url(),
			'plugin_dir_url' => plugin_dir_url(__FILE__),
			'url' => get_bloginfo('url'),
			'logout_url' => wp_logout_url(),
			// other:
			'show_login_messages' => get_option('rs_show_login_messages'),
			'logout_inactive_users' => 240,
			'logged_in' => is_user_logged_in(),
		);
		wp_enqueue_script('rs-cvars', $this->get_assets_url('js/cvars.js') );
		wp_localize_script('rs-cvars', 'rs_cvars', $rs_cvars);
		// we always need jquery:
		wp_enqueue_script('jquery');
		// load the core plugin scripts/styles:
		wp_enqueue_script('rs-script',$this->get_assets_url('js/rs-sociallink.js') , array());
		wp_enqueue_style('rs-style', $this->get_assets_url('css/rs-sociallink.css') , array());
	}
	
	// init scripts and styles for use on BACKEND PAGES:
	function rs_init_backend_scripts_styles() {
		// here we "localize" php variables, making them available as a js variable in the browser:
		$rs_cvars = array(
			// basic info:
			'ajaxurl' => admin_url('admin-ajax.php'),
			'template_directory' => get_bloginfo('template_directory'),
			'stylesheet_directory' => get_bloginfo('stylesheet_directory'),
			'plugins_url' => plugins_url(),
			'plugin_dir_url' => plugin_dir_url(__FILE__),
			'url' => get_bloginfo('url'),
			// other:
			'show_login_messages' => get_option('rs_show_login_messages'),
			'logout_inactive_users' => 240,
			'logged_in' => is_user_logged_in(),
		);
		wp_enqueue_script('rs-cvars', $this->get_assets_url('js/cvars.js') );
		wp_localize_script('rs-cvars', 'rs_cvars', $rs_cvars);
		// we always need jquery:
		wp_enqueue_script('jquery');
		// load the core plugin scripts/styles:
		wp_enqueue_script('rs-script',$this->get_assets_url('js/rs-sociallink.js') , array());
		wp_enqueue_style('rs-style', $this->get_assets_url('css/rs-sociallink.css') , array());
		// load the default wordpress media screen:
		wp_enqueue_media();
	}
	
	// init scripts and styles for use on the LOGIN PAGE:
	function rs_init_login_scripts_styles() {
		// here we "localize" php variables, making them available as a js variable in the browser:
		$rs_cvars = array(
			// basic info:
			'ajaxurl' => admin_url('admin-ajax.php'),
			'template_directory' => get_bloginfo('template_directory'),
			'stylesheet_directory' => get_bloginfo('stylesheet_directory'),
			'plugins_url' => plugins_url(),
			'plugin_dir_url' => plugin_dir_url(__FILE__),
			'url' => get_bloginfo('url'),
			// login specific:
			'hide_login_form' => get_option('rs_hide_wordpress_login_form'),
			'logo_image' => get_option('rs_logo_image'),
			'bg_image' => get_option('rs_bg_image'),
			'login_message' => $_SESSION['WPOA']['RESULT'],
			'show_login_messages' => get_option('rs_show_login_messages'),
			'logout_inactive_users' => 240,
			'logged_in' => is_user_logged_in(),
		);
		wp_enqueue_script('rs-cvars', plugins_url('/cvars.js', __FILE__));
		wp_localize_script('rs-cvars', 'rs_cvars', $rs_cvars);
		// we always need jquery:
		wp_enqueue_script('jquery');
		// load the core plugin scripts/styles:
		wp_enqueue_script('rs-script',$this->get_assets_url('js/wp-oauth.js') , array());
		wp_enqueue_style('rs-style', $this->get_assets_url('css/wp-oauth.css') , array());
	}
	
	// add a settings link to the plugins page:
	function rs_settings_link($links) {
		$settings_link = ''; //"<a href='options-general.php?page=WP-OAuth.php'>Settings</a>"; // CASE SeNsItIvE filename!
		array_unshift($links, $settings_link); 
		return $links; 
	}
	
	// ===============
	// GENERIC HELPERS
	// ===============
	
	// adds basic http auth to a given url string:
	function rs_add_basic_auth($url, $username, $password) {
		$url = str_replace("https://", "", $url);
		$url = "https://" . $username . ":" . $password . "@" . $url;
		return $url;
	}
	
	// ===================
	// LOGIN FLOW HANDLING
	// ===================

	// define the querystring variables that should trigger an action:
	function rs_qvar_triggers($vars) {
		$vars[] = 'connect';
		$vars[] = 'code';
		$vars[] = 'error_description';
		$vars[] = 'error_message';
		return $vars;
	}
	
	// handle the querystring triggers:
	function rs_qvar_handlers() {
		if (get_query_var('connect')) {
			$provider = get_query_var('connect');
			$this->rs_include_connector($provider);
		}
		elseif (get_query_var('code')) {
			$provider = $_SESSION['WPOA']['PROVIDER'];
			$this->rs_include_connector($provider);
		}
		elseif (get_query_var('error_description') || get_query_var('error_message')) {
			$provider = $_SESSION['WPOA']['PROVIDER'];
			$this->rs_include_connector($provider);
		}
	}
	
	// load the provider script that is being requested by the user or being called back after authentication:
	function rs_include_connector($provider) {
		// normalize the provider name (no caps, no spaces):
		$provider = strtolower($provider);
		$provider = str_replace(" ", "", $provider);
		$provider = str_replace(".", "", $provider);
		// include the provider script:
		include 'login-' . $provider . '.php';
	}
	
	// =======================
	// LOGIN / LOGOUT HANDLING
	// =======================

	// match the oauth identity to an existing wordpress user account:
	function rs_match_wordpress_user($oauth_identity) {
		// attempt to get a wordpress user id from the database that matches the $oauth_identity['id'] value:
		global $wpdb;
		$usermeta_table = $wpdb->usermeta;
		$query_string = "SELECT $usermeta_table.user_id FROM $usermeta_table WHERE $usermeta_table.meta_key = 'rs_identity' AND $usermeta_table.meta_value LIKE '%" . $oauth_identity['provider'] . "|" . $oauth_identity['id'] . "%'";
		$query_result = $wpdb->get_var($query_string);
		// attempt to get a wordpress user with the matched id:
		$user = get_user_by('id', $query_result);
		return $user;
	}
	
	// login (or register and login) a wordpress user based on their oauth identity:
	function rs_login_user($oauth_identity) {
		// store the user info in the user session so we can grab it later if we need to register the user:
		$_SESSION["WPOA"]["USER_ID"] = $oauth_identity["id"];
		// try to find a matching wordpress user for the now-authenticated user's oauth identity:
		$matched_user = $this->rs_match_wordpress_user($oauth_identity);
		// handle the matched user if there is one:
		if ( $matched_user ) {
			// there was a matching wordpress user account, log it in now:
			$user_id = $matched_user->ID;
			$user_login = $matched_user->user_login;
			wp_set_current_user( $user_id, $user_login );
			wp_set_auth_cookie( $user_id );
			do_action( 'wp_login', $user_login, $matched_user );
			// after login, redirect to the user's last location
			$this->rs_end_login("Logged in successfully!");
		}
		// handle the already logged in user if there is one:
		if ( is_user_logged_in() ) {
			// there was a wordpress user logged in, but it is not associated with the now-authenticated user's email address, so associate it now:
			global $current_user;
			get_currentuserinfo();
			$user_id = $current_user->ID;
			$this->rs_link_account($user_id);
			// after linking the account, redirect user to their last url
			$this->rs_end_login("Your account was linked successfully with your third party authentication provider.");
		}
		// handle the logged out user or no matching user (register the user):
		if ( !is_user_logged_in() && !$matched_user ) {
			// this person is not logged into a wordpress account and has no third party authentications registered, so proceed to register the wordpress user:
			include 'register.php';
		}
		// we shouldn't be here, but just in case...
		$this->rs_end_login("Sorry, we couldn't log you in. The login flow terminated in an unexpected way. Please notify the admin or try again later.");
	}
	
	// ends the login request by clearing the login state and redirecting the user to the desired page:
	function rs_end_login($msg) {
		$last_url = $_SESSION["WPOA"]["LAST_URL"];
		unset($_SESSION["WPOA"]["LAST_URL"]);
		$_SESSION["WPOA"]["RESULT"] = $msg;
		$this->rs_clear_login_state();
		$redirect_method = get_option("rs_login_redirect");
		$redirect_url = "";
		switch ($redirect_method) {
			case "home_page":
				$redirect_url = site_url();
				break;
			case "last_page":
				$redirect_url = $last_url;
				break;
			case "specific_page":
				$redirect_url = get_permalink(get_option('rs_login_redirect_page'));
				break;
			case "admin_dashboard":
				$redirect_url = admin_url();
				break;
			case "user_profile":
				$redirect_url = get_edit_user_link();
				break;
			case "custom_url":
				$redirect_url = get_option('rs_login_redirect_url');
				break;
		}
		//header("Location: " . $redirect_url);
		wp_safe_redirect($redirect_url);
		die();
	}
	
	// logout the wordpress user:
	// TODO: this is usually called from a custom logout button, but we could have the button call /wp-logout.php?action=logout for more consistency...
	function rs_logout_user() {
		// logout the user:
		$user = null; 		// nullify the user
		session_destroy(); 	// destroy the php user session
		wp_logout(); 		// logout the wordpress user...this gets hooked and diverted to rs_end_logout() for final handling
	}
	
	// ends the logout request by redirecting the user to the desired page:
	function rs_end_logout() {
		$_SESSION["WPOA"]["RESULT"] = 'Logged out successfully.';
		if (is_user_logged_in()) {
			// user is logged in and trying to logout...get their Last Page:
			$last_url = $_SERVER['HTTP_REFERER'];
		}
		else {
			// user is NOT logged in and trying to logout...get their Last Page minus the querystring so we don't trigger the logout confirmation:
			$last_url = strtok($_SERVER['HTTP_REFERER'], "?");
		}
		unset($_SESSION["WPOA"]["LAST_URL"]);
		$this->rs_clear_login_state();
		$redirect_url = "";
		$redirect_url = site_url();
		
		wp_safe_redirect($redirect_url);
		die();
	}
	
	// links a third-party account to an existing wordpress user account:
	function rs_link_account($user_id) {
		if ($_SESSION['WPOA']['USER_ID'] != '') {
			add_user_meta( $user_id, 'rs_identity', $_SESSION['WPOA']['PROVIDER'] . '|' . $_SESSION['WPOA']['USER_ID'] . '|' . time());
		}
	}

	// unlinks a third-party provider from an existing wordpress user account:
	function rs_unlink_account() {
		// get rs_identity row index that the user wishes to unlink:
		$rs_identity_row = $_POST['rs_identity_row']; // SANITIZED via $wpdb->prepare()
		// get the current user:
		global $current_user;
		get_currentuserinfo();
		$user_id = $current_user->ID;
		// delete the rs_identity record from the wp_usermeta table:
		global $wpdb;
		$usermeta_table = $wpdb->usermeta;
		$query_string = $wpdb->prepare("DELETE FROM $usermeta_table WHERE $usermeta_table.user_id = $user_id AND $usermeta_table.meta_key = 'rs_identity' AND $usermeta_table.umeta_id = %d", $rs_identity_row);
		$query_result = $wpdb->query($query_string);
		// notify client of the result;
		if ($query_result) {
			echo json_encode( array('result' => 1) );
		}
		else {
			echo json_encode( array('result' => 0) );
		}
		// wp-ajax requires death:
		die();
	}
	
	// pushes login messages into the dom where they can be extracted by javascript:
	function rs_push_login_messages() {
		$result = $_SESSION['WPOA']['RESULT'];
		$_SESSION['WPOA']['RESULT'] = '';
		echo "<div id='rs-result'>" . $result . "</div>";
	}
	
	// clears the login state:
	function rs_clear_login_state() {
		unset($_SESSION["WPOA"]["USER_ID"]);
		unset($_SESSION["WPOA"]["USER_EMAIL"]);
		unset($_SESSION["WPOA"]["ACCESS_TOKEN"]);
		unset($_SESSION["WPOA"]["EXPIRES_IN"]);
		unset($_SESSION["WPOA"]["EXPIRES_AT"]);
		//unset($_SESSION["WPOA"]["LAST_URL"]);
	}
	
	// ===================================
	// DEFAULT LOGIN SCREEN CUSTOMIZATIONS
	// ===================================

	// force the login screen logo to point to the site instead of wordpress.org:
	function rs_logo_link() {
		return get_bloginfo('url');
	}
	
	// show a custom login form on the default login screen:
	function rs_customize_login_screen() {
		$html = "";
		$design = get_option('rs_login_form_show_login_screen');
		if ($design != "None") {
			// TODO: we need to use $settings defaults here, not hard-coded defaults...
			$html .= $this->rs_login_form_content($design, 'none', 'buttons-column', 'Connect with', 'center', 'conditional', 'conditional', 'Please login:', 'You are already logged in.', 'Logging in...', 'Logging out...');
		}
		echo $html;
	}

	// ===================================
	// DEFAULT COMMENT FORM CUSTOMIZATIONS
	// ===================================
	
	// show a custom login form at the top of the default comment form:
	function rs_customize_comment_form_fields($fields) {
		$html = "";
		$design = get_option('rs_login_form_show_comments_section');
		if ($design != "None") {
			// TODO: we need to use $settings defaults here, not hard-coded defaults...
			$html .= $this->rs_login_form_content($design, 'none', 'buttons-column', 'Connect with', 'center', 'conditional', 'conditional', 'Please login:', 'You are already logged in.', 'Logging in...', 'Logging out...');
			$fields['logged_in_as'] = $html;
		}
		return $fields;
	}
	
	// show a custom login form at the top of the default comment form:
	function rs_customize_comment_form() {
		$html = "";
		$design = get_option('rs_login_form_show_comments_section');
		if ($design != "None") {
			// TODO: we need to use $settings defaults here, not hard-coded defaults...
			$html .= $this->rs_login_form_content($design, 'none', 'buttons-column', 'Connect with', 'center', 'conditional', 'conditional', 'Please login:', 'You are already logged in.', 'Logging in...', 'Logging out...');
		}
		echo $html;
	}

	// =========================
	// LOGIN / LOGOUT COMPONENTS
	// =========================
	
	// shortcode which allows adding the wpoa login form to any post or page:
	function rs_login_form( $atts ){
		$a = shortcode_atts( array(
			'design' => '',
			'icon_set' => 'none',
			'button_prefix' => '',
			'layout' => 'links-column',
			'align' => 'left',
			'show_login' => 'conditional',
			'show_logout' => 'conditional',
			'logged_out_title' => 'Please login:',
			'logged_in_title' => 'You are already logged in.',
			'logging_in_title' => 'Logging in...',
			'logging_out_title' => 'Logging out...',
			'style' => '',
			'class' => '',
		), $atts );
		// convert attribute strings to proper data types:
		
		
		// get the shortcode content:
		$html = $this->rs_login_form_content($a['design'], $a['icon_set'], $a['layout'], $a['button_prefix'], $a['align'], $a['show_login'], $a['show_logout'], $a['logged_out_title'], $a['logged_in_title'], $a['logging_in_title'], $a['logging_out_title'], $a['style'], $a['class']);
		return $html;
	}
	
	// gets the content to be used for displaying the login/logout form:
	function rs_login_form_content($design = '', $icon_set = 'icon_set', $layout = 'links-column', $button_prefix = '', $align = 'left', $show_login = 'conditional', $show_logout = 'conditional', $logged_out_title = 'Please login:', $logged_in_title = 'You are already logged in.', $logging_in_title = 'Logging in...', $logging_out_title = 'Logging out...', $style = '', $class = '') { // even though rs_login_form() will pass a default, we might call this function from another method so it's important to re-specify the default values
		// if a design was specified and that design exists, load the shortcode attributes from that design:
		if ($design != '' && RsMembers::rs_login_form_design_exists($design)) { // TODO: remove first condition not needed
			$a = RsMembers::rs_get_login_form_design($design);
			$icon_set = $a['icon_set'];
			$layout = $a['layout'];
			$button_prefix = $a['button_prefix'];
			$align = $a['align'];
			$show_login = $a['show_login'];
			$show_logout = $a['show_logout'];
			$logged_out_title = $a['logged_out_title'];
			$logged_in_title = $a['logged_in_title'];
			$logging_in_title = $a['logging_in_title'];
			$logging_out_title = $a['logging_out_title'];
			$style = $a['style'];
			$class = $a['class'];
		}
		// build the shortcode markup:
		$html = "";
		$html .= "<div class='rs-login-form rs-layout-$layout rs-layout-align-$align $class' style='$style' data-logging-in-title='$logging_in_title' data-logging-out-title='$logging_out_title'>";
		$html .= "<nav>";
		if (is_user_logged_in()) {
			if ($logged_in_title) {
				$html .= "<p id='rs-title'>" . $logged_in_title . "</p>";
			}
			if ($show_login == 'always') {
				$html .= $this->rs_login_buttons($icon_set, $button_prefix);
			}
			if ($show_logout == 'always' || $show_logout == 'conditional') {
				$html .= "<a class='rs-logout-button' href='" . wp_logout_url() . "' title='Logout'>Logout</a>";
			}
		}
		else {
			if ($logged_out_title) {
				$html .= "<p id='rs-title'>" . $logged_out_title . "</p>";
			}
			if ($show_login == 'always' || $show_login == 'conditional') {
				$html .= $this->rs_login_buttons($icon_set, $button_prefix);
			}
			if ($show_logout == 'always') {
				$html .= "<a class='rs-logout-button' href='" . wp_logout_url() . "' title='Logout'>Logout</a>";
			}
		}
		$html .= "</nav>";
		$html .= "</div>";
		return $html;
	}
	
	// generate and return the login buttons, depending on available providers:
	function rs_login_buttons($icon_set, $button_prefix) {
		// generate the atts once (cache them), so we can use it for all buttons without computing them each time:
		$site_url = get_bloginfo('url');
		$redirect_to = urlencode($_GET['redirect_to']);
		if ($redirect_to) {$redirect_to = "&redirect_to=" . $redirect_to;}
		// get shortcode atts that determine how we should build these buttons:
		$icon_set_path = plugins_url('icons/' . $icon_set . '/', __FILE__);
		$atts = array(
			'site_url' => $site_url,
			'redirect_to' => $redirect_to,
			'icon_set' => $icon_set,
			'icon_set_path' => $icon_set_path,
			'button_prefix' => $button_prefix,
		);
		// generate the login buttons for available providers:
		// TODO: don't hard-code the buttons/providers here, we want to be able to add more providers without having to update this function...
		$html = "";
		$html .= $this->rs_login_button("google", "Google", $atts);
		$html .= $this->rs_login_button("facebook", "Facebook", $atts);
		$html .= $this->rs_login_button("linkedin", "LinkedIn", $atts);
		$html .= $this->rs_login_button("github", "GitHub", $atts);
		$html .= $this->rs_login_button("reddit", "Reddit", $atts);
		$html .= $this->rs_login_button("windowslive", "Windows Live", $atts);
		$html .= $this->rs_login_button("instagram", "Instagram", $atts);
		$html .= $this->rs_login_button("battlenet", "Battlenet", $atts);
		if ($html == '') {
			$html .= 'Sorry, no login providers have been enabled.';
		}
		return $html;
	}

	// generates and returns a login button for a specific provider:
	function rs_login_button($provider, $display_name, $atts) {
		$html = "";
		if (get_option("rs_" . $provider . "_api_enabled")) {
			$html .= "<a id='rs-login-" . $provider . "' class='rs-login-button' href='" . $atts['site_url'] . "?connect=" . $provider . $atts['redirect_to'] . "'>";
			if ($atts['icon_set'] != 'none') {
				$html .= "<img src='" . $atts['icon_set_path'] . $provider . ".png' alt='" . $display_name . "' class='icon'></img>";
			}
			$html .= $atts['button_prefix'] . " " . $display_name;
			$html .= "</a>";
		}
		return $html;
	}
	
	// output the custom login form design selector:
	function rs_login_form_designs_selector($id = '', $master = false) {
		$html = "";
		$designs_json = get_option('rs_login_form_designs');
		$designs_array = json_decode($designs_json);
		$name = str_replace('-', '_', $id);
		$html .= "<select id='" . $id . "' name='" . $name . "'>";
		if ($master == true) {
			foreach($designs_array as $key => $val) {
				$html .= "<option value=''>" . $key . "</option>";
			}
			$html .= "</select>";
			$html .= "<input type='hidden' id='rs-login-form-designs' name='rs_login_form_designs' value='" . $designs_json . "'>";
		}
		else {
			$html .= "<option value='None'>" . 'None' . "</option>";
			foreach($designs_array as $key => $val) {
				$html .= "<option value='" . $key . "' " . selected(get_option($name), $key, false) . ">" . $key . "</option>";
			}
			$html .= "</select>";
		}
		return $html;
	}
	
	// returns a saved login form design as a shortcode atts string or array for direct use via the shortcode
	function rs_get_login_form_design($design_name, $as_string = false) {
		$designs_json = get_option('rs_login_form_designs');
		$designs_array = json_decode($designs_json, true);
		foreach($designs_array as $key => $val) {
			if ($design_name == $key) {
				$found = $val;
				break;
			}
		}
		$atts;
		//echo print_r($found);
		if ($found) {
			if ($as_string) {
				$atts = json_encode($found);
			}
			else {
				$atts = $found;
			}
		}
		return $atts;
	}
	
	function rs_login_form_design_exists($design_name) {
		$designs_json = get_option('rs_login_form_designs');
		$designs_array = json_decode($designs_json, true);
		foreach($designs_array as $key => $val) {
			if ($design_name == $key) {
				$found = $val;
				break;
			}
		}
		if ($found) {
			return true;
		}
		else {
			return false;
		}
	}
	
	// shows the user's linked providers, used on the 'Your Profile' page:
	function rs_linked_accounts() {
		// get the current user:
		global $current_user;
		get_currentuserinfo();
		$user_id = $current_user->ID;
		// get the rs_identity records:
		global $wpdb;
		$usermeta_table = $wpdb->usermeta;
		$query_string = "SELECT * FROM $usermeta_table WHERE $user_id = $usermeta_table.user_id AND $usermeta_table.meta_key = 'rs_identity'";
		$query_result = $wpdb->get_results($query_string);
		// list the rs_identity records:
		echo "<div id='rs-linked-accounts'>";
		echo "<h3>Linked Accounts</h3>";
		echo "<p>Manage the linked accounts which you have previously authorized to be used for logging into this website.</p>";
		echo "<table class='form-table'>";
		echo "<tr valign='top'>";
		echo "<th scope='row'>Your Linked Providers</th>";
		echo "<td>";
		if ( count($query_result) == 0) {
			echo "<p>You currently don't have any accounts linked.</p>";
		}
		echo "<div class='rs-linked-accounts'>";
		foreach ($query_result as $rs_row) {
			$rs_identity_parts = explode('|', $rs_row->meta_value);
			$oauth_provider = $rs_identity_parts[0];
			$oauth_id = $rs_identity_parts[1]; // keep this private, don't send to client
			$time_linked = $rs_identity_parts[2];
			$local_time = strtotime("-" . $_COOKIE['gmtoffset'] . ' hours', $time_linked);
			echo "<div>" . $oauth_provider . " on " . date('F d, Y h:i A', $local_time) . " <a class='rs-unlink-account' data-rs-identity-row='" . $rs_row->umeta_id . "' href='#'>Unlink</a></div>";
		}
		echo "</div>";
		echo "</td>";
		echo "</tr>";
		echo "<tr valign='top'>";
		echo "<th scope='row'>Link Another Provider</th>";
		echo "<td>";
		$design = get_option('rs_login_form_show_profile_page');
		if ($design != "None") {
			// TODO: we need to use $settings defaults here, not hard-coded defaults...
			echo $this->rs_login_form_content($design, 'none', 'buttons-row', 'Link', 'left', 'always', 'never', 'Select a provider:', 'Select a provider:', 'Authenticating...', '');
		}
		echo "</div>";
		echo "</td>";
		echo "</td>";
		echo "</table>";
	}
	
	// ====================
	// PLUGIN SETTINGS PAGE
	// ====================
	
	// registers all settings that have been defined at the top of the plugin:
	function rs_register_settings() {
		foreach ($this->settings as $setting_name => $default_value) {
			register_setting('rs_settings', $setting_name);
		}
	}
	
	// add the main settings page:
	function rs_settings_page() {
		//add_options_page( 'WP-OAuth Options', 'WP-OAuth', 'manage_options', 'WP-OAuth', array($this, 'rs_settings_page_content') );
	}

	// render the main settings page content:
	function rs_settings_page_content() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$blog_url = rtrim(site_url(), "/") . "/";
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

} // End Class


if (!defined('ABSPATH')) { header('Status: 403 Forbidden');  header('HTTP/1.1 403 Forbidden'); die('Forbidden'); }
RsMembers::get_instance();
// EOF


/*====================================================================
	Extended Login
====================================================================*/
	add_filter( 'wp_authenticate_user', 'rsmembers_extended_login', 10, 2 );
	
	function rsmembers_extended_login( $user, $password ){  
				
		$rsmembers_status = get_user_status( $user->ID );
		$user_expiredate = get_user_expire_date( $user->ID );
		
		if ( empty( $rsmembers_status ) ) {
			// the user does not have a status so let's assume the user is good to go
			return $user;
		}
		
		if($user->ID!='1'){		
			
			if(!$user  || $rsmembers_status != 'Active'){
				//User note found, or no value entered or doesn't match stored value - don't proceed.
				remove_action('authenticate', 'wp_authenticate_username_password', 20); 
		
				//Create an error to return to user
				return $user = new WP_Error( 'denied', __("<strong>ERROR</strong>: User [<strong>$user->display_name</strong>] is deactivated. Please contact to administrator for activation.") );
				
			}else if(!$user  || $user_expiredate < date('Y-m-d') ){
				//User note found, or no value entered or doesn't match stored value - don't proceed.
				remove_action('authenticate', 'wp_authenticate_username_password', 20); 
		
				//Create an error to return to user
				return $user = new WP_Error( 'denied', __("<strong>ERROR</strong>:  Free account of <strong>$user->display_name</strong> already expired in [$user_expiredate]. Please contact to administrator for activation.") );
				
			}else 
				return $user;
										
		}else 
			return $user;
		
	}
	
	/**
	 * Get the status of a user.
	 *
	 * @param int $user_id
	 * @return string the status of the user
	 */
	function get_user_status( $user_id ) {
		$user_status = get_user_meta($user_id, 'rsmembers_status', true);

		if ( empty( $user_status ) ) {
			$user_status = 'Active';
		}

		return $user_status;
	}

	/**
	 * Get the expire date of a user.
	 *
	 * @param int $user_id
	 * @return string the status of the user
	 */
	function get_user_expire_date( $user_id ) {
		$user_expiredate = get_user_meta($user_id, 'rsmembers_expiredate', true);

		if ( empty( $user_expiredate ) ) {
			$user_expiredate = date('Y-m-d', strtotime("+15 days"));
		}

		return $user_expiredate;
	}

	
/*====================================================================
	Add Column to user list
====================================================================*/

	function rsmembers_add_user_columns( $column ) {
		$column['rsmembers_status'] = 'User Status';
		$column['rsmembers_actype'] = 'Account Type';
		return $column;
	}
	add_filter( 'manage_users_columns', 'rsmembers_add_user_columns' );
	
	function new_modify_user_table_row( $val, $column_name, $user_id ) {
		$user = get_userdata( $user_id );
		switch ($column_name) {
			case 'rsmembers_status' :
				return get_the_author_meta( 'rsmembers_status', $user_id );
				break;
			case 'rsmembers_actype' :
				return get_the_author_meta( 'rsmembers_actype', $user_id );
				break;
			default:
		}
		return $return;
	}
	add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );


/*====================================================================
	Add field to user edit panel
====================================================================*/

add_action( 'personal_options_update', 'rsmembers_status_fields');
add_action( 'edit_user_profile_update', 'rsmembers_status_fields');

function rsmembers_status_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
    return false;

	update_usermeta( $user_id, 'rsmembers_status', $_POST['rsmembers_status'] );
	update_usermeta( $user_id, 'rsmembers_actype', $_POST['rsmembers_actype'] );
}


add_action( 'show_user_profile', 'rsmembers_edit_status_fields');
add_action( 'edit_user_profile', 'rsmembers_edit_status_fields');

function rsmembers_edit_status_fields ($user) {
?>
<style>
#rsmembers_status { width: 15em;}
#rsmembers_actype { width: 15em;}
</style>
<h3>RS Members Additional Field</h3>
<table class="form-table">
    <tr>
        <th><label for="dropdown">User Status</label></th>
        <td>
            <?php
            //get dropdown saved value
            $selected = get_the_author_meta('rsmembers_status', $user->ID);
            ?>
            <select name="rsmembers_status" id="rsmembers_status">
                <option value="Active" <?php echo ($selected == "Active")?  'selected="selected"' : ''; ?>>Active</option>
                <option value="Deactivate" <?php echo ($selected == "Deactivate")?  'selected="selected"' : ''; ?>>Deactivate</option>              
            </select>
            <span class="description">Select the above</span>
        </td>
    </tr>
    <tr>
        <th><label for="dropdown">User Account Type</label></th>
        <td>
            <?php
            //get dropdown saved value
            $selected = get_the_author_meta('rsmembers_actype', $user->ID);
            ?>
            <select name="rsmembers_actype" id="rsmembers_actype">
                <option value="Free" <?php echo ($selected == "Free")?  'selected="selected"' : ''; ?>>Free</option>
                <option value="Paid" <?php echo ($selected == "Paid")?  'selected="selected"' : ''; ?>>Paid</option>              
            </select>
            <span class="description">Select the above</span>
        </td>
    </tr>
   
    
        
</table>
<?php
}



/*====================================================================
	User Active / Inactive section
====================================================================*/


	add_action( 'admin_footer-users.php', 'rsmembers_bulk_user_action' );
	add_action( 'load-users.php', 'rsmembers_load_user_status' );
	add_action( 'rsmembers_user_action_active', 'rsmembers_set_user_action_active' );
	//add_action( 'rsmembers_user_action_active', 'rsmembers_user_status_inactive' );
	if( modreg == 'on' ) {
		add_filter( 'user_row_actions', 'wpmem_insert_activate_link1', 10, 2 );
	}


	/**
	 * Function to add activate to the bulk dropdown list
	 */
	function rsmembers_bulk_user_action(){
	 ?>
		<script type="text/javascript">
		  jQuery(document).ready(function() {
		<?php if( modreg == 'on' ) { ?>
			jQuery('<option>').val('activebulk').text('<?php _e( 'Active' )?>').appendTo("select[name='action']");
		<?php } ?>			
		<?php if( modreg == 'on' ) { ?>
			jQuery('<option>').val('activebulk').text('<?php _e( 'Active' )?>').appendTo("select[name='action2']");
		<?php } ?>			
		  });
		</script>
		<?php
	}


	/**
	 * Function to add activate link to the user row action
	 *
	 * @param  array $actions
	 * @param  $user_object
	 * @return array $actions
	 */
	function wpmem_insert_activate_link1( $actions, $user_object ) {
		if( current_user_can( 'edit_users', $user_object->ID ) ) {
		
			if($user_object->ID!='1'){		
				$var = get_user_meta( $user_object->ID, 'rsmembers_status', true );			
				if( $var != 'Active' ) {
					$url = "users.php?action=active-single&amp;user=$user_object->ID";
					$url = wp_nonce_url( $url, 'activate-user' );
					$actions['activate'] = '<a href="' . $url . '">Active</a>';
				}else{
					$url = "users.php?action=inactive-single&amp;user=$user_object->ID";
					$url = wp_nonce_url( $url, 'activate-user' );
					$actions['activate'] = '<a href="' . $url . '">Deactivate</a>';		
				}
			}
			
		}
		return $actions;
	}


/**
 * Function to handle bulk actions at page load
 *
 * @uses WP_Users_List_Table
 */
function rsmembers_load_user_status()
{
	$wp_list_table = _get_list_table( 'WP_Users_List_Table' );
	$action = $wp_list_table->current_action();
	$sendback = '';
	
	switch( $action ) {
		
	case 'activebulk':
		
		/** validate nonce */
		check_admin_referer( 'bulk-users' );
		
		/** get the users */
		$users = $_REQUEST['users'];
		
		/** update the users */
		$x = 0;
		foreach( $users as $user ) {
			
			// check to see if the user is already activated, if not, activate
			if(  get_user_meta( $user, 'rsmembers_status', 'Deactivate' ) =='Deactivate' and $user!='1' ) {
				rsmembers_user_status_active( $user );
				$x++;
			}
		}
		
		/** set the return message */
		$sendback = add_query_arg( array('userstatus' => $x . ' users activated' ), $sendback );
		
		break;
		
	case 'active-single':
		
		/** validate nonce */
		check_admin_referer( 'activate-user' );
		
		/** get the users */
		$users = $_REQUEST['user'];
		
		/** set the user activated, if not, activate */
		rsmembers_user_status_active( $users );
			
		/** get the user data */
		$user_info = get_userdata( $users );

		/** set the return message */
		$sendback = add_query_arg( array('userstatus' => "$user_info->user_login activated" ), $sendback );
				
		break;
	
	case 'inactive-single':
		
		/** validate nonce */
		check_admin_referer( 'activate-user' );
		
		/** get the users */
		$users = $_REQUEST['user'];
		
		/** set the user inactivated, if not, inactive */			
		rsmembers_user_status_inactive( $users );
		
		/** get the user data */
		$user_info = get_userdata( $users );
  
		/** set the return message */
		$sendback = add_query_arg( array('userstatus' => "$user_info->user_login deactivate" ), $sendback );
		
		break;		
	
	case 'show':
		
		add_action( 'pre_user_query', 'wpmem_a_pre_user_query' );
		return;
		break;
		
	case 'export':

		/*$users  = ( isset( $_REQUEST['users'] ) ) ? $_REQUEST['users'] : false;
		include_once( WPMEM_PATH . 'admin/user-export.php' );
		wpmem_export_users( array( 'export'=>'selected' ), $users );
		return;*/
		break;
		
	default:
		return;
		break;

	}

	/** if we did not return already, we need to wp_redirect */
	wp_redirect( $sendback );
	exit();

}


/**
 * Activates a user
 *
 * If registration is moderated, sets the activated flag 
 * in the usermeta. Flag prevents login when modreg
 * is true (active). Function is fired from bulk user edit or
 * user profile update.
 *
 * @param int  $user_id
 * @param bool $chk_pass
 * @uses $wpdb WordPress Database object
 */
function rsmembers_user_status_active( $user_id )
{
		
	// set the active flag in usermeta
	update_user_meta( $user_id, 'rsmembers_status', 'Active' );
	
	/**
	 * Fires after the user activation process is complete.
	 *
	 * @param int $user_id The user's ID.
	 */
	do_action( 'rsmembers_user_action_active', $user_id );
	
	return;
}


/**
 * Deactivates a user
 *
 * Reverses the active flag from the activation process
 * preventing login when registration is moderated.
 *
 * @param int $user_id
 */
function rsmembers_user_status_inactive( $user_id ) {
	update_user_meta( $user_id, 'rsmembers_status', 'Deactivate' );
}

/**
 * Use rsmembers_set_user_action_active to set the user_status field to Active using rsmembers_set_action_active.
 *
 * @uses  set_user_status
 * @param $user_id
 */
function rsmembers_set_user_action_active( $user_id ) {
	rsmembers_set_action_active( $user_id, 'Active' );
	return;
}


/**
 * Updates the user_status value in the wp_users table
 *
 * @param $user_id
 * @param $status
 */
function rsmembers_set_action_active( $user_id, $status ) {
	update_user_meta( $user_id, 'rsmembers_status', $status );	
	return;
}



/*====================================================================
	Post Restriction
====================================================================*/
if( postrestrice == 'on' ) {

	function rsmembers_post_restriction_markup($object)
	{
		wp_nonce_field(basename(__FILE__), "rsmembers-post-restriction-nonce"); 
		?>
			<div>
				<span style="width:100%; padding:10px 0px; display:block;">Post is not blocked by default.</span>            
				<?php
					$checkbox_value = get_post_meta($object->ID, "rsmembers-post-restriction", true); 
					if($checkbox_value == ""){
						?>
							<input name="rsmembers-post-restriction" type="checkbox" value="true"> <label for="rsmembers-post-restriction">Block the post</label>
						<?php
					}else if($checkbox_value == "true"){
						?>  
							<input name="rsmembers-post-restriction" type="checkbox" value="true" checked> <label for="rsmembers-post-restriction">Post is blocked</label>
						<?php
					}
				?>            
			</div>
		<?php  
	}
	 
	function rsmembers_post_restriction(){
		add_meta_box("rsmembers-post-restriction", "Post Restriction", "rsmembers_post_restriction_markup", "post", "side", "high", null);
	}
	 
	add_action("add_meta_boxes", "rsmembers_post_restriction");
	
	
	function save_rsmembers_post_restriction($post_id, $post, $update){
		if (!isset($_POST["rsmembers-post-restriction-nonce"]) || !wp_verify_nonce($_POST["rsmembers-post-restriction-nonce"], basename(__FILE__)))
			return $post_id;
	 
		if(!current_user_can("edit_post", $post_id))
			return $post_id;
	 
		if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
			return $post_id;
	 
		$slug = "post";
		if($slug != $post->post_type)
			return $post_id;
		 
		$meta_box_checkbox_value = "";
	 
		if(isset($_POST["rsmembers-post-restriction"])){
			$meta_box_checkbox_value = $_POST["rsmembers-post-restriction"];
		}   
		update_post_meta($post_id, "rsmembers-post-restriction", $meta_box_checkbox_value);
	}
	 
	add_action("save_post", "save_rsmembers_post_restriction", 10, 3);

}
/*====================================================================
	Page Restriction
====================================================================*/
if( pagerestrice == 'on' ) {
	
	function rsmembers_page_restriction_markup($object)
	{
		wp_nonce_field(basename(__FILE__), "rsmembers-page-restriction-nonce"); 
		?>
			<div>
				<span style="width:100%; padding:10px 0px; display:block;">Page is not blocked by default.</span>            
				<?php
					$checkbox_value = get_post_meta($object->ID, "rsmembers-page-restriction", true); 
					if($checkbox_value == ""){
						?>
							<input name="rsmembers-page-restriction" type="checkbox" value="true"> <label for="rsmembers-page-restriction">Block the page</label>
						<?php
					}else if($checkbox_value == "true"){
						?>  
							<input name="rsmembers-page-restriction" type="checkbox" value="true" checked> <label for="rsmembers-page-restriction">Page is blocked</label>
						<?php
					}
				?>            
			</div>
		<?php  
	}
	 
	function rsmembers_page_restriction(){
		add_meta_box("rsmembers-page-restriction", "Page Restriction", "rsmembers_page_restriction_markup", "page", "side", "high", null);
	}
	 
	add_action("add_meta_boxes", "rsmembers_page_restriction");
	
	
	function save_rsmembers_page_restriction($page_id, $page, $update){
		if (!isset($_POST["rsmembers-page-restriction-nonce"]) || !wp_verify_nonce($_POST["rsmembers-page-restriction-nonce"], basename(__FILE__)))
			return $page_id;
	 
		if(!current_user_can("edit_post", $page_id))
			return $page_id;
	 
		if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
			return $page_id;
	 
		$slug = "page";    
		 
		$meta_box_checkbox_value = "";
	 
		if(isset($_POST["rsmembers-page-restriction"])){
			$meta_box_checkbox_value = $_POST["rsmembers-page-restriction"];
		}   
		update_post_meta($page_id, "rsmembers-page-restriction", $meta_box_checkbox_value);
	}
	 
	add_action("save_post", "save_rsmembers_page_restriction", 10, 3);
}

/*====================================================================
	Restricted page and post show section
====================================================================*/
function rsmembers_filter_the_content( $content ) {
    
	$rsmembers_messageoptions  = get_option( 'rsmembers_messageoptions' );
	$rsmembers_settings  = get_option( 'rsmembers_settings' );
	$custom_content='';
	
	global $user_ID, $user_identity; get_currentuserinfo();		
		
	switch(get_post_type()){
	
		case 'post':
			if($rsmembers_settings[5][4]=='on'){
				$checkbox_value = get_post_meta(get_the_ID(), "rsmembers-post-restriction", true); 
				if(!$user_ID and $checkbox_value == "true" )
					$custom_content .= '<div style="color:#F00; padding-bottom:50px;">'.$rsmembers_messageoptions[3][1].'</div><br><br><br><br>[rsmembers-login]';
				else	
					$custom_content .= $content;			
			}else{
				$custom_content .= $content;
			}		
			break;		
		case 'page':
			if($rsmembers_settings[6][4]=='on'){
				$checkbox_value = get_post_meta(get_the_ID(), "rsmembers-page-restriction", true); 
				if(!$user_ID and $checkbox_value == "true" )
					$custom_content .= '<div style="color:#F00; padding-bottom:50px;">'.$rsmembers_messageoptions[4][1].'</div>[rsmembers-login]';
				else	
					$custom_content .= $content;
			}else{
				$custom_content .= $content;
			}		
			break;
	}	
    return $custom_content;
}
add_filter( 'the_content', 'rsmembers_filter_the_content' );
















