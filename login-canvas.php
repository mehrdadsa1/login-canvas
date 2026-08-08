<?php
/**
 * Plugin Name: Login Canvas – Customize Login Page
 * Plugin URI: https://karizmagp.com
 * Description: Create a modern, branded WordPress login page with custom typography, colors, imagery, and responsive layouts.
 * Version: 1.5.3
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: Mehrdad Sadeghi
 * Author URI: https://karizmagp.com
 * Text Domain: login-canvas
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Login_Canvas {
	private const VERSION = '1.5.3';
	private const OPTION  = 'login_canvas_options';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_login_styles' ) );
		add_filter( 'login_message', array( $this, 'render_login_message' ) );
		add_filter( 'login_headerurl', array( $this, 'filter_logo_url' ) );
		add_filter( 'login_headertext', array( $this, 'filter_logo_title' ) );
		add_filter( 'login_body_class', array( $this, 'add_login_body_class' ) );
	}

	public function add_settings_page(): void {
		add_options_page(
			__( 'Login Canvas', 'login-canvas' ),
			__( 'Login Canvas', 'login-canvas' ),
			'manage_options',
			'login-canvas',
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings(): void {
		register_setting(
			'login_canvas_group',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_options' ),
				'default'           => $this->defaults(),
			)
		);
	}

	public function enqueue_admin_assets( string $hook_suffix ): void {
		if ( 'settings_page_login-canvas' !== $hook_suffix ) {
			return;
		}

		$options = $this->get_options();

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );

		if ( ! empty( $options['load_google_fonts'] ) ) {
			wp_enqueue_style( 'login-canvas-google-fonts', $this->google_fonts_url(), array(), self::VERSION );
		}

		wp_enqueue_style(
			'login-canvas-admin',
			plugin_dir_url( __FILE__ ) . 'assets/css/admin.css',
			array( 'wp-color-picker' ),
			self::VERSION
		);
		wp_enqueue_script(
			'login-canvas-admin',
			plugin_dir_url( __FILE__ ) . 'assets/js/admin.js',
			array( 'jquery', 'wp-color-picker' ),
			self::VERSION,
			true
		);
		wp_localize_script(
			'login-canvas-admin',
			'loginCanvasAdmin',
			array(
				'chooseLogo'      => __( 'Choose a logo', 'login-canvas' ),
				'chooseImage'     => __( 'Choose a background image', 'login-canvas' ),
				'useImage'        => __( 'Use this image', 'login-canvas' ),
				'defaultLogoText' => __( 'Your logo', 'login-canvas' ),
			)
		);
	}

	public function sanitize_options( $input ): array {
		$defaults = $this->defaults();
		$input    = is_array( $input ) ? $input : array();

		$output = array(
			'logo_url'            => esc_url_raw( $input['logo_url'] ?? '' ),
			'logo_link'           => esc_url_raw( $input['logo_link'] ?? home_url( '/' ) ),
			'logo_title'          => sanitize_text_field( $input['logo_title'] ?? get_bloginfo( 'name' ) ),
			'welcome_text'        => sanitize_text_field( $input['welcome_text'] ?? $defaults['welcome_text'] ),
			'welcome_description' => sanitize_text_field( $input['welcome_description'] ?? $defaults['welcome_description'] ),
			'panel_title'         => sanitize_text_field( $input['panel_title'] ?? $defaults['panel_title'] ),
			'panel_description'   => sanitize_textarea_field( $input['panel_description'] ?? $defaults['panel_description'] ),
			'background_image'    => esc_url_raw( $input['background_image'] ?? '' ),
			'background_color'    => sanitize_hex_color( $input['background_color'] ?? '' ) ?: $defaults['background_color'],
			'form_background'     => sanitize_hex_color( $input['form_background'] ?? '' ) ?: $defaults['form_background'],
			'button_color'        => sanitize_hex_color( $input['button_color'] ?? '' ) ?: $defaults['button_color'],
			'button_text_color'   => sanitize_hex_color( $input['button_text_color'] ?? '' ) ?: $defaults['button_text_color'],
			'text_color'          => sanitize_hex_color( $input['text_color'] ?? '' ) ?: $defaults['text_color'],
			'link_color'          => sanitize_hex_color( $input['link_color'] ?? '' ) ?: $defaults['link_color'],
			'form_width'          => min( 520, max( 320, absint( $input['form_width'] ?? $defaults['form_width'] ) ) ),
			'border_radius'       => min( 42, max( 8, absint( $input['border_radius'] ?? $defaults['border_radius'] ) ) ),
			'hide_back_to_site'   => empty( $input['hide_back_to_site'] ) ? 0 : 1,
			'hide_language_menu'  => empty( $input['hide_language_menu'] ) ? 0 : 1,
			'load_google_fonts'   => empty( $input['load_google_fonts'] ) ? 0 : 1,
		);

		return wp_parse_args( $output, $defaults );
	}

	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$options = $this->get_options();
		?>
		<div class="wrap login-canvas-admin-wrap">
			<div class="login-canvas-heading">
				<div>
					<h1><?php esc_html_e( 'Login Canvas', 'login-canvas' ); ?></h1>
					<p><?php esc_html_e( 'Build a polished login experience without changing authentication, login URLs, or front-end account forms.', 'login-canvas' ); ?></p>
				</div>
				<a class="button button-secondary" href="<?php echo esc_url( wp_login_url() ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open login page', 'login-canvas' ); ?></a>
			</div>

			<form action="options.php" method="post">
				<?php settings_fields( 'login_canvas_group' ); ?>
				<div class="login-canvas-layout">
					<div class="login-canvas-panel">
						<section class="login-canvas-section">
							<h2><?php esc_html_e( 'Branding', 'login-canvas' ); ?></h2>
							<?php $this->media_field( 'logo_url', __( 'Logo', 'login-canvas' ), $options['logo_url'], 'logo' ); ?>
							<?php $this->text_field( 'logo_link', __( 'Logo link', 'login-canvas' ), $options['logo_link'], 'url' ); ?>
							<?php $this->text_field( 'logo_title', __( 'Logo title', 'login-canvas' ), $options['logo_title'] ); ?>
						</section>

						<section class="login-canvas-section">
							<h2><?php esc_html_e( 'Login content', 'login-canvas' ); ?></h2>
							<?php $this->text_field( 'welcome_text', __( 'Login title', 'login-canvas' ), $options['welcome_text'] ); ?>
							<?php $this->text_field( 'welcome_description', __( 'Login description', 'login-canvas' ), $options['welcome_description'] ); ?>
							<?php $this->text_field( 'panel_title', __( 'Visual panel title', 'login-canvas' ), $options['panel_title'] ); ?>
							<?php $this->textarea_field( 'panel_description', __( 'Visual panel description', 'login-canvas' ), $options['panel_description'] ); ?>
						</section>

						<section class="login-canvas-section">
							<h2><?php esc_html_e( 'Typography', 'login-canvas' ); ?></h2>
							<?php $this->checkbox_field( 'load_google_fonts', __( 'Load Estedad for Persian and Inter for other languages from Google Fonts', 'login-canvas' ), $options['load_google_fonts'] ); ?>
							<p class="description login-canvas-external-note"><?php esc_html_e( 'When enabled, visitors’ browsers connect to Google Fonts. Leave this disabled to use privacy-friendly system fallbacks.', 'login-canvas' ); ?></p>
						</section>

						<section class="login-canvas-section">
							<h2><?php esc_html_e( 'Background', 'login-canvas' ); ?></h2>
							<?php $this->media_field( 'background_image', __( 'Background image', 'login-canvas' ), $options['background_image'], 'background' ); ?>
							<?php $this->color_field( 'background_color', __( 'Background color', 'login-canvas' ), $options['background_color'] ); ?>
						</section>

						<section class="login-canvas-section">
							<h2><?php esc_html_e( 'Style', 'login-canvas' ); ?></h2>
							<div class="login-canvas-grid">
								<?php $this->color_field( 'form_background', __( 'Form background', 'login-canvas' ), $options['form_background'] ); ?>
								<?php $this->color_field( 'text_color', __( 'Text color', 'login-canvas' ), $options['text_color'] ); ?>
								<?php $this->color_field( 'link_color', __( 'Accent color', 'login-canvas' ), $options['link_color'] ); ?>
								<?php $this->color_field( 'button_color', __( 'Primary color', 'login-canvas' ), $options['button_color'] ); ?>
								<?php $this->color_field( 'button_text_color', __( 'Button text color', 'login-canvas' ), $options['button_text_color'] ); ?>
								<?php $this->number_field( 'form_width', __( 'Form area width', 'login-canvas' ), $options['form_width'], 320, 520, 'px' ); ?>
								<?php $this->number_field( 'border_radius', __( 'Corner radius', 'login-canvas' ), $options['border_radius'], 8, 42, 'px' ); ?>
							</div>
						</section>

						<section class="login-canvas-section">
							<h2><?php esc_html_e( 'Visibility', 'login-canvas' ); ?></h2>
							<?php $this->checkbox_field( 'hide_back_to_site', __( 'Hide the “Back to site” link', 'login-canvas' ), $options['hide_back_to_site'] ); ?>
							<?php $this->checkbox_field( 'hide_language_menu', __( 'Hide the language selector', 'login-canvas' ), $options['hide_language_menu'] ); ?>
						</section>

						<?php submit_button( __( 'Save changes', 'login-canvas' ) ); ?>
					</div>

					<aside class="login-canvas-preview-card">
						<div class="login-canvas-preview" id="login-canvas-preview">
							<div class="login-canvas-preview-shell">
								<div class="login-canvas-preview-visual">
									<span><?php esc_html_e( 'Website access', 'login-canvas' ); ?></span>
									<h3 id="login-canvas-preview-panel-title"><?php echo esc_html( $options['panel_title'] ); ?></h3>
									<p id="login-canvas-preview-panel-description"><?php echo esc_html( $options['panel_description'] ); ?></p>
									<div class="login-canvas-preview-orb"></div>
								</div>
								<div class="login-canvas-preview-content">
									<div class="login-canvas-preview-logo" id="login-canvas-preview-logo"><?php esc_html_e( 'Your logo', 'login-canvas' ); ?></div>
									<div class="login-canvas-preview-welcome" id="login-canvas-preview-welcome"><?php echo esc_html( $options['welcome_text'] ); ?></div>
									<div class="login-canvas-preview-description" id="login-canvas-preview-description"><?php echo esc_html( $options['welcome_description'] ); ?></div>
									<div class="login-canvas-preview-form">
										<label><?php esc_html_e( 'Username or Email Address', 'login-canvas' ); ?></label>
										<input type="text" disabled>
										<label><?php esc_html_e( 'Password', 'login-canvas' ); ?></label>
										<input type="password" value="password" disabled>
										<button type="button" disabled><?php esc_html_e( 'Log In', 'login-canvas' ); ?></button>
									</div>
								</div>
							</div>
						</div>
						<p><?php esc_html_e( 'Preview is approximate. Open the login page to see the final responsive layout.', 'login-canvas' ); ?></p>
					</aside>
				</div>
			</form>
		</div>
		<?php
	}

	public function enqueue_login_styles(): void {
		$options      = $this->get_options();
		$dependencies = array();

		if ( ! empty( $options['load_google_fonts'] ) ) {
			wp_enqueue_style( 'login-canvas-google-fonts', $this->google_fonts_url(), array(), self::VERSION );
			$dependencies[] = 'login-canvas-google-fonts';
		}

		wp_enqueue_style(
			'login-canvas-login',
			plugin_dir_url( __FILE__ ) . 'assets/css/login.css',
			$dependencies,
			self::VERSION
		);

		wp_enqueue_script(
			'login-canvas-layout',
			plugin_dir_url( __FILE__ ) . 'assets/js/login-layout.js',
			array(),
			self::VERSION,
			true
		);

		$background_image = '';
		if ( ! empty( $options['background_image'] ) ) {
			$background_image = 'background-image:linear-gradient(rgba(15,23,42,.16),rgba(15,23,42,.16)),url(' . wp_json_encode( esc_url_raw( $options['background_image'] ) ) . ');';
		}

		$custom_css = ':root{' .
			'--lc-bg:' . $options['background_color'] . ';' .
			'--lc-form-bg:' . $options['form_background'] . ';' .
			'--lc-button:' . $options['button_color'] . ';' .
			'--lc-button-text:' . $options['button_text_color'] . ';' .
			'--lc-text:' . $options['text_color'] . ';' .
			'--lc-link:' . $options['link_color'] . ';' .
			'--lc-width:' . absint( $options['form_width'] ) . 'px;' .
			'--lc-radius:' . absint( $options['border_radius'] ) . 'px;' .
		'}' .
		'body.login{' . $background_image . '}' .
		( ! empty( $options['logo_url'] ) ? '.login h1 a{background-image:url(' . wp_json_encode( esc_url_raw( $options['logo_url'] ) ) . ');}' : '' ) .
		( ! empty( $options['hide_back_to_site'] ) ? '.login #backtoblog{display:none!important;}' : '' ) .
		( ! empty( $options['hide_language_menu'] ) ? '.login .language-switcher{display:none!important;}' : '' );

		wp_add_inline_style( 'login-canvas-login', $custom_css );
	}

	public function render_login_message( string $message ): string {
		$options = $this->get_options();

		ob_start();
		?>
		<div class="login-canvas-visual-panel">
			<div class="login-canvas-visual-content">
				<span class="login-canvas-eyebrow">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.75 4.5 6v5.15c0 4.68 3.2 8.99 7.5 10.1 4.3-1.11 7.5-5.42 7.5-10.1V6L12 2.75Zm3.57 6.88-4.3 4.3a.75.75 0 0 1-1.06 0l-1.78-1.78 1.06-1.06 1.25 1.25 3.77-3.77 1.06 1.06Z"/></svg>
					<?php esc_html_e( 'Website access', 'login-canvas' ); ?>
				</span>
				<h2><?php echo esc_html( $options['panel_title'] ); ?></h2>
				<p><?php echo esc_html( $options['panel_description'] ); ?></p>
			</div>
			<div class="login-canvas-visual-art" aria-hidden="true">
				<span></span><span></span><span></span>
			</div>
		</div>
		<div class="login-canvas-intro">
			<?php $display_logo = $this->get_display_logo_url( $options ); ?>
			<a class="login-canvas-brand-logo" href="<?php echo esc_url( $options['logo_link'] ?: home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( $options['logo_title'] ?: get_bloginfo( 'name' ) ); ?>">
				<img src="<?php echo esc_url( $display_logo ); ?>" alt="<?php echo esc_attr( $options['logo_title'] ?: get_bloginfo( 'name' ) ); ?>">
			</a>
			<?php if ( ! empty( $options['welcome_text'] ) ) : ?>
				<p class="login-canvas-welcome"><?php echo esc_html( $options['welcome_text'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $options['welcome_description'] ) ) : ?>
				<p class="login-canvas-description"><?php echo esc_html( $options['welcome_description'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php

		return ob_get_clean() . $message;
	}

	public function filter_logo_url( string $url ): string {
		$options = $this->get_options();
		return ! empty( $options['logo_link'] ) ? $options['logo_link'] : $url;
	}

	public function filter_logo_title( string $title ): string {
		$options = $this->get_options();
		return ! empty( $options['logo_title'] ) ? $options['logo_title'] : $title;
	}

	public function add_login_body_class( array $classes ): array {
		$classes[] = 'login-canvas-enabled';
		return $classes;
	}

	private function get_display_logo_url( array $options ): string {
		if ( ! empty( $options['logo_url'] ) ) {
			return $options['logo_url'];
		}

		$site_icon = get_site_icon_url( 192 );
		if ( $site_icon ) {
			return $site_icon;
		}

		return admin_url( 'images/wordpress-logo.svg' );
	}

	private function defaults(): array {
		return array(
			'logo_url'            => '',
			'logo_link'           => home_url( '/' ),
			'logo_title'          => get_bloginfo( 'name' ),
			'welcome_text'        => __( 'Welcome back', 'login-canvas' ),
			'welcome_description' => __( 'Enter your details to access your dashboard.', 'login-canvas' ),
			'panel_title'         => get_bloginfo( 'name' ),
			'panel_description'   => __( 'A focused and beautifully branded place to continue managing your website.', 'login-canvas' ),
			'background_image'    => '',
			'background_color'    => '#eef2ff',
			'form_background'     => '#ffffff',
			'button_color'        => '#4f46e5',
			'button_text_color'   => '#ffffff',
			'text_color'          => '#111827',
			'link_color'          => '#7c3aed',
			'form_width'          => 420,
			'border_radius'       => 28,
			'hide_back_to_site'   => 0,
			'hide_language_menu'  => 0,
			'load_google_fonts'   => 0,
		);
	}

	private function get_options(): array {
		$options = get_option( self::OPTION, array() );
		$options = wp_parse_args( is_array( $options ) ? $options : array(), $this->defaults() );

		$options['form_width']    = min( 520, max( 360, absint( $options['form_width'] ) ) );
		$options['border_radius'] = min( 36, max( 10, absint( $options['border_radius'] ) ) );

		return $options;
	}

	private function field_name( string $key ): string {
		return self::OPTION . '[' . $key . ']';
	}

	private function text_field( string $key, string $label, string $value, string $type = 'text' ): void {
		?>
		<div class="login-canvas-field">
			<label for="login-canvas-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
			<input class="regular-text" type="<?php echo esc_attr( $type ); ?>" id="login-canvas-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->field_name( $key ) ); ?>" value="<?php echo esc_attr( $value ); ?>">
		</div>
		<?php
	}

	private function textarea_field( string $key, string $label, string $value ): void {
		?>
		<div class="login-canvas-field">
			<label for="login-canvas-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
			<textarea class="large-text" rows="3" id="login-canvas-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->field_name( $key ) ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
		</div>
		<?php
	}

	private function color_field( string $key, string $label, string $value ): void {
		?>
		<div class="login-canvas-field">
			<label for="login-canvas-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
			<input class="login-canvas-color" type="text" id="login-canvas-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->field_name( $key ) ); ?>" value="<?php echo esc_attr( $value ); ?>" data-default-color="<?php echo esc_attr( $this->defaults()[ $key ] ); ?>">
		</div>
		<?php
	}

	private function number_field( string $key, string $label, int $value, int $min, int $max, string $suffix ): void {
		?>
		<div class="login-canvas-field">
			<label for="login-canvas-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
			<div class="login-canvas-number-wrap">
				<input type="number" id="login-canvas-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->field_name( $key ) ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" min="<?php echo esc_attr( (string) $min ); ?>" max="<?php echo esc_attr( (string) $max ); ?>">
				<span><?php echo esc_html( $suffix ); ?></span>
			</div>
		</div>
		<?php
	}

	private function checkbox_field( string $key, string $label, int $checked ): void {
		?>
		<label class="login-canvas-checkbox">
			<input type="checkbox" name="<?php echo esc_attr( $this->field_name( $key ) ); ?>" value="1" <?php checked( 1, $checked ); ?>>
			<span><?php echo esc_html( $label ); ?></span>
		</label>
		<?php
	}

	private function media_field( string $key, string $label, string $value, string $kind ): void {
		?>
		<div class="login-canvas-field login-canvas-media-field" data-kind="<?php echo esc_attr( $kind ); ?>">
			<label for="login-canvas-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
			<div class="login-canvas-media-row">
				<input class="regular-text login-canvas-media-url" type="url" id="login-canvas-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->field_name( $key ) ); ?>" value="<?php echo esc_attr( $value ); ?>">
				<button type="button" class="button login-canvas-select-media"><?php esc_html_e( 'Choose image', 'login-canvas' ); ?></button>
				<button type="button" class="button-link-delete login-canvas-remove-media"><?php esc_html_e( 'Remove', 'login-canvas' ); ?></button>
			</div>
			<div class="login-canvas-media-preview">
				<?php if ( ! empty( $value ) ) : ?>
					<img src="<?php echo esc_url( $value ); ?>" alt="">
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	private function google_fonts_url(): string {
		return 'https://fonts.googleapis.com/css2?family=Estedad:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap';
	}
}

new Login_Canvas();
