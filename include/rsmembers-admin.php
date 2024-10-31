<?php
/**
 * RS-members is wordpress most powerful membership plugin many many features are include there.
 *
 * @link       http://www.themexpo.net
 *
 * @package    rs-members
 */
class RsMembersAdmin
{
	private static $_instance = NULL;

	private $_plugin = NULL;					// reference to main plugin instance
	private $_options = NULL;					// reference to options array
	private $_settings = NULL;					// the SpectrOMSettings instance
	public $b;
	
	public function __construct($plugin){
		
		$this->_plugin = $plugin;
		add_action('admin_enqueue_scripts', array(&$this, 'register_scripts'));

		add_action('admin_init', array(&$this, 'admin_init'));		
		add_action('admin_menu', array(&$this, 'admin_menu'));
		
	}
	
	/**
	 * Return a Singleton instance of the class
	 * @return object Returns the instance of the class
	 */
	public static function get_instance($plugin){
		if (NULL === self::$_instance)
			self::$_instance = new self($plugin);
		return (self::$_instance);
	}

	
	/**
	 * Callback for 'admin_init' action
	 */
	public function admin_init(){
		// init process for button control
		if ( get_user_option('rich_editing') == 'true') {
			$rsmembers_settings = get_option( 'rsmembers_settings' ); 
			if ($rsmembers_settings[7][4] == 'on' ) {					
				add_filter('mce_external_plugins',array(&$this, 'rsmembers_call_editor') );
				add_filter('mce_buttons', array(&$this, 'resticontent_add_button'), 0);
			}
		}
	}
	
	/**
	 * @return editor button
	 */
	function resticontent_add_button($buttons){
		array_push($buttons,"rsmember_editor_button");
		return $buttons;
	}
	function rsmembers_call_editor($plugin_array){
		$url = $this->_plugin->get_assets_url('js/editor_plugin.js') ;
		$plugin_array['rsmember_editor_button'] = $url;
		return $plugin_array;
	}
	
	
	/**
	 * Sets up the admin menu
	 */
	public function admin_menu(){
		
		add_menu_page( rsmembers::PLUGIN_NAME , rsmembers::PLUGIN_NAME , 'manage_options',  'rsmembers_settings', array(&$this, 'settings_page'), 'dashicons-universal-access' , 73 );
				

	}


	/**
	 * Output the settings page
	 */
	public function settings_page(){
		
		echo '<div class="wrap">', PHP_EOL;
		echo '<h2>'. rsmembers::PLUGIN_NAME . '</h2>', PHP_EOL;
		
		$this->public_html();
		
		echo '</div>', PHP_EOL;
	}
	
	/**
	 * Registers the scripts and styles used by the admin code
	 */
	public function register_scripts(){
				
		
		wp_enqueue_style( 'tab-component', $this->_plugin->get_assets_url('css/component.css'), array(), rsmembers::PLUGIN_VERSION );
		wp_enqueue_style( 'notify', $this->_plugin->get_assets_url('css/notify.css'), array(), rsmembers::PLUGIN_VERSION );
		wp_enqueue_style( 'tokenize', $this->_plugin->get_assets_url('css/selectivity-full.min.css'), array(), rsmembers::PLUGIN_VERSION );
		wp_enqueue_style( 'invmodal', $this->_plugin->get_assets_url('css/invmodal.css'), array(), rsmembers::PLUGIN_VERSION );
				
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-sortable' );				
		wp_enqueue_script( 'tab_script', $this->_plugin->get_assets_url('js/cbpFWTabs.js') );
		wp_enqueue_script( 'notify', $this->_plugin->get_assets_url('js/jquery-notify.js') );
		wp_enqueue_script( 'tokenize', $this->_plugin->get_assets_url('js/selectivity-full.min.js') );
		wp_enqueue_script( 'jquery.invmodal', $this->_plugin->get_assets_url('js/jquery.invmodal.js') );
			
	}
	

