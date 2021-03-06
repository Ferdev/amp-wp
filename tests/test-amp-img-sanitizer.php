<?php

class AMP_Img_Sanitizer_Test extends WP_UnitTestCase {
	public static function force_remove_extraction_callbacks() {
		remove_all_filters( 'amp_extract_image_dimensions_batch' );
	}

	public function setUp() {
		parent::setUp();
		add_action( 'amp_extract_image_dimensions_batch_callbacks_registered', array( __CLASS__, 'force_remove_extraction_callbacks' ) );
	}

	public function get_data() {
		return array(
			'no_images'                                => array(
				'<p>Lorem Ipsum Demet Delorit.</p>',
				'<p>Lorem Ipsum Demet Delorit.</p>',
			),

			'image_without_src'                        => array(
				'<p><img width="300" height="300" /></p>',
				'<p></p>',
			),

			'image_with_empty_src'                     => array(
				'<p><img src="" width="300" height="300" /></p>',
				'<p></p>',
			),

			'image_with_layout'                        => array(
				'<img src="http://placehold.it/100x100" data-amp-layout="fill" width="100" height="100" />',
				'<amp-img src="http://placehold.it/100x100" layout="fill" width="100" height="100" class="amp-wp-enforced-sizes"></amp-img>',
			),

			'image_with_spaces_only_src'               => array(
				'<p><img src="    " width="300" height="300" /></p>',
				'<p></p>',
			),

			'image_with_empty_width_and_height'        => array(
				'<p><img src="http://placehold.it/300x300" width="" height="" /></p>',
				'<p><amp-img src="http://placehold.it/300x300" width="600" height="400" class="amp-wp-unknown-size amp-wp-enforced-sizes" layout="intrinsic"></amp-img></p>',
			),

			'image_with_empty_width'                   => array(
				'<p><img src="http://placehold.it/300x300" width="" height="300" /></p>',
				'<p><amp-img src="http://placehold.it/300x300" width="600" height="300" class="amp-wp-unknown-size amp-wp-unknown-width amp-wp-enforced-sizes" layout="intrinsic"></amp-img></p>',
			),

			'image_with_empty_height'                  => array(
				'<p><img src="http://placehold.it/300x300" width="300" height="" /></p>',
				'<p><amp-img src="http://placehold.it/300x300" width="300" height="400" class="amp-wp-unknown-size amp-wp-unknown-height amp-wp-enforced-sizes" layout="intrinsic"></amp-img></p>',
			),

			'image_with_zero_width'                    => array(
				'<p><img src="http://placehold.it/300x300" width="0" height="300" /></p>',
				'<p><amp-img src="http://placehold.it/300x300" width="0" height="300" class="amp-wp-enforced-sizes"></amp-img></p>',
			),

			'image_with_zero_width_and_height'         => array(
				'<p><img src="http://placehold.it/300x300" width="0" height="0" /></p>',
				'<p><amp-img src="http://placehold.it/300x300" width="0" height="0" class="amp-wp-enforced-sizes"></amp-img></p>',
			),

			'image_with_decimal_width'                 => array(
				'<p><img src="http://placehold.it/300x300" width="299.5" height="300" /></p>',
				'<p><amp-img src="http://placehold.it/300x300" width="299.5" height="300" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-img></p>',
			),

			'image_with_self_closing_tag'              => array(
				'<img src="http://placehold.it/350x150" width="350" height="150" alt="Placeholder!" />',
				'<amp-img src="http://placehold.it/350x150" width="350" height="150" alt="Placeholder!" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-img>',
			),

			'image_with_no_end_tag'                    => array(
				'<img src="http://placehold.it/350x150" width="350" height="150" alt="Placeholder!">',
				'<amp-img src="http://placehold.it/350x150" width="350" height="150" alt="Placeholder!" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-img>',
			),

			'image_with_end_tag'                       => array(
				'<img src="http://placehold.it/350x150" width="350" height="150" alt="Placeholder!"></img>',
				'<amp-img src="http://placehold.it/350x150" width="350" height="150" alt="Placeholder!" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-img>',
			),

			'image_with_on_attribute'                  => array(
				'<img src="http://placehold.it/350x150" on="tap:my-lightbox" width="350" height="150" />',
				'<amp-img src="http://placehold.it/350x150" on="tap:my-lightbox" width="350" height="150" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-img>',
			),

			'image_with_blacklisted_attribute'         => array(
				'<img src="http://placehold.it/350x150" width="350" height="150" style="border: 1px solid red;" />',
				'<amp-img src="http://placehold.it/350x150" width="350" height="150" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-img>',
			),

			'image_with_no_dimensions_is_forced_dimensions' => array(
				'<img src="http://placehold.it/350x150" />',
				'<amp-img src="http://placehold.it/350x150" width="600" height="400" class="amp-wp-unknown-size amp-wp-enforced-sizes" layout="intrinsic"></amp-img>',
			),

			'image_with_sizes_attribute_is_overridden' => array(
				'<img src="http://placehold.it/350x150" width="350" height="150"  />',
				'<amp-img src="http://placehold.it/350x150" width="350" height="150" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-img>',
			),

			'gif_image_conversion'                     => array(
				'<img src="http://placehold.it/350x150.gif" width="350" height="150" alt="Placeholder!" />',
				'<amp-anim src="http://placehold.it/350x150.gif" width="350" height="150" alt="Placeholder!" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-anim>',
			),

			'gif_image_url_with_querystring'           => array(
				'<img src="http://placehold.it/350x150.gif?foo=bar" width="350" height="150" alt="Placeholder!" />',
				'<amp-anim src="http://placehold.it/350x150.gif?foo=bar" width="350" height="150" alt="Placeholder!" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-anim>',
			),

			'multiple_same_image'                      => array(
				'<img src="http://placehold.it/350x150" width="350" height="150" />
<img src="http://placehold.it/350x150" width="350" height="150" />
<img src="http://placehold.it/350x150" width="350" height="150" />
<img src="http://placehold.it/350x150" width="350" height="150" />
				',
				'<amp-img src="http://placehold.it/350x150" width="350" height="150" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-img><amp-img src="http://placehold.it/350x150" width="350" height="150" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-img><amp-img src="http://placehold.it/350x150" width="350" height="150" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-img><amp-img src="http://placehold.it/350x150" width="350" height="150" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-img>',
			),

			'multiple_different_images'                => array(
				'<img src="http://placehold.it/350x150" width="350" height="150" />
<img src="http://placehold.it/360x160" width="360" height="160" />
<img src="http://placehold.it/370x170" width="370" height="170" />
<img src="http://placehold.it/380x180" width="380" height="180" />',
				'<amp-img src="http://placehold.it/350x150" width="350" height="150" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-img><amp-img src="http://placehold.it/360x160" width="360" height="160" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-img><amp-img src="http://placehold.it/370x170" width="370" height="170" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-img><amp-img src="http://placehold.it/380x180" width="380" height="180" class="amp-wp-enforced-sizes" layout="intrinsic"></amp-img>',
			),
		);
	}

