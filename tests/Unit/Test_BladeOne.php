<?php

declare(strict_types=1);

/**
 * Tests the BladeOne Module
 *
 * @since 2.0.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\BladeOne
 */

namespace PinkCrab\BladeOne\Tests\Unit;

use WP_UnitTestCase;
use BadMethodCallException;
use eftec\bladeone\BladeOne;
use PinkCrab\Loader\Hook_Loader;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\BladeOne\BladeOne_Engine;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\BladeOne\Tests\Fixtures\Input;
use PinkCrab\Perique\Application\App_Config;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Services\View\View_Model;
use PinkCrab\BladeOne\BladeOne as BladeOne_Module;
use PinkCrab\Perique\Services\View\Component\Component_Compiler;

/**
 * @group unit
 */
class Test_BladeOne extends WP_UnitTestCase {

	/** @testdox If when creating instance of Module for running config, an instance of BladeOne is not created and exception should be thrown. */
	public function test_if_bladeone_not_created_exception_thrown(): void {
		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'Unable to create BladeOne_Engine instance to configure instance' );

		$di = $this->createMock( DI_Container::class );
		$di->method( 'create' )->willReturnCallback(
			function( $class ) {
				return new \stdClass();
			}
		);

		$module = new BladeOne_Module();

		// Run the pre_register callback.
		$module->pre_register( new App_Config(), $this->createMock( Hook_Loader::class ), $di );
	}
}
