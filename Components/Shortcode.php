<?php

namespace StudioVisual\Support\Components;

use StudioVisual\Support\Contracts\TemplateInterface;

class Shortcode implements TemplateInterface
{
	protected $name;
	protected $path;
	protected $data;
	protected $atts;

	public function __construct( $name, $path, $data = [], $atts = [] ) {
		$this->name = StringFormat::convert_camel_case( $name );
		$this->path = $path;
		$this->data = $data;
		$this->atts = $atts;
		$this->register();
	}

	public function register() {
		add_shortcode( $this->name, array( $this, 'build' ) );
	}

	public function getTemplatePath() {
		return $this->path . '/' . str_replace('_', '-', $this->name ) . '.php';
	}

	public function build( $atts, $content = null ) {

		$atts = shortcode_atts( $this->atts, $atts, $this->name );

		foreach ( $atts as $attr_name => $attr_value ) {
			$$attr_name = $attr_value;
		}

		foreach ( $this->get_data() as $key => $value ) {
			$$key = $value;
		}

		ob_start();
		include $this->getTemplatePath();
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function render() {
		echo do_shortcode( "[{$this->name}]" );
	}

	public function print_out( $data ) {
		$this->data = $data;
	}

	public function get_data() {
		return $this->data;
	}

	public function add_attr( $attr ) {
		$this->atts = array_merge( $this->atts, $attr );
	}

	public function the_attr( $attr ) {
		echo esc_attr__( $attr );
	}

	public function is_present() {
		global $post;

		if ( ! is_object( $post ) ) {
			return false;
		}

		if ( ! has_shortcode( $post->post_content, $this->name ) ) {
			return false;
		}

		if ( ! has_shortcode( $post->post_content, str_replace( '-', '_', $this->name ) ) ) {
			return false;
		}

		return true;
	}

	public function has_data( $data ) {
		if ( isset( $data ) && ! empty( $data ) ) {
			return true;
		} else {
			return false;
		}
	}
}
