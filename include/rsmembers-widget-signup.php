<?php
/*======================================================
->	RsMembers Login Widget
======================================================*/
class RsMemberssignup extends WP_Widget {
	
	private $dir_rootplugin = NULL;		// the directory where the plugin assets are located
	public $rsmemberspaypal = NULL;		// Is paypal addons installed
		
	function __construct() {		
		// Instantiate the parent object
		$widget_ops = array( 'classname' => 'features-worp', 'description' => __( "Rs Members Signup" ) );
		parent::__construct('rsmemberssignup', __('Rs Members Signup'), $widget_ops);		
	}
	
	
	
	function widget( $args, $instance ) {
		extract($args);
		
		global $user_ID, $user_identity; get_currentuserinfo();		
		if (!$user_ID) {
			$path = plugins_url();
			$dir = plugin_dir_path(__FILE__) ;		
				
			$title = apply_filters('widget_title', empty($instance['title']) ? __('Features') : $instance['title'], $instance, $this->id_base);		
			$title='<span>'.$title.'</span>';				
			echo'<aside class="widget widget_rsmemberssignup" id="'.$widget_id.'">		
					<h2 class="widget-title">'.$title.'</h2>'; 
			
			$rsmembers_registration_type  = get_option( 'rsmembers_registration_type' );
			if($rsmembers_registration_type=='registrationform' or $rsmembers_registration_type=='both'){	
				echo $this->signup_process();
			}			
			if($rsmembers_registration_type=='socialmedia' or $rsmembers_registration_type=='both'){			
				echo $this->rs_customize_login_screen();
			}					
			echo'</aside>';	
			
		}
	
	}

	function curPageURL() {
            $pageURL = 'http';
            if(isset($_SERVER["HTTPS"]))
            if ($_SERVER["HTTPS"] == "on") {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }
            return $pageURL;
    }

	public function signup_process(){
		$this->loadpaypal();		
		$purl = plugins_url();
			
		$content='';
		$dir = plugin_dir_path(__FILE__) ;		
		$rsmembers_payment = get_option( 'rsmembers_payment' );
		$rsmembers_messageoptions  = get_option( 'rsmembers_messageoptions' );
		$rsmembers_settings  = get_option( 'rsmembers_settings' );
		$rsmembers_fields = get_option( 'rsmembers_fieldoptions' );
		
		$rsmembers_payment_fieldoptions = get_option( 'rsmembers_payment_fieldoptions' );		
		$rsmembers_paymenttype  = get_option( 'rsmembers_paymenttype' );
		
		$path = get_site_url().'/'.get_page_uri(get_the_ID());
								
		include( $dir .'rsmembers-library2.php');				
		$library_signup = RsMembersLibrary2::get_instance($this);

		$content .='<div class="rs_user_registration_worp"><form action="'.$_SERVER['REQUEST_URI'].'" name="form_news_letter_signup" id="form_news_letter_signup" method="post" enctype="multipart/form-data">
			<input type="hidden" name="formprocess" value="active_signup">';
		
		for( $row = 0; $row < count($rsmembers_fields); $row++ ) {			
			if($rsmembers_fields[$row][5]=='on'){
			$content .='<div class="form-signup">
				<div class="left-col">'.$rsmembers_fields[$row][1].'</div>
				<div class="right-col">';							 
					$validation = explode('|',$rsmembers_fields[$row][7]);
					$posttype = ( $_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['formprocess']=='active_signup' ) ? '1' : '0';					
					$content .= $library_signup->formcontrol( $rsmembers_fields[$row][2].'_signup', $rsmembers_fields[$row][3] , $rsmembers_fields[$row][6] ,$rsmembers_fields[$row][6],  $_POST[$rsmembers_fields[$row][2].'_signup'], $validation, $posttype ); 							
					$content .='<div class="clr"></div>
					<div class="r-c-note"></div>
				</div>
				<div class="clr"></div>
			</div>';
			}
		} 
		
		if($rsmembers_settings[8][4]!='0'){
			  $content .='<div class="form-inner15">
				  <div class="left-col">&nbsp;</div>
				  <div class="right-col"><input type="checkbox" name="termscon" id="termscon"> <a href="'. get_page_link($rsmembers_settings[8][4]).'" target="_blank" style="text-decoration:none !important; font-size:14px;">Terms & Condition</a></div>
				  <div class="clr"></div>
			  </div>';
		 }
		
		
		$content .='<div class="form-inner15">
				<div class="left-col">&nbsp;</div>
				<div class="right-col" id="nlloaderdiv"><input type="submit" value="Registration" class="button button-primary" id="nlsubmitbtn" name="nlsubmitbtn"></div>
				<div class="clr"></div>
			</div>
			</form></div>';
	
		
		
				
		
		
			
		if($rsmembers_settings[2][4]=='on'){
		
			$content .= $this->free_member($rsmembers_messageoptions,$rsmembers_fields, $library_signup->get_found_error() );
		
		}else{
		
			if($this->getpaypal()==1){		
				if(method_exists(RsMemberspaymentgetwayPublic, 'rsmemberspaymentwid') ){			 
					$content .= RsMemberspaymentgetwayPublic::rsmemberspaymentwid( $path, $rsmembers_payment, $rsmembers_messageoptions, $rsmembers_settings, $rsmembers_fields, $library_signup->get_found_error() );	
				}else{								
					$content .= 'Error Found'; 
				}				
			}
			
		} // End Registration Type
		
		
		return $content;
	}	//End Function

	
	