	public function public_html(){
		?>
		<div id="tabs" class="inv-tabs">
				<nav>
					<ul>
						<li><a href="#section-1" class="icon-settings"><span>Settings</span></a></li>
						<li><a href="#section-2" class="icon-field"><span>Required Field</span></a></li>
						<li><a href="#section-3" class="icon-message"><span>Required Message</span></a></li>
						<li><a href="#section-4" class="icon-letter"><span>News Letter</span></a></li>
                        <li><a href="#memberrole" class="icon-settings"><span>Member Role</span></a></li>
                        <li><a href="#socialmedia" class="icon-settings"><span>Social Media</span></a></li>
                                                
						<li><a href="#section-5" class="icon-paypal"><span>Payment Getway</span></a></li>
                        <?php 
						if(method_exists(RsMembersmailchimpAdmin, 'rsmembers_a_build_mailchimp')){
							?> 
							<li><a href="#mailchimp" class="icon-paypal"><span>Mailchimp</span></a></li>
							<?php	
						}							
						?>
                        <?php 
						if(method_exists(RsMembersaccountrenewAdmin, 'accountrenewoptions')){
							?> 
							<li><a href="#coupon" class="icon-paypal"><span>Account Renew</span></a></li>
							<?php	
						}							
						?>
                         
                        
                        
					</ul>
				</nav>
				<div class="inv-tabs-content">
					<section id="section-1">
						<div class="innersection">						
							<div class="inner_col1">	
                            	<h3 class="title">Settings</h3>
								<?php $this->plugin_settings();?>
                            </div>
                            <div class="inner_col2">	
                            	
                                
                                
                                <div class="inner_col_2">
                                    <h3 class="title">RS-Members News :</h3>
                                    <ul>
                                    <li>Version : <?php echo rsmembers::PLUGIN_VERSION;?></li>
                                    <li><a href="http://www.themexpo.net/wpplugins/rs-members/users-guide" target="_blank">Free Version Users Guide</a></li>
                                    <li><a href="http://themexpo.net/forums/forum/rs-member/" target="_blank">Support Forum</a></li>
                                    <li><a href="http://themexpo.net/product/rs-membership/" target="_blank">Paid Version User Guide</a></li>
                                    </ul>
                            	</div>
                                                                                            
                            	<div class="inner_col_2">
                                    <h3 class="title">Paid features :</h3>
                                    <ul>
                                    <li>* Paypal payment getway.</li>
                                    <li>* 2 checkouts.</li>
                                    <li>* Authorize.net </li>
                                    <li>* Mailchamp Newsletter.</li>
                                    <li>* Account Renew Configuration.</li>
                                    </ul>
                            	</div> 
                            
                            </div>
                            <div class="clr"></div>
                        </div>
					</section>
					<section id="section-2">
						<div class="innersection">						
                             <div class="inner_section">   
                                <h3 class="title">Required Field</h3>
                                <?php $this->required_field();?>
                            </div>    					
                        </div>
						<div class="innersection">						
							<div class="inner_section">
								<?php $this->required_field_new();?>
                            </div>
                        </div>						
					</section>
					<section id="section-3">
                        <div class="innersection">						
							<div class="inner_section">
                            	<h3 class="title">Required Message</h3>
								<?php $this->required_message();?>
                            </div>
                        </div>
					</section>
					<section id="section-4">
						<div class="innersection">		
                            <div class="inner_section">
                            	<h3 class="title">News Letter</h3>
								<?php $this->news_letter();?>   
                            </div>                        
                    	</div>    
					</section>
                    <section id="memberrole">
						<div class="innersection">		
                            <div class="inner_section">
                            	<h3 class="title">Manage Member Role</h3>
								<?php $this->member_role();?>   
                            </div>                        
                    	</div>    
					</section>
                    
                    <section id="socialmedia">
						<div class="innersection">		
                            <div class="inner_section">
                            	<h3 class="title">Social Media Connect</h3>
								<?php $this->socialmedia();?>   
                            </div>                        
                    	</div>    
					</section>
                    
					<section id="section-5">
						<div class="innersection">		
                        	<div class="inner_section">
                                <?php 
								if(method_exists(RsMemberspaymentgetwayAdmin, 'paymentgetwayoptions')){
									?> 
                                    <?php $this->_plugin->rsmembers_ajaxpost('payment_getway',"pgloaderdiv","pgloadingdiv", $this->_plugin->get_assets_url('images/loading2.gif'),"pgsubmitbtn","pgform_acction"); ?>
									<script type="text/javascript">
                                    function pgform_acction(msg){
                                        
                                    }           
                                    </script>
                                    <h3 class="title">Payment Getway</h3>
									<?php 
									RsMemberspaymentgetwayAdmin::paymentgetwayoptions();	
								}else{								
								?> 
                                <h3 class="title">Payment Getway</h3>                               
                                <div style="font-size:28px !important; line-height:40px; display:block; margin-bottom:20px;">1. Paypal payment getway.</div>
                                <div style="font-size:28px !important; line-height:40px; display:block; margin-bottom:20px;">2. 2 checkouts.</div>
                                <div style="font-size:28px !important; line-height:40px; display:block; margin-bottom:20px;">3. Authorize.net.</div>
                                
                                <?php 
								}								
								?>
                            </div>    
                        </div>
					</section>
                    <?php 
					if(method_exists(RsMembersmailchimpAdmin, 'rsmembers_a_build_mailchimp')){
					?>
                    <section id="mailchimp">
						<div class="innersection">		
                        	<div class="inner_section">                                
								<?php 
									RsMembersmailchimpAdmin::rsmembers_a_build_mailchimp();	
									echo RsMembersmailchimpAdmin::rsmembers_a_update_mailchimp();	
								?>
                            </div>    
                        </div>
					</section>
                    <?php	
					}							
					?>
                    
                    <?php 
					if(method_exists(RsMembersaccountrenewAdmin, 'accountrenewoptions')){
					?>
                    <section id="coupon">
						<div class="innersection">		
                        	<div class="inner_section">                                
								<?php $this->_plugin->rsmembers_ajaxpost('coupons',"codeloaderdiv","codeloadingdiv", $this->_plugin->get_assets_url('images/loading2.gif'),"codesubmitbtn","codeform_acction"); ?>
								<script type="text/javascript">
                                function codeform_acction(msg){
                                    
                                }           
                                </script>
                                <h3 class="title">Account Renew</h3> 
								<?php 
									RsMembersaccountrenewAdmin::accountrenewoptions();
								?>
                            </div>    
                        </div>
					</section>
                    <?php	
					}							
					?>
                    
				</div><!-- /content -->
			</div>			
			<script>new CBPFWTabs(document.getElementById("tabs"));</script>
			
		<?php		
	} // End Function
	
/*====================================================================
	Settings section
====================================================================*/		
	// config check security
	function rs_cc_security() {
		$points = 0;
		if (strpos(site_url(), "https://")) {
			$points += 2;
		}
		if (get_option('rs_hide_wordpress_login_form') == 1) {
			$points += 1;
		}
		if (240 > 0) {
			$points += 1;
		}
		if (get_option('rs_http_util_verify_ssl') == 1) {
			$points += 1;
		}
		if (get_option('rs_http_util') == 'curl') {
			$points += 1;
		}
		$points_max = 6;
		return floor(($points / $points_max) * 100);
	}
	
	// config check privacy
	function rs_cc_privacy() {
		$points = 0;
		if (240 > 0) {
			$points += 1;
		}
		// TODO: +1 for NOT using email address matching
		$points_max = 1;
		return floor(($points / $points_max) * 100);
	}
	
	// config check user experience
	function rs_cc_ux() {
		$points = 0;
		if (get_option('rs_logo_links_to_site') == 1) {
			$points += 1;
		}
		if (get_option('rs_show_login_messages') == 1) {
			$points += 1;
		}
		$points_max = 2;
		return floor(($points / $points_max) * 100);
	}
	
	function selected( $selectvalue, $checkedval ){
		if ( $selectvalue == $checkedval ) echo 'selected="selected"';
	}
	
	function checkedval( $selectvalue, $checkedval ){
		/*echo $selectvalue;
		echo $checkedvalval;*/
		
		if ( $selectvalue == $checkedval ) echo 'checked="checked"';
	}
	
