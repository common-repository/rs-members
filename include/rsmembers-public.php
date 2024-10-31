<?php
/**
 * RS-members is wordpress most powerful membership plugin many many features are include there.
 *
 * @link       http://www.themexpo.net
 *
 * @package    rs-members
 */
class RsMembersPublic
{
	private static $_instance = NULL;
	private $plugin = NULL;
	private $dir_rootplugin = NULL;		// the directory where the plugin assets are located
		
	private function __construct($plugin){
		
		$this->plugin = $plugin;		
		add_shortcode('rsmembers-registration', array(&$this, 'shortcode_rsmembers'));	
		add_shortcode('rsmembers-login', array(&$this, 'shortcode_rsmemberslogin'));	
		add_shortcode('rsmembers-contentrestriction', array(&$this, 'shortcode_restriccontent'));	
		
	}

	/**
	 * Returns the singleton instance for this class
	 * @param Object $plugin The parent plugin's instance
	 * @return Object The single instance to the SlugPublic class
	 */
	public static function get_instance($plugin){
		if (NULL === self::$_instance)
			self::$_instance = new self($plugin);
		return (self::$_instance);
	}
	
	/**
	 * Shortcode callback
	 * Member registration
	 */
	public function shortcode_rsmembers($atts = array(), $content = ''){	
		
		$rsmembers_payment = get_option( 'rsmembers_payment' );
		$rsmembers_messageoptions  = get_option( 'rsmembers_messageoptions' );
		$rsmembers_settings  = get_option( 'rsmembers_settings' );
		$rsmembers_fields = get_option( 'rsmembers_fieldoptions' );
		$rsmembers_registration_type  = get_option( 'rsmembers_registration_type' );
		
		$rsmembers_payment_fieldoptions = get_option( 'rsmembers_payment_fieldoptions' );		
		$rsmembers_paymenttype  = get_option( 'rsmembers_paymenttype' );
		
		$path = get_site_url().'/'.get_page_uri(get_the_ID());	
				
		global $user_ID, $user_identity; get_currentuserinfo();		
		if (!$user_ID) {
				
			if($rsmembers_registration_type=='registrationform' or $rsmembers_registration_type=='both'){																	
				?>				                
				<div class="rs_user_registration_worp_sort">
				<form action="<?php echo $_SERVER['REQUEST_URI']?>" name="rs_user_registration" id="rs_user_registration" method="post" enctype="multipart/form-data" >
					<input type="hidden" name="formprocess" value="active">
				<?php
				for( $row = 0; $row < count($rsmembers_fields); $row++ ) {			
					if($rsmembers_fields[$row][5]=='on'){
					?>			
					<div class="form-inner15">
						<div class="left-col"><?php echo $rsmembers_fields[$row][1]; ?></div>
						<div class="right-col">
							<?php 
							$validation = explode('|',$rsmembers_fields[$row][7]);
							$posttype = ( $_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['formprocess']=='active' ) ? '1' : '0';					
							echo $this->plugin->library->formcontrol($rsmembers_fields[$row][2], $rsmembers_fields[$row][2], $rsmembers_fields[$row][3] , $rsmembers_fields[$row][6] ,$rsmembers_fields[$row][6],  $_POST[$rsmembers_fields[$row][2]], $validation, $posttype ); ?>
							
							<div class="clr"></div>
							<div class="r-c-note"></div>
						</div>
						<div class="clr"></div>
					</div>            
					<?php
					}
				} 
				 
				if($rsmembers_paymenttype[0][1]=='authorize-net') { 
		
					for( $row = 0; $row < count($rsmembers_payment_fieldoptions); $row++ ) {			
						if($rsmembers_fields[$row][5]=='on'){
						?>			
						<div class="form-inner15">
							<div class="left-col"><?php echo $rsmembers_payment_fieldoptions[$row][1]; ?></div>
							<div class="right-col">
								<?php 
								$validation = explode('|',$rsmembers_payment_fieldoptions[$row][7]);
								$posttype = ( $_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['formprocess']=='active' ) ? '1' : '0';					
								echo $this->plugin->library->formcontrol($rsmembers_payment_fieldoptions[$row][2], $rsmembers_payment_fieldoptions[$row][2], $rsmembers_payment_fieldoptions[$row][3] , $rsmembers_payment_fieldoptions[$row][6] ,$rsmembers_payment_fieldoptions[$row][6],  $_POST[$rsmembers_payment_fieldoptions[$row][2]], $validation, $posttype ); ?>
								
								<div class="clr"></div>
								<div class="r-c-note"></div>
							</div>
							<div class="clr"></div>
						</div>            
						<?php
						}
					}
		
		
				}								
				?>
                	
				<?php if($rsmembers_settings[8][4]>0){ ?>
					<div class="form-inner15">
						<div class="left-col">&nbsp;</div>
						<div class="right-col"><input type="checkbox" name="termscon" id="termscon"> <a href="<?php echo get_page_link($rsmembers_settings[8][4]); ?>" target="_blank" style="text-decoration:none !important; font-size:14px;">Terms & Condition</a></div>
						<div class="clr"></div>
					</div>
				<?php } ?>
					
					<div class="form-inner15">
						<div class="left-col">&nbsp;</div>
						<div class="right-col" id="nlloaderdiv"><input type="submit" value="Registration" class="button button-primary" id="nlsubmitbtn" name="nlsubmitbtn"></div>
						<div class="clr"></div>
					</div>
					</form>  
                
                </div> 
                
				<?php
				if($rsmembers_settings[2][4]=='on'){
			
					$content .= $this->free_member($rsmembers_messageoptions,$rsmembers_fields, $this->plugin->library->get_found_error());
				
				}else{
				
					if($this->plugin->getpaypal()==1){		
						if(method_exists(RsMemberspaymentgetwayPublic, 'rsmemberspayment') ){			 
							$content .= RsMemberspaymentgetwayPublic::rsmemberspayment( $path, $rsmembers_payment, $rsmembers_messageoptions, $rsmembers_settings, $rsmembers_fields, $this->plugin->library->get_found_error() );	
						}else{								
							$content .= 'Error Found'; 
						}				
					}
					
				} // End Registration Type
						
			
				echo $content;
				echo '<br>';
			
			
			}
			
			if($rsmembers_registration_type=='socialmedia' or $rsmembers_registration_type=='both'){		
			
				$this->rs_customize_login_screen();	
			
			}
		
		} else {
	
				$content ='<div class="sidebox">
					<h3>Welcome, '.$user_identity.'</h3>
					<div class="usericon">';
						global $userdata; get_currentuserinfo(); $content .= get_avatar($userdata->ID, 60); 
					$content .='</div>
					<div class="userinfo">
						<p>You&rsquo;re logged in as <strong>'.$user_identity.'</strong></p>
						<p>
							<a href="'.wp_logout_url('index.php').'">Log out</a> | ';
							if (current_user_can('manage_options')) { 
								$content .='<a href="' . admin_url() . '" target="_blank">' . __('Dashboard') . '</a>'; 
							} else { 
								$content .='<a href="' . admin_url() . 'profile.php" target="_blank">' . __('Profile') . '</a>'; 
							}
			
						$content .='</p>
					</div>
				</div>';
				echo $content;
				
		}
		
		
		
	
		
	}	//End Function
	



