<?php
/*
  Plugin Name:Facebook Messenger Chat For WordPress
  Plugin URI:http://www.cooltimeline.com
  Description:Cool facebook messenger plugin will add live facebook chat support messenger button on your wordpress pages anywhere using shortcode.
  Version:1.0.4
  Author:narinder-singh
  Author URI:http://www.cooltimeline.com
  License: GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Domain Path: /languages
  Text Domain:cool-fb-chat
 */

/** Configuration * */
if ( ! defined( 'ABSPATH' ) ){
	exit;
}
if (!defined('COOL_FB_CHAT')){
	define('COOL_FB_CHAT_VERSION_CURRENT', '1.0.4');
	define('COOL_FB_CHAT_URL', plugin_dir_url(__FILE__));
	define('COOL_FB_CHAT_DIR', plugin_dir_path(__FILE__));
}
if (!class_exists('COOL_FB_CHAT')) {

	class COOL_FB_CHAT {

		/**
		 * Construct the plugin objects
		 */
		public function __construct() {
			// Installation and uninstallation hooks
			register_activation_hook(__FILE__, array($this, 'activate'));
			register_deactivation_hook(__FILE__, array($this, 'deactivate'));
			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin", array($this, 'plugin_settings_link'));

				if ( is_admin() ){ // admin actions
				add_action( 'admin_menu',array($this, 'cool_fb_chat_create_page') );
				add_action( 'admin_enqueue_scripts',array($this, 'cool_fb_admin_styles'));

			} else {
				// non-admin enqueues, actions, and filters
				add_action( 'wp_enqueue_scripts', array($this,'cool_fb_scripts' ));
				add_action( 'wp_footer', array($this,'fb_messenger'), 10 );

			}
			add_shortcode( 'cool-fb-chat',  array($this,'cool_fb_chat_shortcode'));
		}

		function cool_fb_chat_create_page() {
			global $cool_fb_page;
			//create new top-level menu

			$cool_fb_page=	add_options_page('Cool FB Chat', 'Cool FB Chat', 'administrator', 'cool-fb-settings',  array( $this,'cool_fb_plugin_settings_page'), 'dashicons-admin-generic');
			//call register settings function
			add_action( 'admin_init',array($this, 'cool_fb_plugin_settings' ));
		}
		// Add the settings link to the plugins page
		function plugin_settings_link($links) {
			$settings_link = '<a href="options-general.php?page=cool-fb-settings">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}
		function cool_fb_admin_styles($hook) {
			global $cool_fb_page;
			if   ( $hook == $cool_fb_page ) {
				wp_enqueue_style('fb_boostrap_styles',COOL_FB_CHAT_URL.'css/bootstrap.min.css');

			}
		}

		function cool_fb_plugin_settings() {
			//register our settings
			register_setting( 'cool-fb-msz-settings-group', 'fb_page_url' );
			register_setting( 'cool-fb-msz-settings-group', 'hide_btn' );
			register_setting( 'cool-fb-msz-settings-group', 'btn_text' );
		}

		function cool_fb_plugin_settings_page() {
			?>
			<div class="wrap container" style="background:#fff">
				<h1>Cool FB Chat</h1>
				<hr>
				<div class="">
					<h4>#1: Please add your Facebook Page URL in below added field.</h4>
					<img width="50%" src="<?php echo COOL_FB_CHAT_URL ;?>/images/page-url.png">
					<hr>
					<h4>#2: Enable Messages on Your Page</h4>
					<strong>Your page can accept and send messages only if you’ve enabled Messenger.</strong>
					<br>
					<strong>Please turn on Messenger for your page.</strong>
					<br>
					<ul>
						<li>Go to Messages under General Settings </li>
						<li>and then click Edit </li>
						<li>Select the option to allow messages </li>
						<li>to your page, and <strong>click Save Changes</strong> </li>
					</ul>
					<img src="<?php echo COOL_FB_CHAT_URL ;?>/images/ms-messages-settings.png">
				</div>

				<br>
				<hr>
				<form  class="form-horizontal" method="post" action="options.php">
					<?php settings_fields( 'cool-fb-msz-settings-group' ); ?>
					<?php do_settings_sections( 'cool-fb-msz-settings-group' ); ?>
					<div class="form-group">
						<div class="col-sm-2">
							<label  for="fb_page_url">Facebook Page URL</label>
						</div>
						<div class="col-sm-10">
							<input  class="fb_page_url form-control" type="text" name="fb_page_url" value="<?php echo esc_attr( get_option('fb_page_url') ); ?>">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-2">
							<label  for="fb_page_url">Button Text</label>
						</div>
						<div class="col-sm-10">
							<input  class="fb_page_url form-control" type="text" name="btn_text" value="<?php echo esc_attr( get_option('btn_text') ); ?>">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-2">
							<label for="exampleInputPassword1">Hide button</label>
						</div>
						<div class="col-sm-10">
							<input name="hide_btn" type="checkbox" value="true" <?php checked( 'true', get_option( 'hide_btn' ) ); ?> />
						</div>
					</div>	<div class="col-sm-2">
					</div>
					<div class="col-sm-10"><?php submit_button(); ?>
					</div>
				</form>

				<div class="col-sm-4">
					<strong>Use anywhere using shortcode</strong>
					<pre>[cool-fb-chat]</pre>
				</div>
				<div class="clearfix"></div>
				<!--<p>
					Plugin developed by <a  target="_blank" href="http://cooltimeline.com">CoolHappy</a>
				</p> -->
				
				</div>
		<?php }

		// Add Shortcode
		function cool_fb_chat_shortcode() {
			$btn_txt=!empty(get_option( 'btn_text'))?get_option( 'btn_text'):'Message Us';
			return '<a class="cool-fb-msz-button-sh" href="#cool-fb-chat">'.$btn_txt.'</a>';
		}

		/**
		 * Activate the plugin
		 */
		public function activate() {

		}

		// END public static function activate

		/**
		 * Deactivate the plugin
		 */
		public function deactivate() {

		}

		function cool_fb_scripts() {
			wp_register_script( 'cool-fb-model', plugins_url('js/remodal.min.js', __FILE__),  array('jquery'), '', true );
			wp_enqueue_script( 'cool-fb-model' );
			wp_register_style( 'cool-fb-style', plugins_url('css/style.css', __FILE__) );
			wp_enqueue_style( 'cool-fb-style' );
		}


		function fb_messenger() { ?>

			<?php
			if (get_option('hide_btn')!=true) {
			$btn_txt=!empty(get_option( 'btn_text'))?get_option( 'btn_text'):'Message Us';
				?>
				<div class="cool-fb-msz-cont">
					<a class="cool-fb-msz-button" href="#cool-fb-chat">
					<span class="btn_lbl"><?php echo $btn_txt;?></span></a>
						<?php }
			?>

			<div class="remodal cool-fb-msz-modal" data-remodal-id="cool-fb-chat">
				<?php if(get_option('fb_page_url')){ ?>
					<div class="fb-page"
						 data-href="<?php echo esc_attr( get_option('fb_page_url') ); ?>"
						 data-tabs="messages"
						 data-width="380"
						 data-height="380"
						 data-small-header="true"
						 data-hide-cover="false",
						 data-show-facepile="true",
						 data-adapt-container-width="true"
					>
						<div class="fb-xfbml-parse-ignore">
							<blockquote>Loading...</blockquote>
						</div>
					</div>
				<?php }
				else{
					// generate url path to admin's "Categories", and force https
					$settings_url= admin_url('options-general.php?page=cool-fb-settings');
					echo'<br><p>Please add your facebook page url in settings panel.<br>Click here to go to <strong><a href="'.$settings_url.'">settings page</a></strong>.</p>';
				}
				?>
				<button data-remodal-action="close" class="remodal-close"></button>
			</div>
			<div id="fb-root"></div>
			<script>
				window.fbAsyncInit = function() {
					FB.init({
						appId      : '95100348886',
						xfbml      : true,
						version    : 'v2.6'
					});
				};

				(function(d, s, id){
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) {return;}
					js = d.createElement(s); js.id = id;
					js.src = "//connect.facebook.net/en_US/sdk.js";
					fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
			</script>
			<?php
		}
	}
}

// instantiate the plugin class
new COOL_FB_CHAT();
?>
