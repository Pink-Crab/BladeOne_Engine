<?php

declare(strict_types=1);

/**
 * Application test
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\BladeOne
 */

namespace PinkCrab\BladeOne\Tests;

use WP_UnitTestCase;
use eftec\bladeone\BladeOne;
use Gin0115\WPUnit_Helpers\Output;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\BladeOne\BladeOne_Engine;
use PinkCrab\Perique\Interfaces\Renderable;
use PinkCrab\Perique\Application\App_Factory;
use PinkCrab\BladeOne\BladeOne as BladeOne_Module;

/**
 * @group application
 */
class Test_As_Application extends WP_UnitTestCase {

	use App_Helper_Trait;

	/**
	 * On tear down, unset app instance.
	 *
	 * @return void
	 */
	public function tear_down(): void {
		parent::tear_down();
		$this->unset_app_instance();
		wp_set_current_user( 0 );
	}

	/** @testdox It should be possible to render a template using only its filename and pass values to the view to be rendered */
	public function test_render_template_using_only_file_name() {
		$app = $this->pre_populated_app_provider();

		$output = $app::view()->render( 'testview', array( 'foo' => 'bar' ), false );
		$this->assertEquals( 'bar', $output );
	}

	/** @testdox It should be possible to set a custom template path when adding BladeOne as a module */
	public function test_set_custom_template_path() {
		$app = ( new App_Factory( \FIXTURES_PATH ) )
			->default_setup()
			->module(
				BladeOne_Module::class,
				function( BladeOne_Module $e ) {
					$e->template_path( \FIXTURES_PATH . 'views/custom-path' );
					$e->compiled_path( \FIXTURES_PATH . 'cache' );
					return $e;
				}
			)
			->boot();
		do_action( 'init' );

		$output = $app::view()->render( 'template', array( 'custom_path' => 'bar' ), false );
		$this->assertEquals( 'bar', $output );
	}

	/** @testdox It should be possible to configure the template and compiled paths, mode and access the BladeOne_Engine from the Modules config callback. */
	public function test_configure_bladeone_module() {
		$app = ( new App_Factory( \FIXTURES_PATH ) )
			->default_setup()
			->module(
				BladeOne_Module::class,
				function( BladeOne_Module $e ) {
					$e->template_path( \FIXTURES_PATH . 'views/custom-path' );
					$e->compiled_path( \FIXTURES_PATH . 'cache' );
					$e->mode( BladeOne::MODE_DEBUG );
					$e->config(
						function( BladeOne_Engine $engine ) {
							$engine->directive(
								'bar',
								function( $expression ) {
									return "<?php echo 'barf'; ?>";
								}
							);
							return $engine;
						}
					);
					return $e;
				}
			)
			->boot();
		do_action( 'init' );

		$blade = $app::make( Renderable::class );

		// Check mode is debug.
		$this->assertEquals( BladeOne::MODE_DEBUG, $blade->getMode() );

		// Check the custom directive is added.
		$this->assertEquals(
			"<?php echo 'barf'; ?>",
			$blade->compileString( '@bar()' )
		);

		// Check that both paths are set.
		$health_check = Output::buffer(
			function() use ( $blade ) {
				$result = $blade->checkHealthPath();
				$this->assertTrue( $result );
			}
		);

		$this->assertStringContainsString( \sprintf( 'Compile-path [%s] is a folder ', \FIXTURES_PATH . 'cache' ), $health_check );
		$this->assertStringContainsString( \sprintf( 'Template-path (view) [%s] is a folder ', \FIXTURES_PATH . 'views/custom-path' ), $health_check );
	}

	/** @testdox It should be possible to define a compiled path and have it created if it doesnt exist. */
	public function test_create_compiled_path_if_not_exists() {
		$this->unset_app_instance();

		$cached_path = \FIXTURES_PATH . 'cache/' . time();

		$app = ( new App_Factory( \FIXTURES_PATH ) )
			->default_setup()
			->module(
				BladeOne_Module::class,
				function( BladeOne_Module $e ) use ( $cached_path ) {
					return $e
						->template_path( \FIXTURES_PATH . 'views/custom-path' )
						->compiled_path( $cached_path );
				}
			)
			->boot();
		do_action( 'init' );

		$this->assertTrue( \file_exists( $cached_path ) );
	}