	/**
	 * @dataProvider get_data
	 */
	public function test_converter( $source, $expected ) {
		$dom = AMP_DOM_Utils::get_dom_from_content( $source );
		$sanitizer = new AMP_Img_Sanitizer( $dom );
		$sanitizer->sanitize();
		$content = AMP_DOM_Utils::get_content_from_dom( $dom );
		$this->assertEquals( $expected, $content );
	}

	public function test_no_gif_no_image_scripts() {
		$source = '<img src="http://placehold.it/350x150.png" width="350" height="150" alt="Placeholder!" />';
		$expected = array();

		$dom = AMP_DOM_Utils::get_dom_from_content( $source );
		$sanitizer = new AMP_Img_Sanitizer( $dom );
		$sanitizer->sanitize();

		$whitelist_sanitizer = new AMP_Tag_And_Attribute_Sanitizer( $dom );
		$whitelist_sanitizer->sanitize();

		$scripts = array_merge(
			$sanitizer->get_scripts(),
			$whitelist_sanitizer->get_scripts()
		);
		$this->assertEquals( $expected, $scripts );
	}

	public function test_no_gif_image_scripts() {
		$source = '<img src="http://placehold.it/350x150.gif" width="350" height="150" alt="Placeholder!" />';
		$expected = array( 'amp-anim' => true );

		$dom = AMP_DOM_Utils::get_dom_from_content( $source );
		$sanitizer = new AMP_Img_Sanitizer( $dom );
		$sanitizer->sanitize();

		$whitelist_sanitizer = new AMP_Tag_And_Attribute_Sanitizer( $dom );
		$whitelist_sanitizer->sanitize();

		$scripts = array_merge(
			$sanitizer->get_scripts(),
			$whitelist_sanitizer->get_scripts()
		);
		$this->assertEquals( $expected, $scripts );
	}
}
