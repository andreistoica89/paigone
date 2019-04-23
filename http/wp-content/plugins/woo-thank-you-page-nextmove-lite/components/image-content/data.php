<?php
defined( 'ABSPATH' ) || exit;

$config             = array();
$config["slug"]     = "_xlwcty_image";
$config["title"]    = "Image Text";
$config["instance"] = require( __DIR__ . "/instance.php" );
$config['fields']   = array(
	'id'                     => $config["slug"] . "_1",
	'position'               => 35,
	'xlwcty_accordion_title' => $config["title"],
	'xlwcty_icon'            => "xlwcty-fa xlwcty-fa-drivers-license-o",
	"fields"                 => array(
		array(
			'name'                       => __( 'Enable', 'woo-thank-you-page-nextmove-lite' ),
			'id'                         => $config["slug"] . '_enable_1',
			'type'                       => 'xlwcty_switch',
			'row_classes'                => array( 'xlwcty_is_enable' ),
			'label'                      => array( 'on' => __( 'Yes', 'woo-thank-you-page-nextmove-lite' ), 'off' => __( 'No', 'woo-thank-you-page-nextmove-lite' ) ),
			'before_row'                 => array( 'XLWCTY_Admin_CMB2_Support', 'cmb_before_row_cb' ),
			'xlwcty_accordion_title'     => $config["title"] . ' 1',
			'xlwcty_accordion_index'     => '1',
			"xlwcty_component"           => $config["slug"],
			'xlwcty_is_accordion_opened' => false,
			'after'                      => include_once __DIR__ . "/help.php"
		),
		array(
			'name'        => __( 'Heading', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_heading_1',
			'type'        => 'text',
			'row_classes' => array( 'xlwcty_no_border' ),
			'desc'        => '<a href="javascript:void(0);" onclick="xlwcty_show_tb(\'Merge Tags\',\'xlwcty_merge_tags_invenotry_bar_help\');">Dynamic merge tags list</a>',
			'attributes'  => array(
				'data-conditional-id'    => $config["slug"] . '_enable_1',
				'data-conditional-value' => '1',
			),
		),
		array(
			'name'        => __( 'Heading font size', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_heading_font_size_1',
			'type'        => 'text_small',
			'row_classes' => array( 'xlwcty_hide_label', 'xlwcty_pt0', 'xlwcty_combine_2_field_start' ),
			'before'      => '<p>Font Size (px)</p>',
			'attributes'  => array(
				'type'                   => 'number',
				'min'                    => '0',
				'pattern'                => '\d*',
				'data-conditional-id'    => $config["slug"] . '_enable_1',
				'data-conditional-value' => '1',
			),
		),
		array(
			'name'        => __( 'Heading alignment', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_heading_alignment_1',
			'type'        => 'select',
			'row_classes' => array( 'xlwcty_hide_label', 'xlwcty_pt0', 'xlwcty_combine_2_field_end' ),
			'before'      => '<p>Alignment</p>',
			"options"     => array(
				'left'   => __( "Left", 'woo-thank-you-page-nextmove-lite' ),
				'center' => __( "Center", 'woo-thank-you-page-nextmove-lite' ),
				'right'  => __( "Right", 'woo-thank-you-page-nextmove-lite' ),
			),
			'attributes'  => array(
				'data-conditional-id'    => $config["slug"] . '_enable_1',
				'data-conditional-value' => '1',
			),
		),
		array(
			'name'        => __( 'Description', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_desc_1',
			'type'        => 'textarea_small',
			'row_classes' => array( 'xlwcty_no_border' ),
			'desc'        => '<a href="javascript:void(0);" onclick="xlwcty_show_tb(\'Merge Tags\',\'xlwcty_merge_tags_invenotry_bar_help\');">Dynamic merge tags list</a>',
			'attributes'  => array(
				'data-conditional-id'    => $config["slug"] . '_enable_1',
				'data-conditional-value' => '1',
			),
		),
		array(
			'name'        => __( 'Description alignment', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_desc_alignment_1',
			'type'        => 'select',
			'row_classes' => array( 'xlwcty_hide_label', 'xlwcty_pt0', 'xlwcty_select_small' ),
			'before'      => '<p>Alignment</p>',
			"options"     => array(
				'left'   => __( "Left", 'woo-thank-you-page-nextmove-lite' ),
				'center' => __( "Center", 'woo-thank-you-page-nextmove-lite' ),
				'right'  => __( "Right", 'woo-thank-you-page-nextmove-lite' ),
			),
			'attributes'  => array(
				'data-conditional-id'    => $config["slug"] . '_enable_1',
				'data-conditional-value' => '1',
			),
		),
		array(
			'name'        => __( 'Layout', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_layout_1',
			'type'        => 'radio_inline',
			"options"     => array(
				'full'          => "<img src='" . plugin_dir_url( XLWCTY_PLUGIN_FILE ) . "admin/assets/img/only_icon.jpg' />",
				'2c'            => "<img src='" . plugin_dir_url( XLWCTY_PLUGIN_FILE ) . "admin/assets/img/icon-icon.jpg' />",
				'image_content' => "<img src='" . plugin_dir_url( XLWCTY_PLUGIN_FILE ) . "admin/assets/img/icon-text.jpg' />",
				'content_image' => "<img src='" . plugin_dir_url( XLWCTY_PLUGIN_FILE ) . "admin/assets/img/text-icon.jpg' />",
			),
			'row_classes' => array( 'xlwcty_img_options' ),
			'attributes'  => array(
				'data-conditional-id'    => $config["slug"] . '_enable_1',
				'data-conditional-value' => '1',
			)
		),
		array(
			'name'        => __( 'Image', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_img_source_1',
			'type'        => 'file',
			'options'     => array(
				'url' => false,
			),
			'text'        => array(
				'add_upload_file_text' => 'Add/ Update Image'
			),
			'row_classes' => array( 'xlwcty_no_border', 'xlwcty_hide_label' ),
			'before'      => '<p>Image</p>',
			'attributes'  => array(
				'data-conditional-id'           => $config["slug"] . '_enable_1',
				'data-conditional-value'        => '1',
				'data-xlwcty-conditional-id'    => $config["slug"] . '_layout_1',
				'data-xlwcty-conditional-value' => 'full',
			)
		),
		array(
			'name'        => __( 'Image link ', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_img_link_1',
			'type'        => 'text',
			'row_classes' => array( 'xlwcty_pt0', 'xlwcty_hide_label' ),
			'before'      => '<p>Link</p>',
			'attributes'  => array(
				'data-conditional-id'           => $config["slug"] . '_enable_1',
				'data-conditional-value'        => '1',
				'data-xlwcty-conditional-id'    => $config["slug"] . '_layout_1',
				'data-xlwcty-conditional-value' => 'full',
			)
		),
		array(
			'name'        => __( 'Image Content Ratio', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_img_cont_ratio_1',
			'type'        => 'radio_inline',
			"options"     => array(
				'50_50' => "<img src='" . plugin_dir_url( XLWCTY_PLUGIN_FILE ) . "admin/assets/img/image_layout_50_50.jpg' />",
				'33_66' => "<img src='" . plugin_dir_url( XLWCTY_PLUGIN_FILE ) . "admin/assets/img/image_layout_33_66.jpg' />",
				'66_33' => "<img src='" . plugin_dir_url( XLWCTY_PLUGIN_FILE ) . "admin/assets/img/image_layout_66_33.jpg' />",
			),
			'row_classes' => array( 'xlwcty_img_options' ),
			'attributes'  => array(
				'data-conditional-id'           => $config["slug"] . '_enable_1',
				'data-conditional-value'        => '1',
				'data-xlwcty-conditional-id'    => $config["slug"] . '_layout_1',
				'data-xlwcty-conditional-value' => json_encode( array( 'image_content', 'content_image' ) ),
			)
		),
		array(
			'name'        => __( 'Left Image', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_img_l_source_1',
			'type'        => 'file',
			'options'     => array(
				'url' => false,
			),
			'text'        => array(
				'add_upload_file_text' => 'Add/ Update Imade'
			),
			'row_classes' => array( 'xlwcty_no_border' ),
			'attributes'  => array(
				'data-conditional-id'           => $config["slug"] . '_enable_1',
				'data-conditional-value'        => '1',
				'data-xlwcty-conditional-id'    => $config["slug"] . '_layout_1',
				'data-xlwcty-conditional-value' => json_encode( array( 'image_content', '2c' ) ),
			)
		),
		array(
			'name'        => __( 'Left Image link ', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_img_l_link_1',
			'type'        => 'text',
			'row_classes' => array( 'xlwcty_hide_label', 'xlwcty_pt0' ),
			'before'      => '<p>Link</p>',
			'attributes'  => array(
				'data-conditional-id'           => $config["slug"] . '_enable_1',
				'data-conditional-value'        => '1',
				'data-xlwcty-conditional-id'    => $config["slug"] . '_layout_1',
				'data-xlwcty-conditional-value' => json_encode( array( 'image_content', '2c' ) ),
			)
		),
		array(
			'name'        => __( 'Content', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_editor_1',
			'row_classes' => array( 'xlwcty_no_border' ),
			'type'        => 'wysiwyg',
			'before_row'  => array( 'XLWCTY_Admin_CMB2_Support', 'before_wysiwyg' ),
			'after_row'   => array( 'XLWCTY_Admin_CMB2_Support', 'after_wysiwyg' ),
			'options'     => array(
				'wpautop'       => true,
				'media_buttons' => false,
				'tabindex'      => '',
				'textarea_rows' => 10,
				'editor_css'    => '',
				'editor_class'  => '',
				'teeny'         => false,
				'dfw'           => false,
				'tinymce'       => true,
				'quicktags'     => true
			),
			'attributes'  => array(
				'data-conditional-id'           => $config["slug"] . '_enable_1',
				'data-conditional-value'        => '1',
				'data-xlwcty-conditional-id'    => $config["slug"] . '_layout_1',
				'data-xlwcty-conditional-value' => json_encode( array( 'image_content', 'content_image' ) ),
			),
		),
		array(
			'name'        => __( 'Right Image', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_img_r_source_1',
			'type'        => 'file',
			'options'     => array(
				'url' => false,
			),
			'text'        => array(
				'add_upload_file_text' => 'Add/ Update Imade'
			),
			'row_classes' => array( 'xlwcty_no_border' ),
			'attributes'  => array(
				'data-conditional-id'           => $config["slug"] . '_enable_1',
				'data-conditional-value'        => '1',
				'data-xlwcty-conditional-id'    => $config["slug"] . '_layout_1',
				'data-xlwcty-conditional-value' => json_encode( array( '2c', 'content_image' ) ),
			)
		),
		array(
			'name'        => __( 'Right Image link ', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_img_r_link_1',
			'type'        => 'text',
			'row_classes' => array( 'xlwcty_hide_label', 'xlwcty_pt0' ),
			'before'      => '<p>Link</p>',
			'attributes'  => array(
				'data-conditional-id'           => $config["slug"] . '_enable_1',
				'data-conditional-value'        => '1',
				'data-xlwcty-conditional-id'    => $config["slug"] . '_layout_1',
				'data-xlwcty-conditional-value' => json_encode( array( '2c', 'content_image' ) ),
			)
		),
		array(
			'name'        => __( 'Show Button', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_show_btn_1',
			'type'        => 'radio_inline',
			'options'     => array(
				'yes' => __( 'Yes', 'woo-thank-you-page-nextmove-lite' ),
				'no'  => __( 'No', 'woo-thank-you-page-nextmove-lite' ),
			),
			'row_classes' => array( 'xlwcty_no_border' ),
			'attributes'  => array(
				'data-conditional-id'    => $config["slug"] . '_enable_1',
				'data-conditional-value' => '1',
			),
		),
		array(
			'name'        => __( 'Button Text', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_btn_text_1',
			'type'        => 'text',
			'row_classes' => array( 'xlwcty_hide_label', 'xlwcty_no_border', 'xlwcty_pt0' ),
			'before'      => '<p>Text</p>',
			'attributes'  => array(
				'data-conditional-id'           => $config["slug"] . '_enable_1',
				'data-conditional-value'        => '1',
				'data-xlwcty-conditional-id'    => $config["slug"] . '_show_btn_1',
				'data-xlwcty-conditional-value' => "yes",
			),
		),
		array(
			'name'        => __( 'Button Link', 'woo-thank-you-page-nextmove-lite' ),
			'desc'        => __( 'Use ', 'woo-thank-you-page-nextmove-lite' ) . " {{home_url}}, {{shop_url}} " . __( 'for dynamic links.', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_btn_link_1',
			'desc'        => '<a href="javascript:void(0);" onclick="xlwcty_show_tb(\'Merge Tags\',\'xlwcty_merge_tags_invenotry_bar_help\');">Dynamic merge tags list</a>',
			'type'        => 'text',
			'row_classes' => array( 'xlwcty_no_border', 'xlwcty_hide_label', 'xlwcty_pt0' ),
			'before'      => '<p>Link</p>',
			'attributes'  => array(
				'data-conditional-id'           => $config["slug"] . '_enable_1',
				'data-conditional-value'        => '1',
				'data-xlwcty-conditional-id'    => $config["slug"] . '_show_btn_1',
				'data-xlwcty-conditional-value' => "yes",
			)
		),
		array(
			'name'        => __( 'Button Font Size', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_btn_font_size_1',
			'type'        => 'text_small',
			'row_classes' => array( 'xlwcty_no_border', 'xlwcty_hide_label', 'xlwcty_pt0', 'xlwcty_combine_3_field_start' ),
			'before'      => '<p>Font Size (px)</p>',
			'attributes'  => array(
				'type'                          => 'number',
				'min'                           => '1',
				'pattern'                       => '\d*',
				'data-conditional-id'           => $config["slug"] . '_enable_1',
				'data-conditional-value'        => '1',
				'data-xlwcty-conditional-id'    => $config["slug"] . '_show_btn_1',
				'data-xlwcty-conditional-value' => "yes",
			),
		),
		array(
			'name'        => __( 'Button Text Color', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_btn_color_1',
			'type'        => 'colorpicker',
			'row_classes' => array( 'xlwcty_no_border', 'xlwcty_hide_label', 'xlwcty_pt0', 'xlwcty_combine_3_field_middle' ),
			'before'      => '<p>Text Color</p>',
			'attributes'  => array(
				'data-conditional-id'           => $config["slug"] . '_enable_1',
				'data-conditional-value'        => '1',
				'data-xlwcty-conditional-id'    => $config["slug"] . '_show_btn_1',
				'data-xlwcty-conditional-value' => "yes",
			),
		),
		array(
			'name'        => __( 'Button Background Color', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_btn_bg_color_1',
			'type'        => 'colorpicker',
			'row_classes' => array( 'xlwcty_no_border', 'xlwcty_hide_label', 'xlwcty_pt0', 'xlwcty_combine_3_field_end' ),
			'before'      => '<p>Background Color</p>',
			'attributes'  => array(
				'data-conditional-id'           => $config["slug"] . '_enable_1',
				'data-conditional-value'        => '1',
				'data-xlwcty-conditional-id'    => $config["slug"] . '_show_btn_1',
				'data-xlwcty-conditional-value' => "yes",
			),
		),
		array(
			'name'        => __( 'Border', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_border_style_1',
			'type'        => 'select',
			'row_classes' => array( 'xlwcty_no_border', 'xlwcty_select_small', 'xlwcty_combine_3_field_start' ),
			'before'      => '<p class="xlwcty_mt5 xlwcty_mb5">Style</p>',
			'options'     => array(
				'dotted' => __( 'Dotted', 'woo-thank-you-page-nextmove-lite' ),
				'dashed' => __( 'Dashed', 'woo-thank-you-page-nextmove-lite' ),
				'solid'  => __( 'Solid', 'woo-thank-you-page-nextmove-lite' ),
				'double' => __( 'Double', 'woo-thank-you-page-nextmove-lite' ),
				'none'   => __( 'None', 'woo-thank-you-page-nextmove-lite' ),
			),
			'attributes'  => array(
				'data-conditional-id'    => $config["slug"] . '_enable_1',
				'data-conditional-value' => '1',
			),
		),
		array(
			'name'        => __( 'Border Width', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_border_width_1',
			'type'        => 'text_small',
			'row_classes' => array( 'xlwcty_no_border', 'xlwcty_hide_label', 'xlwcty_combine_3_field_middle' ),
			'before'      => '<p class="xlwcty_mt5 xlwcty_mb5">Width (px)</p>',
			'attributes'  => array(
				'type'                   => 'number',
				'min'                    => '0',
				'pattern'                => '\d*',
				'data-conditional-id'    => $config["slug"] . '_enable_1',
				'data-conditional-value' => '1',
			),
		),
		array(
			'name'        => __( 'Border Color', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_border_color_1',
			'type'        => 'colorpicker',
			'row_classes' => array( 'xlwcty_border_top', 'xlwcty_hide_label', 'xlwcty_combine_3_field_end' ),
			'before'      => '<p class="xlwcty_mt5 xlwcty_mb5">Color</p>',
			'attributes'  => array(
				'data-conditional-id'    => $config["slug"] . '_enable_1',
				'data-conditional-value' => '1',
			),
		),
		array(
			'name'        => __( 'Background', 'woo-thank-you-page-nextmove-lite' ),
			'desc'        => __( 'Component background color', 'woo-thank-you-page-nextmove-lite' ),
			'id'          => $config["slug"] . '_component_bg_1',
			'type'        => 'colorpicker',
			'row_classes' => array(),
			'attributes'  => array(
				'data-conditional-id'    => $config["slug"] . '_enable_1',
				'data-conditional-value' => '1',
			),
			'after_row'   => array( 'XLWCTY_Admin_CMB2_Support', 'cmb_after_row_cb' ),
		),
	)
);
$config['default']  = array(
	"heading"            => "",
	"heading_font_size"  => "20",
	"heading_alignment"  => "center",
	"desc"               => "",
	"desc_alignment"     => "center",
	"layout"             => "full",
	"img_source"         => "",
	"img_link"           => "",
	"img_cont_ratio"     => "50_50",
	"img_l_source"       => "",
	"img_l_link"         => "#",
	"editor"             => "",
	"show_btn"           => "no",
	"btn_text"           => "Start Shopping",
	"btn_link"           => "{{shop_url}}",
	"btn_font_size"      => "20",
	"btn_color"          => "#ffffff",
	"btn_bg_color"       => "#1291ff",
	"img_r_source"       => "",
	"img_r_link"         => "#",
	"border_style"       => "solid",
	"border_width"       => "1",
	"border_color"       => "#d9d9d9",
	"component_bg_color" => "#ffffff",
);

return $config;