	/** @testdox It should be possible to render a component nested inside another component */
	public function test_can_render_nested_component(): void {
		$app = $this->pre_populated_app_provider();

		$value = $app::view()->render( 'testnestedcomponents', array(), false );

		$this->assertStringContainsString(
			'<input name="a" id="b" value="c" type="d" />',
			$value
		);
	}

	/** @testdox It should be possible to render an nested view model using $this->view_model($instance) */
	public function test_can_render_nested_view_model(): void {
		$app = $this->pre_populated_app_provider();

		$value = $app::view()->render( 'testrendersviewmodel', array(), false );

		$this->assertStringContainsString( 'woo', $value );
	}

	/** @testdox When a string is escaped, it should use the default WP esc_html */
	public function test_can_escape_string(): void {
		$app = $this->pre_populated_app_provider();

		$called_esc_html = false;
		add_filter(
			'esc_html',
			function( $value ) use ( &$called_esc_html ) {
				$called_esc_html = true;
				return $value;
			}
		);

		$app::view()->render( 'testview', array( 'foo' => 'woo' ), false );
		$this->assertTrue( $called_esc_html );
	}

	/** @testdox It should be possible to set any function as the esc function */
	public function test_set_custom_esc_function(): void {
		$app = $this->pre_populated_app_provider();

		$called_esc_html = false;
		add_filter(
			'attribute_escape',
			function( $value ) use ( &$called_esc_html ) {
				$called_esc_html = true;
				return $value;
			}
		);

		$app::view()->engine()->get_blade()->set_esc_function( 'esc_attr' );
		$app::view()->render( 'testview', array( 'foo' => 'woo' ), false );
		$this->assertTrue( $called_esc_html );
	}

	/** @testdox It should be possible to render an nested view model using @viewModel($instance) */
	public function test_can_render_nested_view_model_directive(): void {
		$app = $this->pre_populated_app_provider();

		$value = $app::view()->render( 'testrendersviewmodeldirective', array(), false );

		$this->assertStringContainsString( 'woo', $value );
	}

	/** @testdox It should be possible to render a component nested inside another component using @component($instance) */
	public function test_can_render_nested_component_using_directive(): void {
		$app = $this->pre_populated_app_provider();

		$value = $app::view()->render( 'testnestedcomponentsdirective', array(), false );

		$this->assertStringContainsString(
			'<input name="a" id="b" value="c" type="d" />',
			$value
		);
	}

	/**
	 * @testdox By not passing the view or compliled path to the use method on boot strap these should be implied.
	 */
	public function test_can_use_default_paths(): void {

		$app = ( new App_Factory( \FIXTURES_PATH ) )
			->default_setup()
			->module( BladeOne_Module::class )
			->app_config( array() )
			->boot();

		do_action( 'init' ); // Boots Perique
		do_action( 'wp_loaded' ); // Triggers the blade one config once all is loaded (see issue 13)

		// Get blade instance
		$blade = $app::view()->engine()->get_blade();
		// Assert the template path is correct.
		$this->assertEquals( \FIXTURES_PATH . 'views/', $blade->get_template_paths()[0] );

		// Assert the compiled path is correct.
		$path = \wp_upload_dir()['basedir'] . '/blade-cache';
		$this->assertEquals( $path, Objects::get_property( $blade, 'compiledPath' ) );
	}

	/** @testdox It should be possible to get the details of a logged in user */
	public function test_can_get_user_details(): void {
		$user = $this->factory()->user->create_and_get( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user->ID );

		$app = $this->pre_populated_app_provider();

		$engine = $app::view()->engine()->get_blade();
		// dd($engine);
		$current_user        = $engine->getCurrentUser();
		$current_role        = $engine->getCurrentRole();
		$current_permissions = $engine->getCurrentPermission();

		// Check all exist.
		$this->assertNotEquals( '', $current_user );
		$this->assertSame( $user->user_login, $current_user );

		$this->assertNotEquals( '', $current_role );
		$this->assertSame( 'administrator', $current_role );

		$this->assertNotEmpty( $current_permissions );
		// Loop through all permissions of user and check they are in the array.
		foreach ( $user->allcaps as $cap => $value ) {
			$this->assertContains( $cap, $current_permissions );
		}

	}

