<?php
/**
 * Bootstrapping the Gutenberg widgets editor in the customizer.
 *
 * @package gutenberg
 */

/**
 * Gutenberg's Customize Register.
 *
 * Adds a section to the Customizer for editing widgets with Gutenberg.
 *
 * @param \WP_Customize_Manager $manager An instance of the class that controls most of the Theme Customization API for WordPress 3.4 and newer.
 */
function gutenberg_widgets_customize_register( $manager ) {
	global $wp_registered_sidebars;

	if ( ! gutenberg_use_widgets_block_editor() ) {
		return;
	}

	require_once __DIR__ . '/class-wp-sidebar-block-editor-control.php';

	foreach ( $manager->sections() as $section ) {
		if ( $section instanceof WP_Customize_Sidebar_Section ) {
			$section->description = '';
		}
	}

	foreach ( $manager->controls() as $control ) {
		if (
			$control instanceof WP_Widget_Area_Customize_Control ||
			$control instanceof WP_Widget_Form_Customize_Control
		) {
			$manager->remove_control( $control->id );
		}
	}

	$sidebars_widgets = array_merge(
		array_fill_keys( array_keys( $wp_registered_sidebars ), array() ),
		wp_get_sidebars_widgets()
	);

	foreach ( $sidebars_widgets as $sidebar_id => $sidebar_widgets_ids ) {
		$control = new WP_Sidebar_Block_Editor_Control(
			$manager,
			"sidebars_widgets[$sidebar_id]",
			array(
				'section'    => "sidebar-widgets-$sidebar_id",
				'sidebar_id' => $sidebar_id,
			)
		);
		$manager->add_control( $control );
	}
}

if ( gutenberg_is_experiment_enabled( 'gutenberg-widgets-in-customizer' ) ) {
	add_action( 'customize_register', 'gutenberg_widgets_customize_register', 20 );
}
