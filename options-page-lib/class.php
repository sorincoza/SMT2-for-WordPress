<?php

/*
* Code from http://hughlashbrooke.com/
*/


if ( ! defined( 'ABSPATH' ) ) exit;

class WordPress_Plugin_Template_Settings {
    private $dir;
	private $file;
	private $assets_dir;
	private $assets_url;
	private $settings_base;
	private $settings;

	public function __construct( $file ) {
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
		$this->settings_base = '';

		// Initialise settings
		add_action( 'admin_init', array( $this, 'init' ) );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item() {
		$page = add_options_page( __( 'SMT2 Settings', 'smt2wp' ) , __( 'SMT2 Plugin Settings', 'smt2wp' ) , 'manage_options' , 'smt2wp_plugin_settings' ,  array( $this, 'settings_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets() {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		wp_enqueue_style( 'farbtastic' );
    wp_enqueue_script( 'farbtastic' );

    // We're including the WP media scripts here because they're needed for the image upload field
    // If you're not including an image upload then you can leave this function call out
    wp_enqueue_media();

    wp_register_script( 'wpt-admin-js', $this->assets_url . 'js/settings.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
    wp_enqueue_script( 'wpt-admin-js' );
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=smt2wp_plugin_settings">' . __( 'Settings', 'smt2wp' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {

		$settings['standard'] = array(
			'title'					=> __( 'Simple Settings', 'smt2wp' ),
			'description'			=> __( '', 'smt2wp' ),
			'fields'				=> array(

				array(
					'id' 			=> 'smt2wp_fps',
					'label'			=> __( 'Frames per Second Rate' , 'smt2wp' ),
					'description'	=> '<b>Default is 24 fps.</b><br>How many times per second will the mouse activity be recorded. Higher value will give you a smoother recording, but will use more database space. Lower value will use less space, but the recording can be "choppy" (like old TV replays), if the value is very low.',
					'type'			=> 'number',
					'default'		=> '',
					'placeholder'	=> __( '24', 'smt2wp' )
				),

				array(
					'id' 			=> 'smt2wp_rec_time',
					'label'			=> __( 'Maximum Recording Time' , 'smt2wp' ),
					'description'	=> '<b>Value is in seconds. Default is 3600 (equivalent of one hour).</b><br>This is the maximum time that the recording will take place. When this limit is reached, all recording stops.',
					'type'			=> 'number',
					'default'		=> '',
					'placeholder'	=> __( '3600', 'smt2wp' )
				),

				array(
					'id' 			=> 'smt2wp_post_interval',
					'label'			=> __( 'Post Interval' , 'smt2wp' ),
					'description'	=> '<b>Value is in seconds. Default is 30.</b><br>This is the interval at which the recorded data is written into the database during a normal view of the page. Tipically, you do not need to modify this.',
					'type'			=> 'number',
					'default'		=> '',
					'placeholder'	=> __( '30', 'smt2wp' )
				),

			)
		);

		$settings['extra'] = array(
			'title'					=> __( 'Extra', 'smt2wp' ),
			'description'			=> __( 'These are more advanced settings. Some of them (but not all) require technical knowledge.', 'smt2wp' ),
			'fields'				=> array(

				array(
					'id' 			=> 'smt2wp_random_tracking',
					'label'			=> __( 'Random user selection', 'smt2wp' ),
					'description'	=> __( 'Select "Yes" if you want to track users at random (this gives better statistical analysis in most cases). Select "No" if you want to track all users, no matter what.<br>More complex tracking conditions can be created (e.g.: tracking only on Mondays). <a href="http://sorincoza.com">Get in touch with me</a> for such a solution.', 'smt2wp' ),
					'type'			=> 'radio',
					'options'		=> array( '1' => 'Yes', '0' => 'No' ),
					'default'		=> '0'
				),

				array(
					'id' 			=> 'smt2wp_cont_recording',
					'label'			=> __( 'Continuous Recording', 'smt2wp' ),
					'description'	=> 'Wheter to continue recording after the user minimizes the browser window, or switches to another tab.<br>Default is "Yes" - it keeps recording until the window or tab is closed, or the user navigates away.',
					'type'			=> 'radio',
					'options'		=> array( '1' => 'Yes', '0' => 'No' ),
					'default'		=> '1'
				),

				array(
					'id' 			=> 'smt2wp_warn_text',
					'label'			=> __( 'Warn Message' , 'smt2wp' ),
					'description'	=> 'This message will be displayed before recording starts (e.g.:"We would like to study your mouse activity. Do you agree?").<br><b>Leave this blank if you do not want to display a warning message to users.</b>',
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> __( '', 'smt2wp' )
				),

				array(
					'id' 			=> 'smt2wp_cookie_days',
					'label'			=> __( 'Cookie Refresh Interval' , 'smt2wp' ),
					'description'	=> '<b>Value is in days. Default is 365 (equivalent of one year).</b><br>- SMT2 uses cookies to identify users. For example, if a user returns after a week, he will be recognized without being logged in, and his mouse activity will be stored in the same place in the database.<br>- This value specifies how many days in the future will one user be recognized.<br>- The cookie method is not reliable when a computer user account is used by multiple people, because all those people will be identified as one single user. In such cases a lower value will be more reliable, with the disadvantage that you will have many more users stored in the database - which can be difficult for interpreting your data. If you need help with this decision, <a href="http://sorincoza.com">get in touch with me</a>.',
					'type'			=> 'number',
					'default'		=> '',
					'placeholder'	=> __( '365', 'smt2wp' )
				),

			)
		);

		$settings = apply_filters( 'plugin_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings() {
		if( is_array( $this->settings ) ) {
			foreach( $this->settings as $section => $data ) {

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'smt2wp_plugin_settings' );

				foreach( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->settings_base . $field['id'];
					register_setting( 'smt2wp_plugin_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this, 'display_field' ), 'smt2wp_plugin_settings', $section, array( 'field' => $field ) );
				}
			}
		}
	}

	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Generate HTML for displaying fields
	 * @param  array $args Field data
	 * @return void
	 */
	public function display_field( $args ) {

		$field = $args['field'];

		$html = '';

		$option_name = $this->settings_base . $field['id'];
		$option = get_option( $option_name, $field['default'] );

		$data = $option;

		switch( $field['type'] ) {

			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
			break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value=""/>' . "\n";
			break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>'. "\n";
			break;

			case 'checkbox':
				$checked = '';
				if( $option && 'on' == $option ){
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;

			case 'checkbox_multi':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'radio':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';
			break;

			case 'image':
				$image_thumb = '';
				if( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __( 'Upload an image' , 'smt2wp' ) . '" data-uploader_button_text="' . __( 'Use image' , 'smt2wp' ) . '" class="image_upload_button button" value="'. __( 'Upload new image' , 'smt2wp' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="'. __( 'Remove image' , 'smt2wp' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
			break;

			case 'color':
				?><div class="color-picker" style="position:relative;">
			        <input type="text" name="<?php esc_attr_e( $option_name ); ?>" class="color" value="<?php esc_attr_e( $data ); ?>" />
			        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
			    </div>
			    <?php
			break;

		}

		switch( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
			break;

			default:
				$html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
			break;
		}

		echo $html;
	}

	/**
	 * Validate individual settings field
	 * @param  string $data Inputted value
	 * @return string       Validated value
	 */
	public function validate_field( $data ) {
		if( $data && strlen( $data ) > 0 && $data != '' ) {
			$data = urlencode( strtolower( str_replace( ' ' , '-' , $data ) ) );
		}
		return $data;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page() {

		// Build page HTML
		$html = '<div class="wrap" id="plugin_settings">' . "\n";
			$html .= '<h2>' . __( 'Plugin Settings' , 'smt2wp' ) . '</h2>' . "\n";
			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Setup navigation
				$html .= '<ul id="settings-sections" class="subsubsub hide-if-no-js">' . "\n";
					$html .= '<li><a class="tab all current" href="#all">' . __( 'All' , 'smt2wp' ) . '</a></li>' . "\n";

					foreach( $this->settings as $section => $data ) {
						$html .= '<li>| <a class="tab" href="#' . $section . '">' . $data['title'] . '</a></li>' . "\n";
					}

				$html .= '</ul>' . "\n";

				$html .= '<div class="clear"></div>' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( 'smt2wp_plugin_settings' );
				do_settings_sections( 'smt2wp_plugin_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'smt2wp' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		$html .= '<style>input[type=number]{width:70px}</style>';

		echo $html;
	}

}