	/** @testdox When no user is logged in, this should be reflected in blades auth data. */
	public function test_can_get_user_details_when_no_user_logged_in(): void {
		wp_set_current_user( 0 );
		$app = $this->pre_populated_app_provider();

		$engine = $app::view()->engine()->get_blade();

		$current_user        = $engine->getCurrentUser();
		$current_role        = $engine->getCurrentRole();
		$current_permissions = $engine->getCurrentPermission();

		// Check all exist.
		$this->assertEquals( '', $current_user );

		$this->assertEquals( '', $current_role );

		$this->assertEmpty( $current_permissions );
	}

	/** @testdox It should be possible to use WP user roles to make some aspects of the template render logged in as administrator*/
	public function test_user_auth_logged_in_rendered_admin(): void {
		$user = $this->factory()->user->create_and_get( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user->ID );

		$app = $this->pre_populated_app_provider();

		// using @auth directive with role
		$output = $app::view()->render( 'testauthrole', array(), false );
		$this->assertStringContainsString( 'Administrator', $output );

		// using @auth directive no role, just "logged in"
		$output = $app::view()->render( 'testauthany', array(), false );
		$this->assertStringContainsString( 'Is Logged In', $output );
		$this->assertStringContainsString( 'Isn\'t Guest', $output );

		// using @can directive
		$output = $app::view()->render( 'testroles', array(), false );
		$this->assertStringContainsString( 'can_manage_options', $output );
	}

	/** @testdox It should be possible to use WP user roles to make some aspects of the template render logged in as edior*/
	public function test_user_auth_logged_in_rendered_editor(): void {
		$user = $this->factory()->user->create_and_get( array( 'role' => 'editor' ) );
		wp_set_current_user( $user->ID );

		$app = $this->pre_populated_app_provider();

		// using @auth directive with role
		$output = $app::view()->render( 'testauthrole', array(), false );
		$this->assertStringContainsString( 'Editor', $output );

		// using @auth directive no role, just "logged in"
		$output = $app::view()->render( 'testauthany', array(), false );
		$this->assertStringContainsString( 'Is Logged In', $output );
		$this->assertStringContainsString( 'Isn\'t Guest', $output );

		// using @can directive
		$output = $app::view()->render( 'testroles', array(), false );
		$this->assertStringContainsString( 'can_edit_posts', $output );
	}

	/** @testdox It should be possible to use WP user roles to make some aspects of the template render */
	public function test_user_auth_logged_out_rendered(): void {
		wp_set_current_user( 0 );

		$app = $this->pre_populated_app_provider();

		// using @auth directive with role
		$output = $app::view()->render( 'testauthrole', array(), false );
		$this->assertStringContainsString( '(not administrator)', $output );
		$this->assertStringContainsString( '(neither administrator or editor)', $output );

		// using @auth directive no role, just "logged in"
		$output = $app::view()->render( 'testauthany', array(), false );
		$this->assertStringContainsString( 'Isn\'t Logged In', $output );
		$this->assertStringContainsString( 'Is Guest', $output );

		// using @can directive
		$output = $app::view()->render( 'testroles', array(), false );
		$this->assertStringContainsString( 'cannot_manage_options', $output );
	}

	/**
	 * @testdox It should be possible to add a WP Nonce field to a template.
	 * @dataProvider nonce_provider
	 */
	public function test_can_add_nonce_to_template( string $action, string $name, bool $has_ref ): void {
		$app    = $this->pre_populated_app_provider();
		$output = $app::view()->render( 'testnonce', array( 'type' => $action ), false );

		// Regenerate the nonce to make sure it's not the same as the one in the template.
		$nonce = wp_create_nonce( $action );

		$this->assertStringContainsString( $nonce, $output );
		$this->assertStringContainsString( 'id="' . $name . '"', $output );
		$this->assertStringContainsString( 'name="' . $name . '"', $output );

		if ( $has_ref ) {
			$this->assertStringContainsString( '_wp_http_referer', $output );
		} else {
			$this->assertStringNotContainsString( '_wp_http_referer', $output );
		}
	}

	/** DataProvider for test_can_add_nonce_to_template */
	public function nonce_provider(): array {
		return array(
			array( 'with_ref_nonce', 'with_referer', true ),
			array( 'without_ref_nonce', 'without_referer', false ),
			array( 'def_ref_nonce', '_pcnonce', true ),
		);
	}


}
