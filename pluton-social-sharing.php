<?php
/**
 * Plugin Name:			Pluton Social Sharing
 * Plugin URI:			https://plutonwp.com/extension/pluton-social-sharing/
 * Description:			A simple plugin to add social share buttons to your posts.
 * Version:				1.0.1
 * Author:				PlutonWP
 * Author URI:			https://plutonwp.com/
 * Requires at least:	4.0.0
 * Tested up to:		4.6
 *
 * Text Domain: pluton-social-sharing
 * Domain Path: /languages/
 *
 * @package Pluton_Social_Sharing
 * @category Core
 * @author PlutonWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the main instance of Pluton_Social_Sharing to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Pluton_Social_Sharing
 */
function Pluton_Social_Sharing() {
	return Pluton_Social_Sharing::instance();
} // End Pluton_Social_Sharing()

Pluton_Social_Sharing();

/**
 * Main Pluton_Social_Sharing Class
 *
 * @class Pluton_Social_Sharing
 * @version	1.0.0
 * @since 1.0.0
 * @package	Pluton_Social_Sharing
 */
final class Pluton_Social_Sharing {
	/**
	 * Pluton_Social_Sharing The single instance of Pluton_Social_Sharing.
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

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct() {
		$this->token 			= 'pluton-social-sharing';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.1';

		define( 'PSS_ROOT', dirname( __FILE__ ) );

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'pss_load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'pss_setup' ) );
	}

	/**
	 * Main Pluton_Social_Sharing Instance
	 *
	 * Ensures only one instance of Pluton_Social_Sharing is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Pluton_Social_Sharing()
	 * @return Main Pluton_Social_Sharing instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function pss_load_plugin_textdomain() {
		load_plugin_textdomain( 'pluton-social-sharing', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Setup all the things.
	 * Only executes if Pluton or a child theme using Pluton as a parent is active and the extension specific filter returns true.
	 * Child themes can disable this extension using the pluton_social_sharing filter
	 * @return void
	 */
	public function pss_setup() {
		$theme = wp_get_theme();

		if ( 'Pluton' == $theme->name || 'pluton' == $theme->template && apply_filters( 'pluton_social_sharing', true ) ) {
			require_once PSS_ROOT . '/includes/core.php';
			add_action( 'customize_register', array( $this, 'pss_customizer_register' ) );
			add_action( 'customize_preview_init', array( $this, 'pss_customize_preview_js' ) );
			add_action( 'customize_controls_print_styles', array( $this, 'pss_customize_controls_print_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'pss_style' ), 999 );
			add_action( 'social_share', array( $this, 'pss_social_share' ) );
			add_filter( 'pluton_head_css', array( $this, 'pss_head_css' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'pss_install_pluton_notice' ) );
		}
	}

	/**
	 * Pluton install
	 * If the user activates the plugin while having a different parent theme active, prompt them to install Pluton.
	 * @since   1.0.0
	 * @return  void
	 */
	public function pss_install_pluton_notice() {
		echo '<div class="notice is-dismissible updated">
				<p>' . esc_html__( 'Pluton Social Sharing requires that you use Pluton as your parent theme.', 'pluton-social-sharing' ) . ' <a href="https://plutonwp.com/">' . esc_html__( 'Install Pluton now', 'pluton-social-sharing' ) . '</a></p>
			</div>';
	}

	/**
	 * Customizer Controls and settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function pss_customizer_register( $wp_customize ) {

		/**
	     * Add a new section
	     */
		$wp_customize->add_section( 'pss_section' , array(
		    'title'      	=> esc_html__( 'Pluton Social Sharing', 'pluton-social-sharing' ),
		    'priority'   	=> 162,
		) );

		/**
	     * Sharing sites
	     */
        $wp_customize->add_setting( 'pss_social_share_sites', array(
			'default'			=> array( 'twitter', 'facebook', 'google_plus', 'pinterest', 'linkedin' ),
		) );

		$wp_customize->add_control( new Pluton_Customize_Control_Sorter( $wp_customize, 'pss_social_share_sites', array(
			'label'    		=> esc_html__( 'Sites', 'pluton-social-sharing' ),
			'description' 	=> esc_html__( 'Click and drag and drop elements to re-order them.', 'pluton-social-sharing' ),
			'section'  		=> 'pss_section',
			'settings'		=> 'pss_social_share_sites',
			'choices' => array(
				'twitter'  		=> 'Twitter',
				'facebook' 		=> 'Facebook',
				'google_plus' 	=> 'Google Plus',
				'pinterest' 	=> 'Pinterest',
				'linkedin' 		=> 'LinkedIn',
			),
			'priority' 	=> 5,
		) ) );

		/**
	     * Sharing title
	     */
        $wp_customize->add_setting( 'pss_social_share_heading', array(
			'default'			=> esc_html__( 'Please Share This', 'pluton' ),
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'pss_social_share_heading', array(
			'label'			=> esc_html__( 'Heading on Posts', 'pluton-social-sharing' ),
			'section'		=> 'pss_section',
			'settings'		=> 'pss_social_share_heading',
			'type'			=> 'text',
			'priority'		=> 5,
		) ) );

		/**
	     * Sharing title
	     */
        $wp_customize->add_setting( 'pss_social_share_twitter_handle', array(
			'default'			=> '',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'pss_social_share_twitter_handle', array(
			'label'			=> esc_html__( 'Twitter Handle', 'pluton-social-sharing' ),
			'section'		=> 'pss_section',
			'settings'		=> 'pss_social_share_twitter_handle',
			'type'			=> 'text',
			'priority'		=> 5,
		) ) );

		/**
	     * Borders color
	     */
        $wp_customize->add_setting( 'pss_sharing_borders_color', array(
			'default'			=> '#e9e9e9',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( new Pluton_Customizer_Color_Control( $wp_customize, 'pss_sharing_borders_color', array(
			'label'			=> esc_html__( 'Links Borders Color', 'pluton-social-sharing' ),
			'section'		=> 'pss_section',
			'settings'		=> 'pss_sharing_borders_color',
			'priority'		=> 5,
		) ) );

		/**
	     * Icons background color
	     */
        $wp_customize->add_setting( 'pss_sharing_icons_bg', array(
			'default'			=> '#ffffff',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( new Pluton_Customizer_Color_Control( $wp_customize, 'pss_sharing_icons_bg', array(
			'label'			=> esc_html__( 'Icons Background Color', 'pluton-social-sharing' ),
			'section'		=> 'pss_section',
			'settings'		=> 'pss_sharing_icons_bg',
			'priority'		=> 5,
		) ) );

		/**
	     * Icons color
	     */
        $wp_customize->add_setting( 'pss_sharing_icons_color', array(
			'default'			=> '#bbbbbb',
			'transport'			=> 'postMessage',
		) );

		$wp_customize->add_control( new Pluton_Customizer_Color_Control( $wp_customize, 'pss_sharing_icons_color', array(
			'label'			=> esc_html__( 'Icons Color', 'pluton-social-sharing' ),
			'section'		=> 'pss_section',
			'settings'		=> 'pss_sharing_icons_color',
			'priority'		=> 5,
		) ) );
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 */
	public function pss_customize_preview_js() {
		wp_enqueue_script( 'pss-customizer', plugins_url( '/assets/js/customizer.min.js', __FILE__ ), array( 'customize-preview' ), '1.1', true );
	}

	/**
	 * Adds CSS for customizer custom controls
	 */
	public static function pss_customize_controls_print_styles() { ?>

		 <style type="text/css" id="pluton-customizer-controls-css">

			/* Icons */
			#accordion-section-pss_section > h3:before { display: inline-block; font-family: "dashicons"; content: "\f108"; width: 20px; height: 20px; font-size: 20px; line-height: 1; text-decoration: inherit; font-weight: 400; font-style: normal; vertical-align: top; text-align: center; -webkit-transition: color .1s ease-in; transition: color .1s ease-in; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; color: #298cba; margin-right: 10px; }

			#accordion-section-pss_section > h3:before { content: "\f242" }

		 </style>

	<?php
	}

	/**
	 * Enqueue style.
	 * @since   1.0.0
	 * @return  void
	 */
	public function pss_style() {

		// Load main stylesheet
		wp_enqueue_style( 'pss-style', plugins_url( '/assets/css/style.min.css', __FILE__ ) );

		// If rtl
		if ( is_RTL() ) {
			wp_enqueue_style( 'pss-style-rtl', plugins_url( '/assets/css/rtl.css', __FILE__ ) );
		}

	}

	/**
	 * Social sharing links
	 */
	public function pss_social_share() {

		$file 		= $this->plugin_path . 'template/social-share.php';
		$theme_file = get_stylesheet_directory() . '/templates/pss/social-share.php';

		if ( file_exists( $theme_file ) ) {
			$file = $theme_file;
		}

		if ( file_exists( $file ) ) {
			include $file;
		}

	}

	/**
	 * Add css in head tag.
	 */
	public function pss_head_css( $output ) {
		
		// Global vars
		$borders 		= get_theme_mod( 'pss_sharing_borders_color', '#e9e9e9' );
		$icons_bg 		= get_theme_mod( 'pss_sharing_icons_bg', '#ffffff' );
		$icons_color 	= get_theme_mod( 'pss_sharing_icons_color', '#bbbbbb' );

		// Define css var
		$css = '';

		// Add borders color
		if ( ! empty( $borders ) && '#e9e9e9' != $borders ) {
			$css .= '.entry-share ul li a{border-color:'. $borders .';}';
		}

		// Add icon background
		if ( ! empty( $icons_bg ) ) {
			$css .= '.entry-share ul li a{background-color:'. $icons_bg .';}';
		}

		// Add icon color
		if ( ! empty( $icons_color ) && '#bbbbbb' != $icons_color ) {
			$css .= '.entry-share ul li a{color:'. $icons_color .';}';
		}
			
		// Return CSS
		if ( ! empty( $css ) ) {
			$output .= '/*SOCIAL SHARE CSS*/'. $css;
		}

		// Return output css
		return $output;

	}

} // End Class
