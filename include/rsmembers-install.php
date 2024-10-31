<?php
/**
 * RS-members is wordpress most powerful membership plugin many many features are include there.
 *
 * @link       http://www.themexpo.net
 * @since      1.0.1
 *
 * @package    rs-members
 */
 
class RsMembersInstall
{
	/**
	 * Performs the installaction process for the plugin
	 */
	public static function install(){
		// perform installation steps
		self::create_options();
		
	}
	
	/**
	 * Creates options needed by the plugin.
	 */
	private static function create_options(){
			
			
			if(!get_option( 'rsmembers_settings' )){
				
				
				add_option('rsmembers_settings', "");				
				$user_field_options = array(
					array( 1, 'Notify Admin',							'nadmin',		'checkbox',		''),
					array( 2, 'Moderate Registration',					'modreg',		'checkbox',		''),
					array( 3, 'Free Account',							'faccount',		'checkbox',		'on'),
					array( 4, 'Trial Period',							'trailperiod',	'text',			'0'),
					array( 5, 'Captcha',								'captcha',		'checkbox',		''),
					array( 6, 'Restrict Post',							'rconpp',		'checkbox',		''),
					array( 7, 'Restrict Page',							'rpage',		'checkbox',		''),
					array( 8, 'Restrict Post/Page Content',				'rpagepost',	'checkbox',		''),
					array( 9, 'Terms & Condition Page',					'termcondi',	'text',			'0'),
				);
				update_option( 'rsmembers_settings', $user_field_options, '', 'yes' ); // using update_option to allow for forced update
				
				
				add_option('rsmembers_fieldoptions', "");
				$user_field_options = array(
					array( 1,  	'User Login',         	'user_login',		'text',     'no', 	'on',	'',	'required|minlen:5|userid|userexist',   's'),
					array( 2, 	'Email',              	'user_email',       'text',     'no', 	'on',	'',	'required|email|emailexist',			's'),	
					array( 3, 	'Password',           	'user_pass',        'password', 'no', 	'on',	'',	'required',								's'),
					array( 4, 	'Confirm Password',   	'user_pass_confirm','password', 'no', 	'on',	'',	'required|passwordmatch',				's'),					
					array( 5,  	'First Name',         	'first_name',       'text',     '', 	'',		'',	'required|minlen:3|maxlen:50',			's'),	
					array( 6,  	'Last Name',          	'last_name',        'text',     '', 	'',		'',	'required|minlen:3|maxlen:50',			's'),					
					array( 7,  	'Nickname',				'nickname',    		'text',     '', 	'',		'',	'',										's'),
					array( 8,  	'Display Name',   		'display_name',     'text',     '', 	'',		'',	'',										's'),
					array( 9,  	'Description',   		'description',     	'text',     '', 	'',		'',	'',										's'),
					array( 10,  'Website',          	'website',          'text',     '', 	'',		'',	'',										's'),
					array( 11,  'Sate',          		'sate',          	'text',     '', 	'',		'',	'',										'u'),
					array( 12,  'City',          		'city',          	'text',     '', 	'',		'',	'',										'u'),
					array( 13,  'Address',          	'address',          'text',     '', 	'',		'',	'',										'u'),
					array( 14,  'Zip Code',          	'zip-code',         'text',     '', 	'',		'',	'',										'u'),
					
				);
				update_option( 'rsmembers_fieldoptions', $user_field_options, '', 'yes' ); // using update_option to allow for forced update
				
				
				add_option('rsmembers_messageoptions', "");		
				$rsmembers_messageoptions = array(
					array( 'Restricted post (or page), displays above the login/registration form',  'This content is restricted to site members.  If you are an existing user, please log in.  New users may register below.' ),
					array( 'Registration completed',  'Congratulations! Your registration was successful.<br /><br />You may now log in using the user id and password.' ),
					array( 'Registration cancel',  'Sorry! Your registration not complete.<br /><br />Please try again to complete your registration.' ),
					array( 'Post Restriction',  'This post content is restricted to site members. If you are an existing user, please log in.' ),
					array( 'Page Restriction',  'This page content is restricted to site members. If you are an existing user, please log in.' ),
					array( 'Content Restriction',  'Only loged user can be access this section.' ),	
					array( 'Account Renew',  'Renew your account' )				
				);				
				update_option( 'rsmembers_messageoptions', $rsmembers_messageoptions, '', 'yes' ); // using update_option to allow for forced update
								
				add_option('rsmembers_custom', "");				
				$rsmembers_custom_options = array(
					array( 'Custom CSS',	''),
					array( 'Custom JS',		''),
				);
				update_option( 'rsmembers_custom', $rsmembers_custom_options, '', 'yes' ); // using update_option to allow for forced update	
				
				add_option('rsmembers_haspayment', "no");
				update_option( 'rsmembers_haspayment', 'no', '', 'yes' ); // using update_option to allow for forced update
				
				
				add_option('rsmembers_registration_type', "");
				update_option( 'rsmembers_registration_type', 'both', '', 'yes' ); // using update_option to allow for forced update
				
							
			}
	
	
	}


