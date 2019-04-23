<?php
defined( 'ABSPATH' ) || exit;

if ( $this->is_enable() ) {
	if ( $this->data->layout == "full" ) {
		include __DIR__ . "/full.php";
	}
	if ( $this->data->layout == "2c" ) {
		include __DIR__ . "/2c.php";
	}
	if ( $this->data->layout == "image_content" ) {
		include __DIR__ . "/left-image.php";
	}
	if ( $this->data->layout == "content_image" ) {
		include __DIR__ . "/right-image.php";
	}
}