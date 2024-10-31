<?php
/*======================================================
->	RsMembers Login Widget
======================================================*/
class RsMembersLogin extends WP_Widget {

	function __construct() {
		// Instantiate the parent object
		$widget_ops = array( 'classname' => '', 'description' => __( "Rs Members Login" ) );
		parent::__construct('rsmemberslogin', __('Rs Members Login'), $widget_ops);		
	}

	function widget( $args, $instance ) {
		extract($args);
						
		$title = apply_filters('widget_title', empty($instance['title']) ? __('User Login') : $instance['title'], $instance, $this->id_base);
		$widget_id = $args['widget_id'];
		
		echo'<aside class="widget widget_rsmemberslogin" id="'.$widget_id.'">		
					<h2 class="widget-title">'.$title.'</h2>';
		
		global $user_ID, $user_identity; get_currentuserinfo();	
		
		if (!$user_ID) { 			
		$ajax_nonce = wp_create_nonce("rs-security-nonce");			
		?>
		
		<div class='rs-widget-login-div'>
					<form method="post" action="<?php echo wp_login_url() ?>" class="wp-user-form">
					<p><label for='user_login-<?php echo $this->number;?>'><?php _e('Username: ', 'rsmembers') ?></label>
					<input id='user_login-<?php echo $this->number;?>' type='text' name='log' required='required' /></p>
					<p><label for='user_pass-<?php echo $this->number;?>'><?php _e('Password: ', 'rsmembers') ?></label>
					<input id='user_pass-<?php echo $this->number;?>' type='password' name='pwd' required='required' /></p>					
					<p><input id='rememberme-<?php echo $this->number; ?>' type='checkbox' name='rememberme' value='forever' />
					<label for='rememberme-<?php echo $this->number; ?>' ><?php _e(' Remember me', 'rsmembers') ?></label></p>
					
					<?php do_action('login_form'); ?>
					<p><input type="submit" name="user-submit" value="<?php _e('Login', 'rsmembers')?>" /></p>
					<p>
					<input type="hidden" name="action" value="login">
					<input type="hidden" name="wp-submit" value="yes">
					<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />					
					<input type="hidden" class="force_ssl_login" value="<?php echo json_encode(force_ssl_login()); ?>"/>
					<input type="hidden" name="security" value="<?php echo $ajax_nonce?>"/>
					</p>
					</form>
					<a class="rs-flipping-link" href='#lost-pass' ><?php _e('Lost your password?', 'rsmembers') ?></a>
					
		</div>        
        <div class='rs-widget-lost_pass-div' style='display:none;'>
			
					<form method="post" action='<?php echo add_query_arg( 'action' , 'lostpassword', wp_login_url() ) ?>'>
					<p><label for='lost_user_login-<?php echo $this->number;?>'><?php _e('Enter your username or email: ', 'rsmembers') ?></label>
					<input type="text" name="user_login" value="" size="20" id="lost_user_login-<?php echo $this->number;?>" /></p>
					<?php do_action('login_form', 'resetpass')?>
					<p><input type="submit" name="user-submit" value="<?php _e('Reset my password', 'rsmembers') ?>"  /></p>
					<p>
					<input type="hidden" name="action" value="lostpassword">
					<input type="hidden" name="wp-submit" value="yes">
					<input type="hidden" name="redirect_to" value="<?php echo empty($_GET)? $_SERVER['REQUEST_URI']."?reset=true" : $_SERVER['REQUEST_URI']."&reset=true"; ?>" />
					<input type="hidden" name="security" value="<?php echo $ajax_nonce?>"/>
					<p>
					</form>
					<a class="rs-flipping-link" href='#rs-login'><?php _e('Back to login', 'rsmembers') ?></a>
		</div>
        <?php
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
        
		echo'</aside>';
	}

	
	private function shortcode_rsmemberslogin(){				
		
		if(isset($_POST['log']) && wp_verify_nonce($_POST['rsmember_login_nonce'], 'rsmember-login-nonce')) {
	 
			// this returns the user ID and other info from the user name
			$user = get_userdatabylogin($_POST['log']);
	 
			if(!$user) {
				$errors = 'Invalid username';
			}	 
			if(!isset($_POST['pwd']) || $_POST['pwd'] == '') {
				$errors = 'Please enter a password';
			}	 
			// check the user's login with their password
			if(!wp_check_password($_POST['pwd'], $user->user_pass, $user->ID)) {
				$errors = 'Incorrect password';
			}
	 	 
			// only log the user in if there are no errors
			if(empty($errors)) {	 				
				wp_setcookie($user_login, $user_pass, true);
				wp_set_current_user($user->ID, $_POST['log']);	
				do_action('wp_login', $_POST['log']);
			}
		
		}
		
		
		
		
		global $user_ID, $user_identity; get_currentuserinfo();		
		$content='';										
		$content .='<div id="login-register-password">';
		if (!$user_ID) { 

    		$content .='<div id="tab1_login" class="">';

			$register = $_GET['register']; $reset = $_GET['reset']; 
			if ($register == true) { 
				$content .='<h3>Success!</h3>
				<p>Check your email for the password and then return to log in.</p>';
			} elseif ($reset == true) { 
				$content .='<h3>Success!</h3>
				<p>Check your email to reset your password.</p>';
			} else {
				$content .='<p>';
				$content .='You are not logged in.';
				$content .= '<br> <span style="color:#F00;">'.$errors.'</span>';
				
				$content .='</p>';
				
			} 
			
			
			$content .='<form method="post" action="" class="wp-user-form">
					<div class="form-signup">
						<div class="left-col"><strong>Username:</strong> </div>
						<div class="right-col">
							<input type="text" name="log" value="'.esc_attr(stripslashes($user_login)).'" size="20" id="user_login" tabindex="11" />
							<div class="clr"></div>
							<div class="r-c-note"></div>
						</div>
						<div class="clr"></div>
					</div>
					<div class="form-signup">
						<div class="left-col"><strong>Password:</strong> </div>
						<div class="right-col">
							<input type="password" name="pwd" value="" size="20" id="user_pass" tabindex="12" />
							<div class="clr"></div>
							<div class="r-c-note"></div>
						</div>
						<div class="clr"></div>
					</div>
					<div class="form-signup">
						<div class="left-col"> </div>
						<div class="right-col">
							'.do_action('login_form').'
							<input type="submit" name="user-submit" value="Log In" tabindex="14" class="user-submit" />							
							<input type="hidden" name="rsmember_login_nonce" value="'. wp_create_nonce('rsmember-login-nonce') .'"/>
							<div class="clr"></div>
							<div class="r-c-note"></div>
						</div>
						<div class="clr"></div>
					</div>
			</form>';
			
		
		$content .='</div>';

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

	} 

	$content .='</div>'; 
				
				
				
		
	return $content;
		
	}	//End Function
	
	
	
	
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
}

function RsMembersLogin_Action() {
	register_widget( 'RsMembersLogin' );
}

add_action( 'widgets_init', 'RsMembersLogin_Action' );
?>