	/**
	 * Free member account
	 */	
	function free_member($rsmembers_messageoptions,$rsmembers_fields, $error){
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' and !empty($_POST['formprocess']) and $_POST['formprocess']=='active_signup' and $error==0  ){
			
			$content = '';
			$systems='';
			$user='';
			for( $row = 0; $row < count($rsmembers_fields); $row++ ) {			
				$value = sanitize_text_field( $_POST[$rsmembers_fields[$row][2].'_signup'] );
				
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
				$content .='<style>.rs_user_registration_worp{display:none;}</style>';
				
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
	 * mail send
	 */	
	function mail_send($to, $subject, $message, $headers){
		$sent_message = wp_mail( $to, $subject, $message, $headers );	
		if ( $sent_message ) {
			echo 'Message send to '.$to;
		} else {
			echo 'The message was not sent to '.$to;
		}
	}



	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);		
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'count' => 0) );
		$title = strip_tags($instance['title']);
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>		
                        
		<?php
	}
	
	/**
	 * Plugin paypal function
	 *
	*/
	public function loadpaypal(){
		$this->dir_rootplugin = plugin_dir_url('');		
		$path = 'rs-members-paymentgetway'; $file = 'rsmemberspaymentgetway-admin.php';	
		$PluginList = get_option('active_plugins');
		$Plugin = $path.'/rsmemberspaymentgetway.php'; 	 
		if ( in_array( $Plugin , $PluginList ) ) {			
			//if( file_exists($this->dir_rootplugin . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file)  ){
				include_once($this->dir_rootplugin . $path . DIRECTORY_SEPARATOR . $file);
				include_once($this->dir_rootplugin . $path . DIRECTORY_SEPARATOR . 'rsmemberspaymentgetway-public.php');
				$this->rsmemberspaypal = 1;	
				update_option( 'rsmembers_haspayment', 'yes', '', 'yes' ); // using update_option to allow for forced update
			//}					
		}						
	}
	public function getpaypal(){		
		return $this->rsmemberspaypal;			
	}
	
	
	
	
	function rs_customize_login_screen() {
		$html = '<br><h2 style="margin-bottom:30px;">Sign up using social media</h2> ';
		$design = get_option('rs_login_form_show_login_screen');
		if ($design != "None") {
			// TODO: we need to use $settings defaults here, not hard-coded defaults...
			$html .= $this->rs_login_form_content($design, 'none', 'buttons-column', 'Connect with', 'center', 'conditional', 'conditional', '', 'You are already logged in.', 'Logging in...', 'Logging out...');
		}
		return $html;
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

	
	
}

function RsMemberssignup_Action() {
	register_widget( 'RsMemberssignup' );
}

add_action( 'widgets_init', 'RsMemberssignup_Action' );
?>