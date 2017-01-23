<?php
/**
 * @link              http://appygoo.com
 * @since             1.0.0
 * @package           Tweet_It
 *
 * @wordpress-plugin
 * Plugin Name:       Tweet It Shortcode Plugin
 * Plugin URI:        http://appygoo.com/tweeit-shorcode/
 * Description:       
 * Version:           1.0.0
 * Author:            Zeiad Habbab
 * Author URI:        http://appygoo.com
 * Text Domain:       tweetit
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of Tweetit_Plugin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Tweetit_Plugin
 */
function Tweetit_Plugin() {
	return Tweetit_Plugin::instance();
} // End Tweetit_Plugin()

add_action( 'plugins_loaded', 'Tweetit_Plugin' );

/**
 * Main Tweetit_Plugin Class
 *
 * @class Tweetit_Plugin
 * @version	1.0.0
 * @since 1.0.0
 * @package	Tweetit_Plugin
 * @author Matty
 */
final class Tweetit_Plugin {
	/**
	 * Tweetit_Plugin The single instance of Tweetit_Plugin.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * The plugin directory URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_url;

	/**
	 * The plugin directory path.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_path;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * The settings object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings;
	// Admin - End

	// Post Types - Start
	/**
	 * The post types we're registering.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $post_types = array();
	// Post Types - End
	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct () {
		$this->token 			= 'tweetit-plugin';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.0';

		// Admin - Start
		require_once( 'classes/class-tweetit-plugin-settings.php' );
			$this->settings = Tweetit_Plugin_Settings::instance();

		if ( is_admin() ) {
			require_once( 'classes/class-tweetit-plugin-admin.php' );
			$this->admin = Tweetit_Plugin_Admin::instance();
		}
		
		// Admin - End
 		register_activation_hook( __FILE__, array( $this, 'install' ) );
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init',  array( $this, 'tweetit_css_and_js' ) );
		
		
		
		$domain = 'tweetit';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		// wp-content/languages/plugin-name/plugin-name-de_DE.mo
		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		// wp-content/plugins/plugin-name/languages/plugin-name-de_DE.mo
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
		
		
		
		
	} // End __construct()

	/**
	 * Main Tweetit_Plugin Instance
	 *
	 * Ensures only one instance of Tweetit_Plugin is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Tweetit_Plugin()
	 * @return Main Tweetit_Plugin instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'tweetit-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

	/**
	 * Cloning is forbidden.
	 * @access public
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 * @access public
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 */
	public function install () {
		$this->_log_version_number();
	} // End install()

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 */
	private function _log_version_number () {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()
	
	
	public static function tweetit_func( $atts, $content = "" ) {
        
		$account  = "";
		$id  = "";
		$settings = get_option( 'tweetit-plugin-standard-fields', array() );
		$background_color = "twitter-background";
		$show_twitter = false;
		$show_facebook = false;
		
		
		
		if(!empty($settings)){
			
			$account  = $settings['twitter_account'];
			$id  = $settings['facebook_app_id'];
			$background  = $settings['back_color'];
			
			$show_twitter = $settings['show_twitter'];
			$show_facebook = $settings['show_facebook'];
		
 
		}
		
		$twitterlink = "https://twitter.com/intent/tweet?text=".wp_strip_all_tags($content)."&amp;via=".$account."&amp;related=".$account."&amp;url=".wp_get_shortlink();
		
		$facebooklink = "https://www.facebook.com/dialog/share?app_id=$id&display=popup&description=".wp_strip_all_tags($content)."&href=".wp_get_shortlink();
		
		if($background == "0"){
			$background_color = "facebook-background";
		}
		
		if($background == "1"){
			$background_color = "twitter-background";
		}
		
		$output = '
			<span class="tweetit-box '.$background_color.'">
				<span>'.wp_strip_all_tags($content).'</span>
				<div class="tweetit-links">';
				
				if($show_facebook){
					$output .= ' <a href="'.$facebooklink.'" target="_blank" rel="nofollow"><span class="tweetit-link"><span class="tweetit-icon-twitter fa fa-facebook"></span> '.__( 'Share it', 'tweetit' ).'</span></a>';
				}
				
				if($show_twitter){
					$output .= ' <a href="'.$twitterlink.'" target="_blank" rel="nofollow"><span class="tweetit-link"><span class="tweetit-icon-twitter fa fa-twitter"></span> '.__( 'Tweet it', 'tweetit' ).'</span></a>';
				}
		
		$output .= '
				</div>
			</span>
			';
		
		return $output;
	
    }
 
	public function tweetit_css_and_js() {
		wp_register_style('tweetit_css_and_js', plugins_url('css/tweetit.css',__FILE__ ));
		wp_enqueue_style('tweetit_css_and_js');
		
		if(__('dir','tweetit') == "rtl"){
			
			wp_register_style('tweetit_css_rtl', plugins_url('css/tweetit_rtl.css',__FILE__ ));
			wp_enqueue_style('tweetit_css_rtl');
		}
		
		
		
		
		wp_register_style('font_awesome', "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css");
		wp_enqueue_style('font_awesome');
		
		//wp_register_script( 'tweetit_css_and_js', plugins_url('js/tweetit.js',__FILE__ ));
		//wp_enqueue_script('tweetit_css_and_js');
	}
		

} // End Class

function register_buttons_editor($buttons)
{
    //register buttons with their id.
    array_push($buttons, "green");
    return $buttons;
}

add_filter("mce_buttons", "register_buttons_editor");

add_shortcode( 'tweetit', array( 'Tweetit_Plugin', 'tweetit_func' ) );

function enqueue_plugin_scripts($plugin_array)
{
    $plugin_array["tweetit_button_plugin"] =  plugin_dir_url(__FILE__) . "js/tweetit.js";
    return $plugin_array;
}

add_filter("mce_external_plugins", "enqueue_plugin_scripts");