	/**
	 * Free member account
	 */	
	function free_member($rsmembers_messageoptions,$rsmembers_fields, $errors){
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['formprocess']) && $_POST['formprocess']=='active' and $errors==0  ){				
			
			$content = '';
			$systems='';
			$user='';
			for( $row = 0; $row < count($rsmembers_fields); $row++ ) {			
				$value = sanitize_text_field( $_POST[$rsmembers_fields[$row][2]] );
				
				if($rsmembers_fields[$row][8]=='s'){						
					$systems .= $rsmembers_fields[$row][2] .','. $value .'|';
				}
				if($rsmembers_fields[$row][8]=='u'){						
					$user .= $rsmembers_fields[$row][2] .','. $value .'|';
				}
			} 
			$custom = $systems . '/#/#' . $user;	
			
			$customex = explode('/#/#', $custom);
			$systems = $customex[0];
			$user = $customex[1];
								
			$fieldsval  = array();
			$fields = explode('|', $systems);					
			foreach( $fields as $field ) {
				$options = explode( ',', $field );
				$fieldsval["$options[0]"] = $options[1];
			}							
			$userdata = array(
				'user_login'    =>   esc_attr($fieldsval['user_login']),
				'user_email'    =>   esc_attr($fieldsval['user_email']),
				'user_pass'     =>   esc_attr($fieldsval['user_pass']),
				'user_url'      =>   esc_attr($fieldsval['website']),
				'first_name'    =>   esc_attr($fieldsval['first_name']),
				'last_name'     =>   esc_attr($fieldsval['last_name']),
				'nickname'      =>   esc_attr($fieldsval['nickname']),
				'description'   =>   esc_attr($fieldsval['description']),
				'role'     		=>   esc_attr(get_option('default_role')),
				'user_registered' => date('Y-m-d H:i:s'),			
			);
			$user_id = wp_insert_user( $userdata );
										
			if(!empty($user_id)){						
				$content .='<style>.rs_user_registration_worp_sort{display:none;}</style>';
				
				$users = explode('|', $user);					
				foreach( $users as $usr ) {
					$options = explode( ',', $usr );
					update_user_meta( $user_id, $options[0], $options[1] );
				}
				
				if($rsmembers_settings[1][4]=='on')
					update_usermeta( $user_id, 'rsmembers_status', 'Deactivate' );
				else
					update_usermeta( $user_id, 'rsmembers_status', 'Active' );
				
				if($rsmembers_settings[3][4]>0)
					update_usermeta( $user_id, 'rsmembers_expiredate', date('Y-m-d', strtotime("+".$rsmembers_settings[3][4]." days")) );
				else
					update_usermeta( $user_id, 'rsmembers_expiredate', date('Y-m-d', strtotime("+".'365'." days")) );								
				
				update_usermeta( $user_id, 'rsmembers_actype', 'Free' );
				
				$headers = 'From: User Registration <'.  get_option( 'admin_email' ) . ">\r\n";
				$headers .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
												
				$subject = 'User Registration';
				$message = 'Thank you for registration. <br><br> Your mail address: '.$fieldsval['user_email'].' User Id: '.$fieldsval['user_login'].' Password: '.$fieldsval['user_pass'] . '';
				$to = $fieldsval['user_email'];						
				$sent_message = wp_mail( $to, $subject, $message, $headers );
												
				if($rsmembers_settings[0][4]=='on'){						
					$subject = 'User registration admin notification';
					$message = 'One user complete registration. <br><br> Mail address: '.$fieldsval['user_email'].' User Id: '.$fieldsval['user_login']. '';
					$to = get_option( 'admin_email' );						
					$sent_message = wp_mail( $to, $subject, $message, $headers );						
				}								
				$content .= $rsmembers_messageoptions[1][1];		
				
			}
			
		} // End Post Data
		
		return $content;
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
			$this->dir_rootplugin = $this->plugin->getrootpath();			
			if( file_exists($this->dir_rootplugin . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file)  ){
				include_once($this->dir_rootplugin . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file);
				$this->rsmemberspaypal = 1;	
				update_option( 'rsmembers_haspayment', 'yes', '', 'yes' ); // using update_option to allow for forced update
			}			
		}
	}
	public function getpaypal(){		
		return $this->rsmemberspaypal;			
	}







	/**
	 * Shortcode callback
	 * member login
	 */
	public function shortcode_rsmemberslogin($atts = array(), $content = '')
	{
		$content='';	
		
		global $user_ID, $user_identity; get_currentuserinfo();	
		
		if (!$user_ID) { 			
		$ajax_nonce = wp_create_nonce("rs-security-nonce");			
			
			$redirect_to = empty($_GET)? $_SERVER["REQUEST_URI"]."?reset=true" : $_SERVER["REQUEST_URI"]."&reset=true";
		
			$content .='<div class="sidebox"><h3>Login to your account</h3><div class="rs-widget-login-div" >
						<form method="post" action="'. wp_login_url().'" class="wp-user-form">
						<p><label for="user_login1">Username:</label>
						<input id="user_login1" type="text" name="log" required="required" /></p>
						<p><label for="user_pass1">Password:</label>
						<input id="user_pass1" type="password" name="pwd" required="required" /></p>					
						<p><input id="rememberme-1" type="checkbox" name="rememberme" value="forever" />
						<label for="rememberme-1" >Remember me</label></p>
						
						'.do_action('login_form').'
						<p><input type="submit" name="user-submit" value="Login" /></p>
						<p>
						<input type="hidden" name="action" value="login">
						<input type="hidden" name="wp-submit" value="yes">
						<input type="hidden" name="redirect_to" value="'. $_SERVER['REQUEST_URI'].'" />
						<input type="hidden" class="force_ssl_login" value="<?php echo json_encode(force_ssl_login()); ?>"/>
						<input type="hidden" name="security" value="<?php echo $ajax_nonce?>"/>
						</p>
						</form>
						<a class="rs-flipping-link" href="#lost-pass" >Lost your password?</a>
						
			</div>        
			<div class="rs-widget-lost_pass-div" style="display:none;">
				
						<form method="post" action="'. add_query_arg( 'action' , 'lostpassword', wp_login_url() ) .'">
						<p><label for="lost_user_login1">Enter your username or email: </label>
						<input type="text" name="user_login" value="" size="20" id="lost_user_login1" /></p>
						'.do_action('login_form', 'resetpass') .'
						<p><input type="submit" name="user-submit" value="Reset my password" /></p>
						<p>
						<input type="hidden" name="action" value="lostpassword">
						<input type="hidden" name="wp-submit" value="yes">
						<input type="hidden" name="redirect_to" value="'. $redirect_to .'" />
						<input type="hidden" name="security" value="'. $ajax_nonce.'"/>
						<p>
						</form>
						<a class="rs-flipping-link" href="#rs-login">Back to login</a>
			</div>';
        
        } else {
	
			$content .='<div class="sidebox">
				<h3>Welcome, '.$user_identity.'</h3>
				<div class="usericon">';
					global $userdata; get_currentuserinfo(); $content .= get_avatar($userdata->ID, 60); 
				$content .='</div>
				<div class="userinfo">
					<p>You&rsquo;re logged in as <strong>'.$user_identity.'</strong></p>
					<p>
						<a href="'.wp_logout_url('index.php').'">Log out</a> | ';
						if (current_user_can('manage_options')) { 
							$content .='<a href="' . admin_url() . '" target="_blank">' . __('Dashboard') . '</a>'; 
						} else { 
							$content .='<a href="' . admin_url() . 'profile.php" target="_blank">' . __('Profile') . '</a>'; 
						}
		
					$content .='</p>
				</div>
			</div>';
			echo $content;
	
		} 				
		$content .='<style>.sidebox{max-width:300px;}</style>';		
		
		return $content;
		
	}	//End Function

	/**
	 * Shortcode callback
	 * Content restriction
	 */
	public function shortcode_restriccontent($atts = array(), $content = ''){
			
		global $user_ID, $user_identity; get_currentuserinfo();
		$rsmembers_settings = get_option( 'rsmembers_settings' ); 
		if (!$user_ID and $rsmembers_settings[7][4] == 'on' ) {					
			return '<strong style="color:#F00;">Only logined user can visible this section.</strong>';		
		}else{
			return $content;
		}	
		
	}	//End Function
	
	
	
	
	
	
	
	
	
	
	
	
	
	function rs_customize_login_screen() {
		$html = '<h2 style="margin-bottom:30px;">Sign up using social media</h2> ';
		$design = get_option('rs_login_form_show_login_screen');
		if ($design != "None") {
			// TODO: we need to use $settings defaults here, not hard-coded defaults...
			$html .= $this->rs_login_form_content($design, 'none', 'buttons-column', 'Connect with', 'center', 'conditional', 'conditional', '', 'You are already logged in.', 'Logging in...', 'Logging out...');
		}
		echo $html;
	}
	
	// gets the content to be used for displaying the login/logout form:
	function rs_login_form_content($design = '', $icon_set = 'icon_set', $layout = 'links-column', $button_prefix = '', $align = 'left', $show_login = 'conditional', $show_logout = 'conditional', $logged_out_title = '', $logged_in_title = 'You are already logged in.', $logging_in_title = 'Logging in...', $logging_out_title = 'Logging out...', $style = '', $class = '') { // even though rs_login_form() will pass a default, we might call this function from another method so it's important to re-specify the default values
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
		$icon_set_path = plugins_url('icons/', __FILE__);
		$atts = array(
			'site_url' => $site_url,
			'redirect_to' => $redirect_to,
			'icon_set' => $icon_set,
			'icon_set_path' => $icon_set_path,
			'button_prefix' => $button_prefix,
		);
		// generate the login buttons for available providers:
		// TODO: don't hard-code the buttons/providers here, we want to be able to add more providers without having to update this function...
		$html = "<ul>";
		$html .= $this->rs_login_button("google", "Google", $atts);
		$html .= $this->rs_login_button("facebook", "Facebook", $atts);
		$html .= $this->rs_login_button("twitter", "Twitter", $atts);
		$html .= $this->rs_login_button("linkedin", "LinkedIn", $atts);
		$html .= $this->rs_login_button("github", "GitHub", $atts);
		$html .= $this->rs_login_button("reddit", "Reddit", $atts);
		$html .= $this->rs_login_button("windowslive", "Windows Live", $atts);
		$html .= $this->rs_login_button("instagram", "Instagram", $atts);
		$html .= $this->rs_login_button("battlenet", "Battlenet", $atts);
		$html .= "</ul>";
		if ($html == '<ul></ul>') {
			$html = 'Sorry, no login providers have been enabled.';
		}
		
		return $html;
	}

	// generates and returns a login button for a specific provider:
	function rs_login_button($provider, $display_name, $atts) {
		$html = "";
		if (get_option("rs_" . $provider . "_api_enabled")) {
			$html .= "<li><a id='rs-login-" . $provider . "' class='rs-login-button' href='" . $atts['site_url'] . "?connect=" . $provider . $atts['redirect_to'] . "'>";
			//if ($atts['icon_set'] != 'none') {				
				$html .= "<img src='" .$atts['icon_set_path'].$provider.".png'  alt='" . $display_name . "' class='icon'>";
			//}
			//$html .= $atts['button_prefix'] . " " . $display_name;
			$html .= "</a></li>";
		}
		return $html;
	}



	
	

}	//End Class

// EOF