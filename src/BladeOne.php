<?php

declare( strict_types=1 );

/**
 * The BladeOne Module for Perique.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\BladeOne_Engine
 */

namespace PinkCrab\BladeOne;

use PinkCrab\Loader\Hook_Loader;
use PinkCrab\BladeOne\BladeOne_Engine;
use PinkCrab\Perique\Interfaces\Module;
use PinkCrab\BladeOne\PinkCrab_BladeOne;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique\Interfaces\Renderable;
use PinkCrab\Perique\Application\App_Config;
use PinkCrab\Perique\Interfaces\DI_Container;

/**
 * BladeOne Module for Perique.
 */
class BladeOne implements Module {

	private ?string $template_path = null;
	private ?string $compiled_path = null;

	private int $mode = PinkCrab_BladeOne::MODE_AUTO;

	/**
	 * Holds the config closure.
	 *
	 * @var ?\Closure(BladeOne_Engine):BladeOne_Engine
	 */
	private $config = null;

	/**
	 * Set the template path.
	 *
	 * @param string $template_path
	 * @return self
	 */
	public function template_path( string $template_path ): self {
		$this->template_path = $template_path;
		return $this;
	}

	/**
	 * Set the compiled path.
	 *
	 * @param string $compiled_path
	 * @return self
	 */
	public function compiled_path( string $compiled_path ): self {
		$this->compiled_path = $compiled_path;
		return $this;
	}

	/**
	 * Set the mode.
	 *
	 * @param integer $mode
	 * @return self
	 */
	public function mode( int $mode ): self {
		$this->mode = $mode;
		return $this;
	}

	/**
	 * Provider config.
	 *
	 * @param \Closure(BladeOne_Engine):BladeOne_Engine $config
	 * @return self
	 */
	public function config( \Closure $config ): self {
		$this->config = $config;
		return $this;
	}

	/**
	 * Creates the shared instance of the module and defines the
	 * DI Rules to use the BladeOne_Engine.
	 *
	 * @pram App_Config   $config
	 * @pram Hook_Loader  $loader
	 * @pram DI_Container $di_container
	 * @return void
	 */
	public function pre_boot( App_Config $config, Hook_Loader $loader, DI_Container $di_container ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceBeforeLastUsed
		// @codeCoverageIgnoreStart
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			// @phpstan-ignore-next-line
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		// @codeCoverageIgnoreEnd
		\WP_Filesystem();
		global $wp_filesystem;

		// If we dont have an instance of the WP_Filesystem, throw an exception.
		if ( ! $wp_filesystem instanceof \WP_Filesystem_Base ) {
			// @codeCoverageIgnoreStart
			throw new \RuntimeException( 'Unable to create WP_Filesystem instance' );
			// @codeCoverageIgnoreEnd
		}

		$wp_upload_dir = wp_upload_dir();
		$compiled_path = $this->compiled_path ?? sprintf( '%1$s%2$sblade-cache', $wp_upload_dir['basedir'], \DIRECTORY_SEPARATOR ); // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceBeforeLastUsed
		$instance      = new PinkCrab_BladeOne(
			$this->template_path ?? $config->path( 'view' ),
			$compiled_path,
			$this->mode
		);

		$instance->setAuth( ...$this->get_auth_data() );

		// Create the compiled path if it does not exist.
		if ( ! $wp_filesystem->exists( $compiled_path ) ) {

			// Create the directory.
			$wp_filesystem->mkdir( $compiled_path ); // phpcs:ignore WordPress.VIP.MkdirPermissions
		}

		$di_container->addRule(
			BladeOne_Engine::class,
			array(
				'constructParams' => array(
					$instance,
				),
				'call'            => array(
					array( 'allow_pipe', array() ),
				),
				'shared'          => true,
			)
		);

		$di_container->addRule(
			Renderable::class,
			array(
				'instanceOf' => BladeOne_Engine::class,
				'shared'     => true,
			)
		);

		$di_container->addRule(
			View::class,
			array(
				'substitutions' => array(
					Renderable::class => BladeOne_Engine::class,
				),
				'shared'        => true,
			)
		);
	}

	/**
	 * Gets the current logged in user details
	 *
	 * @return array{0:string, 1:string, 2:string[]}
	 */
	private function get_auth_data(): array {

		// @codeCoverageIgnoreStart
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			// @phpstan-ignore-next-line
			require_once ABSPATH . 'wp-includes/pluggable.php';
		}
		// @codeCoverageIgnoreEnd

		$user = \wp_get_current_user();
		return array(
			0 !== $user->ID ? $user->user_login : '',
			0 !== $user->ID ? $user->roles[0] : '',
			0 !== $user->ID ? array_keys( array_filter( $user->allcaps ) ) : array(),
		);
	}

	/**
	 * Allows for the config to be passed to the provider, before its used.
	 *
	 * @pram App_Config $config
	 * @pram Hook_Loader $loader
	 * @pram DI_Container $di_container
	 * @return void
	 */
	public function pre_register( App_Config $config, Hook_Loader $loader, DI_Container $di_container ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceBeforeLastUsed

		$provider = $di_container->create( BladeOne_Engine::class );

		// If dont have an instance of BladeOne_Engine, return.
		if ( ! $provider instanceof BladeOne_Engine ) {
			throw new \RuntimeException( 'Unable to create BladeOne_Engine instance to configure instance' );
		}

		// Pass the config to the provider, if set.
		if ( ! is_null( $this->config ) ) {
			\call_user_func( $this->config, $provider );
		}
	}

	## Unused methods


	/**
	 * Unused Mthod.
	 *
	 * @param App_Config   $config       The App_Config instance.
	 * @param Hook_Loader  $loader       The Hook_Loader instance.
	 * @param DI_Container $di_container The DI_Container instance.
	 *
	 * @return void
	 */
	public function post_register( App_Config $config, Hook_Loader $loader, DI_Container $di_container ): void {} // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceBeforeLastUsed

	/**
	 * Unused method.
	 *
	 * @return string|null
	 */
	public function get_middleware(): ?string {
		return null;
	}
}
