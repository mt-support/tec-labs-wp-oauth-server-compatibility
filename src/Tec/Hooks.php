<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * ```php
 *  remove_filter( 'some_filter', [ tribe( Tribe\Extensions\WPOAuthServer\Hooks::class ), 'some_filtering_method' ] );
 *  remove_filter( 'some_filter', [ tribe( 'extension.wp_oauth_server_compatibility.hooks' ), 'some_filtering_method' ] );
 * ```
 *
 * To remove an action:
 * ```php
 *  remove_action( 'some_action', [ tribe( Tribe\Extensions\WPOAuthServer\Hooks::class ), 'some_method' ] );
 *  remove_action( 'some_action', [ tribe( 'extension.wp_oauth_server_compatibility.hooks' ), 'some_method' ] );
 * ```
 *
 * @since 1.0.0
 *
 * @package Tribe\Extensions\WPOAuthServer;
 */

namespace Tribe\Extensions\WPOAuthServer;

use Tribe__Main as Common;

/**
 * Class Hooks.
 *
 * @since 1.0.0
 *
 * @package Tribe\Extensions\WPOAuthServer;
 */
class Hooks extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'extension.wp_oauth_server_compatibility.hooks', $this );

		$this->add_filters();
	}

	/**
	 * Adds the filters required by the plugin.
	 *
	 * @since 1.0.0
	 */
	protected function add_filters() {
		add_action( 'rest_authentication_errors', function( $result ) {
			global $current_user;

			if ( ! empty( $current_user->ID ) ) {
				return $result;
			}

			$context = tribe_context();

			if ( ! $context->doing_rest() ) {
				return $result;
			}

			if ( ! $view = $context->get( 'view' ) ) {
				return $result;
			}

			add_filter( 'option_wo_options', function( $options ) {
				$options['block_all_unauthenticated_rest_request'] = false;
				return $options;
			} );

			return $result;
		}, 1 );
	}
}