	private function socialmedia(){
		// cache the config check ratings:
		$cc_security = $this->rs_cc_security();
		$cc_privacy = $this->rs_cc_privacy();
		$cc_ux = $this->rs_cc_ux();
		?>
		
<div class='rs-settings'>
	
	
	<!-- START Settings Body -->
	<div id="rs-settings-body">
	<!-- START Settings Column 2 -->
	
	<!-- END Settings Column 2 -->
	<!-- START Settings Column 1 -->
	<div id="rs-settings-col1" class="rs-settings-column">
		<form method='post' action='options.php'>
			<?php settings_fields('rs_settings'); ?>
			<?php do_settings_sections('rs_settings'); ?>
			<!-- START General Settings section -->
			<div id="rs-settings-section-general-settings" class="rs-settings-section">
			<h3>General Settings</h3>
			<div class='form-padding'>
			<table class='form-table'>
				<tr valign='top' class='has-tip' class="has-tip">
				<th scope='row'>Show login messages: <a href="#" class="tip-button">[?]</a></th>
				<td>
					<input type='checkbox' name='rs_show_login_messages' value='1' <?php $this->checkedval(get_option('rs_show_login_messages') , 1); ?> />
					<p class="tip-message">Shows a short-lived notification message to the user which indicates whether or not the login was successful, and if there was an error.</p>
				</td>
				</tr>
				
				<tr valign='top' class="has-tip">
				<th scope='row'>Login redirects to: <a href="#" class="tip-button">[?]</a></th>
				<td>
					<select name='rs_login_redirect'>
						<option value='home_page' <?php $this->selected(get_option('rs_login_redirect'), 'home_page'); ?>>Home Page</option>
						<option value='last_page' <?php $this->selected(get_option('rs_login_redirect'), 'last_page'); ?>>Last Page</option>
						<option value='specific_page' <?php $this->selected(get_option('rs_login_redirect'), 'specific_page'); ?>>Specific Page</option>
						<option value='admin_dashboard' <?php $this->selected(get_option('rs_login_redirect'), 'admin_dashboard'); ?>>Admin Dashboard</option>
						<option value='user_profile' <?php $this->selected(get_option('rs_login_redirect'), 'user_profile'); ?>>User's Profile Page</option>
						<option value='custom_url' <?php $this->selected(get_option('rs_login_redirect'), 'custom_url'); ?>>Custom URL</option>
					</select>
					<?php wp_dropdown_pages(array("id" => "rs_login_redirect_page", "name" => "rs_login_redirect_page", "selected" => get_option('rs_login_redirect_page'))); ?>
					<input type="text" name="rs_login_redirect_url" value="<?php echo get_option('rs_login_redirect_url'); ?>" style="display:none;" />
					<p class="tip-message">Specifies where to redirect a user after they log in.</p>
				</td>
				</tr>
				
			</table> <!-- .form-table -->
            <br>
            <input type="submit" value="Save all settings &raquo;" class="button button-primary" id="socialbtn" name="socialbtn">
			</div> <!-- .form-padding -->
			</div> <!-- .rs-settings-section -->
			<!-- END General Settings section -->
			
			
            
            <!-- START Login with Facebook section -->
			<div id="rs-settings-section-login-with-facebook" class="rs-settings-section">
			<h3>Login with Facebook</h3>
			<div class='form-padding'>
			<table class='form-table'>
				<tr valign='top'>
				<th scope='row'>Enabled:</th>
				<td>
					<input type='checkbox' name='rs_facebook_api_enabled' value='1' <?php $this->checkedval(get_option('rs_facebook_api_enabled') , 1); ?> />
				</td>
				</tr>
				
				<tr valign='top'>
				<th scope='row'>App ID:</th>
				<td>
					<input type='text' name='rs_facebook_api_id' value='<?php echo get_option('rs_facebook_api_id'); ?>' />
				</td>
				</tr>
				 
				<tr valign='top'>
				<th scope='row'>App Secret:</th>
				<td>
					<input type='text' name='rs_facebook_api_secret' value='<?php echo get_option('rs_facebook_api_secret'); ?>' />
				</td>
				</tr>
			</table> <!-- .form-table -->
			<p>
				<strong>Instructions:</strong>
				<ol>
					<li>Register as a Facebook Developer at <a href='https://developers.facebook.com/' target="_blank">developers.facebook.com</a>.</li>
					<li>At Facebook, create a new App. This will enable your site to access the Facebook API.</li>
					<li>At Facebook, provide your site's homepage URL (<?php echo $blog_url; ?>) for the new App's Redirect URI. Don't forget the trailing slash!</li>
					<li>Paste your App ID/Secret provided by Facebook into the fields above, then click the Save all settings button.</li>
				</ol>
			</p>
			<br>
            <input type="submit" value="Save all settings &raquo;" class="button button-primary" id="socialbtn" name="socialbtn">
			</div> <!-- .form-padding -->
			</div> <!-- .rs-settings-section -->
			<!-- END Login with Facebook section -->
			
			
            <!-- START Login with Twitter section -->
			<div id="rs-settings-section-login-with-twitter" class="rs-settings-section">
			<h3>Login with Twitter</h3>
			<div class='form-padding'>
			<table class='form-table'>
				<tr valign='top'>
				<th scope='row'>Enabled:</th>
				<td>
					<input type='checkbox' name='rs_twitter_api_enabled' value='1' <?php $this->checkedval(get_option('rs_twitter_api_enabled') , 1); ?> />
				</td>
				</tr>
				
				<tr valign='top'>
				<th scope='row'>App ID:</th>
				<td>
					<input type='text' name='rs_twitter_api_id' value='<?php echo get_option('rs_twitter_api_id'); ?>' />
				</td>
				</tr>
				 
				<tr valign='top'>
				<th scope='row'>App Secret:</th>
				<td>
					<input type='text' name='rs_twitter_api_secret' value='<?php echo get_option('rs_twitter_api_secret'); ?>' />
				</td>
				</tr>
			</table> <!-- .form-table -->
			<p>
				<strong>Instructions:</strong>
				<ol>
					<li>Register as a Twitter Developer at <a href='https://apps.twitter.com/' target="_blank">apps.twitter.com</a>.</li>
					<li>At Twitter, create a new App. This will enable your site to access the Twitter API.</li>
					<li>At Twitter, provide your site's homepage URL (<?php echo $blog_url; ?>) for the new App's Callback URL. Don't forget the trailing slash!</li>
					<li>Paste your App ID/Secret provided by Twitter into the fields above, then click the Save all settings button.</li>
				</ol>
			</p>
			<br>
            <input type="submit" value="Save all settings &raquo;" class="button button-primary" id="socialbtn" name="socialbtn">
			</div> <!-- .form-padding -->
			</div> <!-- .rs-settings-section -->
			<!-- END Login with Twitter section -->
            
            
            
            
            <!-- START Login with LinkedIn section -->
			<div id="rs-settings-section-login-with-linkedin" class="rs-settings-section">
			<h3>Login with LinkedIn</h3>
			<div class='form-padding'>
			<table class='form-table'>
				<tr valign='top'>
				<th scope='row'>Enabled:</th>
				<td>
					<input type='checkbox' name='rs_linkedin_api_enabled' value='1' <?php $this->checkedval(get_option('rs_linkedin_api_enabled') , 1); ?> />
				</td>
				</tr>
				
				<tr valign='top'>
				<th scope='row'>API Key:</th>
				<td>
					<input type='text' name='rs_linkedin_api_id' value='<?php echo get_option('rs_linkedin_api_id'); ?>' />
				</td>
				</tr>
				 
				<tr valign='top'>
				<th scope='row'>Secret Key:</th>
				<td>
					<input type='text' name='rs_linkedin_api_secret' value='<?php echo get_option('rs_linkedin_api_secret'); ?>' />
				</td>
				</tr>
			</table> <!-- .form-table -->
			<p>
				<strong>Instructions:</strong>
				<ol>
					<li>Register as a LinkedIn Developer at <a href='https://developers.linkedin.com/' target="_blank">developers.linkedin.com</a>.</li>
					<li>At LinkedIn, create a new App. This will enable your site to access the LinkedIn API.</li>
					<li>At LinkedIn, provide your site's homepage URL (<?php echo $blog_url; ?>) for the new App's Redirect URI. Don't forget the trailing slash!</li>
					<li>Paste your API Key/Secret provided by LinkedIn into the fields above, then click the Save all settings button.</li>
				</ol>
			</p>
			<br>
            <input type="submit" value="Save all settings &raquo;" class="button button-primary" id="socialbtn" name="socialbtn">
			</div> <!-- .form-padding -->
			</div> <!-- .rs-settings-section -->
			<!-- END Login with LinkedIn section -->
			
			
            
            <!-- START Login with Google section -->
			<div id="rs-settings-section-login-with-google" class="rs-settings-section">
			<h3>Login with Google</h3>
			<div class='form-padding'>
			<table class='form-table'>
				<tr valign='top'>
				<th scope='row'>Enabled:</th>
				<td>
					<input type='checkbox' name='rs_google_api_enabled' value='1' <?php $this->checkedval(get_option('rs_google_api_enabled') , 1); ?> />
				</td>
				</tr>
				
				<tr valign='top'>
				<th scope='row'>Client ID:</th>
				<td>
					<input type='text' name='rs_google_api_id' value='<?php echo get_option('rs_google_api_id'); ?>' />
				</td>
				</tr>

				<tr valign='top'>
				<th scope='row'>Client Secret:</th>
				<td>
					<input type='text' name='rs_google_api_secret' value='<?php echo get_option('rs_google_api_secret'); ?>' />
				</td>
				</tr>
			</table> <!-- .form-table -->
			<p>
				<strong>Instructions:</strong>
				<ol>
					<li>Visit the Google website for developers <a href='https://console.developers.google.com/project' target="_blank">console.developers.google.com</a>.</li>
					<li>At Google, create a new Project and enable the Google+ API. This will enable your site to access the Google+ API.</li>
					<li>At Google, provide your site's homepage URL (<?php echo $blog_url; ?>) for the new Project's Redirect URI. Don't forget the trailing slash!</li>
					<li>At Google, you must also configure the Consent Screen with your Email Address and Product Name. This is what Google will display to users when they are asked to grant access to your site/app.</li>
					<li>Paste your Client ID/Secret provided by Google into the fields above, then click the Save all settings button.</li>
				</ol>
			</p>
			<br>
            <input type="submit" value="Save all settings &raquo;" class="button button-primary" id="socialbtn" name="socialbtn">
			</div> <!-- .form-padding -->
			</div> <!-- .rs-settings-section -->
			<!-- END Login with Google section -->
			
			
			
			
			
			<!-- START Login with Reddit section -->
			<div id="rs-settings-section-login-with-reddit" class="rs-settings-section">
			<h3>Login with Reddit</h3>
			<div class='form-padding'>
			<table class='form-table'>
				<tr valign='top'>
				<th scope='row'>Enabled:</th>
				<td>
					<input type='checkbox' name='rs_reddit_api_enabled' value='1' <?php $this->checkedval(get_option('rs_reddit_api_enabled') , 1); ?> />
				</td>
				</tr>
				
				<tr valign='top'>
				<th scope='row'>Client ID:</th>
				<td>
					<input type='text' name='rs_reddit_api_id' value='<?php echo get_option('rs_reddit_api_id'); ?>' />
				</td>
				</tr>
				 
				<tr valign='top'>
				<th scope='row'>Client Secret:</th>
				<td>
					<input type='text' name='rs_reddit_api_secret' value='<?php echo get_option('rs_reddit_api_secret'); ?>' />
				</td>
				</tr>
			</table> <!-- .form-table -->
			<p>
				<strong>Instructions:</strong>
				<ol>
					<li>Register as a Reddit Developer at <a href='https://ssl.reddit.com/prefs/apps' target="_blank">ssl.reddit.com/prefs/apps</a>.</li>
					<li>At Reddit, create a new App. This will enable your site to access the Reddit API.</li>
					<li>At Reddit, provide your site's homepage URL (<?php echo $blog_url; ?>) for the new App's Redirect URI. Don't forget the trailing slash!</li>
					<li>Paste your Client ID/Secret provided by Reddit into the fields above, then click the Save all settings button.</li>
				</ol>
			</p>
			<br>
            <input type="submit" value="Save all settings &raquo;" class="button button-primary" id="socialbtn" name="socialbtn">
			</div> <!-- .form-padding -->
			</div> <!-- .rs-settings-section -->
			<!-- END Login with Reddit section -->
			
			<!-- START Login with Windows Live section -->
			<div id="rs-settings-section-login-with-windowslive" class="rs-settings-section">
			<h3>Login with Windows Live</h3>
			<div class='form-padding'>
			<table class='form-table'>
				<tr valign='top'>
				<th scope='row'>Enabled:</th>
				<td>
					<input type='checkbox' name='rs_windowslive_api_enabled' value='1' <?php $this->checkedval(get_option('rs_windowslive_api_enabled') , 1); ?> />
				</td>
				</tr>
				
				<tr valign='top'>
				<th scope='row'>Client ID:</th>
				<td>
					<input type='text' name='rs_windowslive_api_id' value='<?php echo get_option('rs_windowslive_api_id'); ?>' />
				</td>
				</tr>
				 
				<tr valign='top'>
				<th scope='row'>Client Secret:</th>
				<td>
					<input type='text' name='rs_windowslive_api_secret' value='<?php echo get_option('rs_windowslive_api_secret'); ?>' />
				</td>
				</tr>
			</table> <!-- .form-table -->
			<p>
				<strong>Instructions:</strong>
				<ol>
					<li>Register as a Windows Live Developer at <a href='https://manage.dev.live.com' target="_blank">manage.dev.live.com</a>.</li>
					<li>At Windows Live, create a new App. This will enable your site to access the Windows Live API.</li>
					<li>At Windows Live, provide your site's homepage URL (<?php echo $blog_url; ?>) for the new App's Redirect URI. Don't forget the trailing slash!</li>
					<li>Paste your Client ID/Secret provided by Windows Live into the fields above, then click the Save all settings button.</li>
				</ol>
			</p>
			<br>
            <input type="submit" value="Save all settings &raquo;" class="button button-primary" id="socialbtn" name="socialbtn">
			</div> <!-- .form-padding -->
			</div> <!-- .rs-settings-section -->
			<!-- END Login with Windows Live section -->

			

			<!-- START Login with Instagram section -->
			<div id="rs-settings-section-login-with-instagram" class="rs-settings-section">
			<h3>Login with Instagram</h3>
			<div class='form-padding'>
			<table class='form-table'>
				<tr valign='top'>
				<th scope='row'>Enabled:</th>
				<td>
					<input type='checkbox' name='rs_instagram_api_enabled' value='1' <?php $this->checkedval(get_option('rs_instagram_api_enabled') , 1); ?> />
				</td>
				</tr>
				
				<tr valign='top'>
				<th scope='row'>Client ID:</th>
				<td>
					<input type='text' name='rs_instagram_api_id' value='<?php echo get_option('rs_instagram_api_id'); ?>' />
				</td>
				</tr>
				 
				<tr valign='top'>
				<th scope='row'>Client Secret:</th>
				<td>
					<input type='text' name='rs_instagram_api_secret' value='<?php echo get_option('rs_instagram_api_secret'); ?>' />
				</td>
				</tr>
			</table> <!-- .form-table -->
			<p>
				<strong>Instructions:</strong>
				<ol>
					<li>NOTE: Instagram's developer signup requires a valid cell phone number.</li>
					<li>At Instagram, register as an <a href='http://instagram.com/developer/authentication/' target="_blank">Instagram Developer</a>.</li>
					<li>At Instagram, after signing up/in, click <a href='http://instagram.com/developer/clients/manage/'>Manage Clients</a>.</li>
					<li>At Instagram, click <a href="http://instagram.com/developer/clients/register/">Register a New Client</a>. This will enable your site to access the Instagram API.</li>
					<li>At Instagram, provide your site's homepage URL (<?php echo $blog_url; ?>) for the <em>OAuth redirect_uri</em>. Don't forget the trailing slash!</li>
					<li>At Instagram, copy the <em>Client ID/Client Secret</em> provided by Instagram and paste them into the fields above, then click the Save all settings button.</li>
				</ol>
				<strong>References:</strong>
				<ul>
					<li><a href='http://instagram.com/developer/authentication/'>Instagram Developer Reference - Authentication</a></li>
				</ul>
			</p>
			<br>
            <input type="submit" value="Save all settings &raquo;" class="button button-primary" id="socialbtn" name="socialbtn">
			</div> <!-- .form-padding -->
			</div> <!-- .rs-settings-section -->
			<!-- END Login with Instagram section -->
			
			
			
			
		</form> <!-- form -->
	</div>
	<!-- END Settings Column 1 -->
	</div> <!-- #rs-settings-body -->
	<!-- END Settings Body -->
</div> <!-- .wrap .rs-settings -->

		
		<?php
	}
/*====================================================================
	Settings section
====================================================================*/	
	private function plugin_settings(){
		
		$rsmembers_settings  = get_option( 'rsmembers_settings' );	
		$rsmembers_custom  = get_option( 'rsmembers_custom' );	
		$rsmembers_registration_type  = get_option( 'rsmembers_registration_type' );
		
		$rsmembers_settings_arr = array(
			__( "Notify email at [ <strong>" . get_option( 'admin_email' )."</strong> ] for each new registration.", 'rsmembers' ),
			__( "Holds new registrations for admin approval.", 'rsmembers' ),
			__( "Set the account free No/Yes", 'rsmembers' ),
			__( "Enter trial period days", 'rsmembers' ),
			__( "", 'rsmembers' ),
			__( "", 'rsmembers' ),
			__( "", 'rsmembers' ),
		);
		
		?>		
        <?php $this->_plugin->rsmembers_ajaxpost('rsmembers_settings',"psloaderdiv","psloadingdiv", $this->_plugin->get_assets_url('images/loading2.gif'),"pssubmitbtn","psform_acction"); ?>
		<script type="text/javascript">
        function psform_acction(msg){
            
        }           
        </script>                
        <form action="<?php echo $_SERVER['REQUEST_URI']?>" name="rsmembers_settings" id="rsmembers_settings" method="post" enctype="multipart/form-data">
                <input type="hidden" name="caseselect" value="plugin_settings">
                <?php
				for( $row = 0; $row < 8 ; $row++ ) { 
					if($rsmembers_settings[$row][2]=='faccount' and $this->_plugin->getpaypal()<1 ){
						?>
                        <div class="form-inner15" style="display:none">
                        	<input type="hidden" name="<?php echo $rsmembers_settings[$row][2];?>" id="<?php echo $rsmembers_settings[$row][2];?>" value="on">
                        </div>
						<?php
					}else if($rsmembers_settings[$row][2]=='captcha'){
						?>
                        <div class="form-inner15" style="display:none">
                        	<input type="hidden" name="<?php echo $rsmembers_settings[$row][2];?>" id="<?php echo $rsmembers_settings[$row][2];?>" value="">
                        </div>
						<?php
					}else{
						?>
                        <div class="form-inner15">
                            <div class="left-col"><?php echo $rsmembers_settings[$row][1]; ?></div>
                            <div class="right-col">
                                <?php echo $this->_plugin->library->formcontrol( $rsmembers_settings[$row][2], $rsmembers_settings[$row][2], $rsmembers_settings[$row][3], $rsmembers_settings[$row][4], '', '', '', 0 );?>
                                <div class="clr"></div>
                                <div class="r-c-note"><?php echo $rsmembers_settings_arr[$row];?></div>
                            </div>
                            <div class="clr"></div>
                        </div>
						<?php 
					}
				} 
				?>
                                
                <div class="form-inner15">
                    <div class="left-col">Terms & Condition Page</div>
                    <div class="right-col">
                        <?php
						 $args = array(
							'authors'      => '',
							'child_of'     => 0,
							'date_format'  => get_option('date_format'),
							'depth'        => 0,
							'echo'         => 1,
							'exclude'      => '',
							'include'      => '',
							'link_after'   => '',
							'link_before'  => '',
							'post_type'    => 'page',
							'post_status'  => 'publish',
							'show_date'    => '',
							'sort_column'  => 'post_title',
							'sort_order'   => '',
							'title_li'     => __('Pages')
						); 
						$pages = get_pages( $args ); 
						?>
						<select name="termcondi" id="termcondi" class="select-control">
                            <option value="">Select Page</option>
                            <?php						
                            foreach ($pages as $page) {
                                ?>
                                <option <?php if($rsmembers_settings[8][4]==$page->ID) echo'selected';?>  value="<?php echo $page->ID;?>"><?php echo $page->post_title;?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <div class="clr"></div>
                        <div class="r-c-note">Select Term & Condition page.</div>
                    </div>
                    <div class="clr"></div>
                </div>
                
                <div class="form-inner15">
                    <div class="left-col">Registration Type</div>
                    <div class="right-col">                        
						
                        <select name="rsmembers_registration_type" id="rsmembers_registration_type" class="select-control">
                            <option <?php if($rsmembers_registration_type=='registrationform') echo'selected';?> value="registrationform">Using Registration Form</option>
                            <option <?php if($rsmembers_registration_type=='socialmedia') echo'selected';?> value="socialmedia">Using Social Media</option>
                            <option <?php if($rsmembers_registration_type=='both') echo'selected';?> value="both">Registration Form + Social Media</option>                            
                        </select>
                        <div class="clr"></div>
                        <div class="r-c-note">Select Registration Type.</div>
                    </div>
                    <div class="clr"></div>
                </div>
                
                
                <div class="form-inner15">
                    <div class="left-col">Custom CSS</div>
                    <div class="right-col">
                        <textarea name="custom_css" id="custom_css" style="width:100%; height:200px; padding:10px;"><?php echo $rsmembers_custom[0][1];?></textarea>
                        <div class="clr"></div>
                        <div class="r-c-note"><strong>Ex:</strong> html{-webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; height:100%;} 
	body{margin:0px; font-size:13px; font-family: 'Open Sans', sans-serif; font-weight:300;}</div>
                    </div>
                    <div class="clr"></div>
                </div>
                
                
                
                <div class="form-inner15">
                    <div class="left-col">CSV Download</div>
                    <div class="right-col">
                        <a class="csvdownload" href="javascript:void(0)" dataurl="<?php echo $_SERVER['REQUEST_URI']?>">Download</a>
                        <div class="clr"></div>
                        <div class="r-c-note">Click [ <strong>Download</strong> ] to backup your data.</div>
                    </div>
                    <div class="clr"></div>
                </div>
                
                <div class="form-inner15">
                    <div class="left-col">&nbsp;</div>
                    <div class="right-col" id="psloaderdiv"><input type="submit" value="Save Changes" class="button button-primary" id="pssubmitbtn" name="pssubmitbtn"></div>
                    <div class="clr"></div>
                </div>
        </form>        			
		<?php
	} // End Function	


/*====================================================================
	News letter section
====================================================================*/
	private function news_letter(){
		?>
         <?php $this->_plugin->rsmembers_ajaxpost('form_news_letter',"nlloaderdiv","nlloadingdiv", $this->_plugin->get_assets_url('images/loading2.gif'),"nlsubmitbtn","nlform_acction"); ?>
		<script type="text/javascript">
        function nlform_acction(msg){
            
        }           
        </script>                
        <form action="<?php echo $_SERVER['REQUEST_URI']?>" name="form_news_letter" id="form_news_letter" method="post" enctype="multipart/form-data">
            <input type="hidden" name="caseselect" value="news_letter">   
            <div class="form-inner15">
                <div class="left-col">To</div>
                <div class="right-col">
                    <script type="text/javascript">
                    jQuery(document).ready(function() {	 
                        jQuery('#multiple-select').selectivity();
                    });
                    </script>
                    <select id="multiple-select" class="selectivity-input" data-placeholder="Type to search user" name="traditional[]" multiple>
                    	<?php
						$blogusers = get_users( 'orderby=role' );
						foreach ( $blogusers as $user ) {
							?>
                        	<option value="<?php echo esc_html( $user->user_email );?>"><?php echo esc_html( $user->user_email );?></option>
							<?php
                        }
                        ?>
                	</select>
                    <div class="clr"></div>
                    <div class="r-c-note"></div>
                </div>
                <div class="clr"></div>
            </div>
            <div class="form-inner15">
                <div class="left-col">Subject</div>
                <div class="right-col">
                    <input id="subject" name="subject" type="text" class="text-control">
                    <div class="clr"></div>
                    <div class="r-c-note"></div>
                </div>
                <div class="clr"></div>
            </div>
            <div class="form-inner15">
                <div class="left-col">Message</div>
                <div class="right-col">
                    <textarea id="message" name="message" rows="6" class="textarea-control"  style=""></textarea>
                    <div class="clr"></div>
                    <div class="r-c-note"></div>
                </div>
                <div class="clr"></div>
            </div>
            <div class="form-inner15">
                <div class="left-col">&nbsp;</div>
                <div class="right-col" id="nlloaderdiv"><input type="submit" value="Send News letter" class="button button-primary" id="nlsubmitbtn" name="nlsubmitbtn"></div>
                <div class="clr"></div>
            </div>
		</form>    
		<?php	
	}

/*====================================================================
	Manage Member Role section
====================================================================*/
	private function member_role(){
		?>
         <?php $this->_plugin->rsmembers_ajaxpost('form_member_role',"mrloaderdiv","mrloadingdiv", $this->_plugin->get_assets_url('images/loading2.gif'),"mrsubmitbtn","mrform_acction"); ?>
		<script type="text/javascript">
        function mrform_acction(msg){
            
        }           
        </script>                
        <form action="<?php echo $_SERVER['REQUEST_URI']?>" name="form_member_role" id="form_member_role" method="post" enctype="multipart/form-data">
            <input type="hidden" name="caseselect" value="member_role">   
            
            
            <table class="widefat" id="wpmem-fields">
					<thead><tr class="head">
						<th scope="col"><?php _e( 'User Name', 'rsmembers' ); ?></th>
                        <th scope="col"><?php _e( 'Name', 'rsmembers' ); ?></th>
                        <th scope="col"><?php _e( 'Role',  'rsmembers' ); ?></th>                                             
                        <th scope="col"><?php _e( 'Posts', 'rsmembers' ); ?></th>
					</tr></thead>                	
                    <?php
                    $blogusers = get_users( 'orderby=role' );
					$class = '';
					foreach ( $blogusers as $user ) {
						$class = ( $class == 'alternate' ) ? '' : 'alternate'; ?>
						<tr class="<?php echo $class; ?>" valign="top" id="">
                            <td><?php echo esc_html( $user->user_login );?><input type="hidden" name="userid[]" value="<?php echo $user->ID;?>"></td>
                            <td><?php echo esc_html( $user->display_name );?></td>
                            <td><?php $this->userrole( $user->ID );?></td>
                            <td><?php echo count_user_posts( $user->ID , 'post' );?></td>                            
                        </tr>
                        <?php
					}
					?>
                </table>
            
            
            <div class="form-worp">
            	<br><div class="inner-block">
                	
                	<div style="margin-left:30px;" class="full-block" id="mrloaderdiv">
                    <input type="submit" value="<?php _e( 'Update Member Role', 'rsmembers' ); ?> &raquo;" class="button button-primary" id="mrsubmitbtn" name="mrsubmitbtn">
                    </div>
                </div>            	
            	<div class="clr"></div>
            </div>
            
		</form>    
		<?php	
	}
	
	private function userrole($id){		      	
		if($id==1){
			?>		
			-<input type="hidden" id="role<?php echo $id;?>" name="role[]" value="administrator">
			<?php
		}else{
			$user = new WP_User($id);
			$role = array_shift($user -> roles);
			?>		
			<select id="role<?php echo $id;?>" name="role[]">
			<option <?php if($role=='subscriber') echo'selected="selected"';?> value="subscriber">Subscriber</option>
			<option <?php if($role=='contributor') echo'selected="selected"';?> value="contributor">Contributor</option>
			<option <?php if($role=='author') echo'selected="selected"';?> value="author">Author</option>
			<option <?php if($role=='editor') echo'selected="selected"';?> value="editor">Editor</option>
			<option <?php if($role=='administrator') echo'selected="selected"';?> value="administrator">Administrator</option>
			<option <?php if($role=='') echo'selected="selected"';?> value="">&mdash; No role for this site &mdash;</option>
			</select>		
			<?php
		}
	}
	
/*====================================================================
	Field list section
====================================================================*/
	private function required_field(){		
		$rsmembers_fields = get_option( 'rsmembers_fieldoptions' );
		?>
		<?php $this->_plugin->rsmembers_ajaxpost('updatefieldform',"ffloaderdiv","ffloadingdiv", $this->_plugin->get_assets_url('images/loading2.gif'),"ffsubmitbtn","ffform_acction"); ?>
		<script type="text/javascript">
        function ffform_acction(msg){
           
        }          
        </script>       
        <form name="updatefieldform" id="updatefieldform" method="post" action="<?php echo $_SERVER['REQUEST_URI']?>" enctype="multipart/form-data">			
			<div>
				<table class="widefat" id="image_sort" style="table-layout:inherit;">
					<thead><tr class="head" style="background-color:#e2e2e1;">
						<th scope="col"><?php _e( 'Field Name', 'rsmembers' ); ?></th>
                        <th scope="col"><?php _e( 'Option Name', 'rsmembers' ); ?></th>
                        <th scope="col"><?php _e( 'Field Type',  'rsmembers' ); ?></th>
                        <th scope="col"><?php _e( 'Action',  'rsmembers' ); ?></th>
                        <th scope="col"><?php _e( 'On registration area?',    'rsmembers' ); ?></th>
					</tr></thead>
                    <tbody id="requiredfieldnew">
				<?php				
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
				} ?>
                </tbody>
				</table>
                </div> 
                <p style="width:98%; margin:0px 1%;" class="submit" id="ffloaderdiv"><input type="submit" value="<?php _e( 'Update Fields', 'rsmembers' ); ?> &raquo;" class="button button-primary" id="ffsubmitbtn" name="ffsubmitbtn"></p><br /> 
			</form>	
           
			<script type="text/javascript">
           	jQuery(document).ready(function(){
                jQuery('table#image_sort tbody').sortable({
                    axis: 'y',
                    update: function (event, ui) {
                        var post_url = '<?php echo $_SERVER['REQUEST_URI']?>';            
                        // POST to server using jQuery.post or jQuery.ajax
                        jQuery.ajax({
                            data: jQuery("#updatefieldform").serialize(),
                            type: 'POST',
                            url: post_url				
                        });
                    }
                });	
            });
            function setfieldvilue(fieldcheckbox,fieldrequired){
                if(document.getElementById(fieldcheckbox).checked) {
                    jQuery("#"+fieldrequired).val('on');
                } else {
                    jQuery("#"+fieldrequired).val('');
                }
            }
            </script>
            <script type="text/javascript">
			
			function editfields(fieldsid){
				jQuery('#thumb0').html('');
				ajax_state('<?php echo $_SERVER['REQUEST_URI']?>&type=editfields&fieldsid='+ fieldsid ,"thumb0");				
				jQuery('a.poplight').trigger("click");
			}	
			function deletefields(fieldsid){
				jQuery('#thumb0').html('');
				delcon=confirm('Are you want to delete ??');
				if(delcon){						
					ajax_state('<?php echo $_SERVER['REQUEST_URI']?>&type=deletefields&fieldsid='+ fieldsid ,"requiredfieldnew");
				}
			}										
			</script>
            
			<a href="#thumb0" class="poplight"></a>
			<div id="thumb0" class="popup_block" style="height:450px;">
				  <!--<div class="btn_close"></div>-->
				  <div class="thumb-text"></div>
				  <div class="clr"></div>    
			 </div>
		<?php 
	} // End function
	

/*====================================================================
	New Field section
====================================================================*/	
	private function required_field_new(){		
		
		$this->_plugin->rsmembers_ajaxpost('newfieldform',"nffloaderdiv","nffloadingdiv", $this->_plugin->get_assets_url('images/loading2.gif'),"nffsubmitbtn","ffform_acction"); ?>
		<script type="text/javascript">
        function ffform_acction(msg){
           	if(msg=='Added successfully'){									
				jQuery('#FieldName').val('');				
				ajax_state('<?php echo $_SERVER['REQUEST_URI']?>&caseselect=field_list_form',"requiredfieldnew");	
			}
        }          
        </script>
        
        <form name="newfieldform" id="newfieldform" method="post" action="<?php echo $_SERVER['REQUEST_URI']?>">
			<input type="hidden" name="caseselect" value="field_new_form">	
			<input type="hidden" value="" id="field_name_action" name="field_name_action">	
                <h3 class="title">New Fields Info</h3><br>
                
                <div class="form-inner15">
                    <div class="left-col">Field Name</div>
                    <div class="right-col">
                        <input id="FieldName" name="FieldName" type="text" class="text-control">
                        <div class="clr"></div>
                        <div class="r-c-note"></div>
                    </div>
                    <div class="clr"></div>
                </div>
                <div class="form-inner15">
                    <div class="left-col">Field Type</div>
                    <div class="right-col">
                        <select name="FieldType" id="FieldType" class="select-control">
                        	<option value="text">Text</option>
                            <option value="textarea">Textarea</option>
                        	<option value="password">Password</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="select">Drop Down</option>
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
                        <textarea name="selectval" id="selectval" class="text-control" rows="5"></textarea>
                        <div class="clr"></div>
                        <div class="r-c-note"> Options should be Option Name,option_value| <br><strong>Ex:</strong> <---- Select One ---->,|Position One,1|Position Two,2|Position Three,3|Position Four,4</div>
                    </div>
                    <div class="clr"></div>
                </div>
                                
                <h3 class="title">Field Validation Rules</h3><br>
                <div class="form-inner15">
                    <div class="left-col">Required</div>
                    <div class="right-col">
                        <input name="required" id="required" type="checkbox" class="cmn-toggle cmn-toggle-round"/>
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
                            <option value="numeric">Numeric Value</option>
                        	<option value="email">Email</option>
                            <option value="date">Date</option>
                            <option value="website">Website</option>
                        </select>
                        <div class="clr"></div>
                        <div class="r-c-note"></div>
                    </div>
                    <div class="clr"></div>
                </div>
                
                <div class="form-inner15">
                    <div class="left-col">Maximum length</div>
                    <div class="right-col">
                        <input id="maxlen" name="maxlen" type="text" class="text-control">
                        <div class="clr"></div>
                        <div class="r-c-note"></div>
                    </div>
                    <div class="clr"></div>
                </div>
                
                <div class="form-inner15">
                    <div class="left-col">Minimum length</div>
                    <div class="right-col">
                        <input id="minlen" name="minlen" type="text" class="text-control">
                        <div class="clr"></div>
                        <div class="r-c-note"></div>
                    </div>
                    <div class="clr"></div>
                </div>
                
                
                <div class="form-inner15">
                    <div class="left-col">&nbsp;</div>
                    <div class="right-col" id="nffloaderdiv">
                        <input type="submit" value="<?php _e( 'New Fields', 'rsmembers' ); ?> &raquo;" class="button button-primary" id="nffsubmitbtn" name="nffsubmitbtn">
                        <div class="clr"></div>
                        <div class="r-c-note"></div>
                    </div>
                    <div class="clr"></div>
                </div>
		</form>	
            
		<?php 
	} // End function

/*====================================================================
	Required Message section
====================================================================*/	
	private function required_message(){
		$rsmembers_messageoptions  = get_option( 'rsmembers_messageoptions' );
		$this->_plugin->rsmembers_ajaxpost('rm_form',"rmloaderdiv","rmloadingdiv", $this->_plugin->get_assets_url('images/loading2.gif'),"rmsubmitbtn","rmform_acction"); ?>
		<script type="text/javascript">
        function rmform_acction(msg){
            
        }           
        </script>
        <form name="rm_form" id="rm_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']?>" enctype="multipart/form-data">
		<input type="hidden" name="caseselect" value="required_message">
		<?php
		for( $row = 0; $row < count( $rsmembers_messageoptions ); $row++ ) { ?>
			<div class="form-inner15">
				<div class="left-col"><?php echo $rsmembers_messageoptions[$row][0];?></div>
				<div class="right-col">
					<textarea name="<?php echo "rmessage_".$row; ?>" id="" rows="3" class="textarea-control"><?php echo stripslashes( $rsmembers_messageoptions[$row][1] ); ?></textarea>
					<div class="clr"></div>
					<div class="r-c-note"></div>
				</div>
				<div class="clr"></div>
			</div>
		<?php } 
		?>
        	<div class="form-inner15">
				<div class="left-col">&nbsp;</div>
				<div class="right-col">
					<p class="submit" id="rmloaderdiv"><input type="submit" value="Save Changes" class="button button-primary" id="rmsubmitbtn" name="rmsubmitbtn"></p>
					<div class="clr"></div>
					<div class="r-c-note"></div>
				</div>
				<div class="clr"></div>
			</div>        
        </form>     
		<?php
	} // End function
	
}	//End Class

// EOF