	/**
	 * Creates options needed by the plugin.
	 */
	public function reset_options(){
			
				
			add_option('rsmembers_settings', "");				
			$user_field_options = array(
				array( 1, 'Notify Admin',							'nadmin',		'checkbox',		''),
				array( 2, 'Moderate Registration',					'modreg',		'checkbox',		''),
				array( 3, 'Free Account',							'faccount',		'checkbox',		'on'),
				array( 4, 'Trial Period',							'trailperiod',	'text',			'0'),
				array( 5, 'Captcha',								'captcha',		'checkbox',		''),
				array( 6, 'Restrict Post',							'rconpp',		'checkbox',		''),
				array( 7, 'Restrict Page',							'rpage',		'checkbox',		''),
				array( 8, 'Restrict Post/Page Content',				'rpagepost',	'checkbox',		''),
				array( 9, 'Terms & Condition Page',					'termcondi',	'text',			'0'),
			);
			update_option( 'rsmembers_settings', $user_field_options, '', 'yes' ); // using update_option to allow for forced update
			
			
			add_option('rsmembers_fieldoptions', "");
			$user_field_options = array(
				array( 1,  	'User Login',         	'user_login',		'text',     'no', 	'on',	'',	'required|minlen:5|userid|userexist',   's'),
				array( 2, 	'Email',              	'user_email',       'text',     'no', 	'on',	'',	'required|email|emailexist',			's'),	
				array( 3, 	'Password',           	'user_pass',        'password', 'no', 	'on',	'',	'required',								's'),
				array( 4, 	'Confirm Password',   	'user_pass_confirm','password', 'no', 	'on',	'',	'required|passwordmatch',				's'),					
				array( 5,  	'First Name',         	'first_name',       'text',     '', 	'',		'',	'required|minlen:3|maxlen:50',			's'),	
				array( 6,  	'Last Name',          	'last_name',        'text',     '', 	'',		'',	'required|minlen:3|maxlen:50',			's'),					
				array( 7,  	'Nickname',				'nickname',    		'text',     '', 	'',		'',	'',										's'),
				array( 8,  	'Display Name',   		'display_name',     'text',     '', 	'',		'',	'',										's'),
				array( 9,  	'Description',   		'description',     	'text',     '', 	'',		'',	'',										's'),
				array( 10,  'Website',          	'website',          'text',     '', 	'',		'',	'',										's'),
				array( 11,  'Sate',          		'sate',          	'text',     '', 	'',		'',	'',										'u'),
				array( 12,  'City',          		'city',          	'text',     '', 	'',		'',	'',										'u'),
				array( 13,  'Address',          	'address',          'text',     '', 	'',		'',	'',										'u'),
				array( 14,  'Zip Code',          	'zip-code',         'text',     '', 	'',		'',	'',										'u'),
				
			);
			update_option( 'rsmembers_fieldoptions', $user_field_options, '', 'yes' ); // using update_option to allow for forced update
			
			
			add_option('rsmembers_messageoptions', "");		
			$rsmembers_messageoptions = array(
				array( 'Restricted post (or page), displays above the login/registration form',  'This content is restricted to site members.  If you are an existing user, please log in.  New users may register below.' ),
				array( 'Registration completed',  'Congratulations! Your registration was successful.<br /><br />You may now log in using the user id and password.' ),
				array( 'Registration cancel',  'Sorry! Your registration not complete.<br /><br />Please try again to complete your registration.' ),
				array( 'Post Restriction',  'This post content is restricted to site members. If you are an existing user, please log in.' ),
				array( 'Page Restriction',  'This page content is restricted to site members. If you are an existing user, please log in.' ),
				array( 'Content Restriction',  'Only loged user can be access this section.' ),	
				array( 'Account Renew',  'Renew your account' )				
			);				
			update_option( 'rsmembers_messageoptions', $rsmembers_messageoptions, '', 'yes' ); // using update_option to allow for forced update
							
			add_option('rsmembers_custom', "");				
			$rsmembers_custom_options = array(
				array( 'Custom CSS',	''),
				array( 'Custom JS',		''),
			);
			update_option( 'rsmembers_custom', $rsmembers_custom_options, '', 'yes' ); // using update_option to allow for forced update	
			
			add_option('rsmembers_haspayment', "no");
			update_option( 'rsmembers_haspayment', 'no', '', 'yes' ); // using update_option to allow for forced update
				
				
			add_option('rsmembers_registration_type', "");
			update_option( 'rsmembers_registration_type', 'both', '', 'yes' ); // using update_option to allow for forced update				
			
	
	
	}


	

}	//End Class

// EOF