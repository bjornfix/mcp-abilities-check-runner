<?php
/**
 * Plugin Name: MCP Abilities - Plugin Check
 * Plugin URI: https://devenia.com
 * Description: MCP bridge for the official WordPress.org Plugin Check plugin.
 * Version: 0.1.0
 * Author: Devenia
 * Author URI: https://devenia.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 6.9
 * Requires PHP: 8.0
 *
 * @package MCP_Abilities_Plugin_Check
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if Abilities API is available.
 */
function mcp_plugin_check_dependencies_ok(): bool {
	if ( ! function_exists( 'wp_register_ability' ) ) {
		add_action(
			'admin_notices',
			static function (): void {
				echo '<div class="notice notice-error"><p><strong>MCP Abilities - Plugin Check</strong> requires the Abilities API plugin to be installed and activated.</p></div>';
			}
		);
		return false;
	}

	return true;
}

/**
 * Flatten Plugin Check nested result arrays.
 *
 * @param array<string, mixed> $grouped Grouped errors or warnings.
 * @param string              $type Result type.
 * @return array<int, array<string, mixed>>
 */
function mcp_plugin_check_flatten_results( array $grouped, string $type ): array {
	$flat = array();

	foreach ( $grouped as $file => $lines ) {
		if ( ! is_array( $lines ) ) {
			continue;
		}
		foreach ( $lines as $line => $columns ) {
			if ( ! is_array( $columns ) ) {
				continue;
			}
			foreach ( $columns as $column => $messages ) {
				if ( ! is_array( $messages ) ) {
					continue;
				}
				foreach ( $messages as $message ) {
					if ( ! is_array( $message ) ) {
						continue;
					}
					$flat[] = array(
						'type'     => $type,
						'file'     => (string) $file,
						'line'     => (int) $line,
						'column'   => (int) $column,
						'code'     => isset( $message['code'] ) ? (string) $message['code'] : '',
						'message'  => isset( $message['message'] ) ? wp_strip_all_tags( (string) $message['message'] ) : '',
						'docs'     => isset( $message['docs'] ) ? (string) $message['docs'] : '',
						'severity' => isset( $message['severity'] ) ? (int) $message['severity'] : 0,
					);
				}
			}
		}
	}

	return $flat;
}

/**
 * Run the official Plugin Check runner.
 *
 * @param array<string, mixed> $input Ability input.
 * @return array<string, mixed>
 */
function mcp_plugin_check_run( array $input ): array {
	if ( ! class_exists( 'WordPress\\Plugin_Check\\Checker\\AJAX_Runner' ) ) {
		return array(
			'success' => false,
			'message' => 'The official WordPress.org Plugin Check plugin is not active or its runner class is unavailable.',
		);
	}

	$plugin = isset( $input['plugin'] ) ? sanitize_text_field( (string) $input['plugin'] ) : '';
	if ( '' === $plugin ) {
		return array(
			'success' => false,
			'message' => 'Plugin slug or basename is required.',
		);
	}

	$checks = array();
	if ( isset( $input['checks'] ) && is_array( $input['checks'] ) ) {
		foreach ( $input['checks'] as $check ) {
			$check = sanitize_key( (string) $check );
			if ( '' !== $check ) {
				$checks[] = $check;
			}
		}
	}

	$categories = array();
	if ( isset( $input['categories'] ) && is_array( $input['categories'] ) ) {
		foreach ( $input['categories'] as $category ) {
			$category = sanitize_key( (string) $category );
			if ( '' !== $category ) {
				$categories[] = $category;
			}
		}
	}

	$include_experimental = (bool) ( $input['include_experimental'] ?? false );
	$mode                 = isset( $input['mode'] ) && 'update' === $input['mode'] ? 'update' : 'new';
	$max_results          = isset( $input['max_results'] ) ? max( 1, min( 500, (int) $input['max_results'] ) ) : 100;

	try {
		$runner = new WordPress\Plugin_Check\Checker\AJAX_Runner();
		$runner->set_experimental_flag( $include_experimental );
		$runner->set_check_slugs( array_values( array_unique( $checks ) ) );
		$runner->set_categories( array_values( array_unique( $categories ) ) );
		$runner->set_plugin( $plugin );
		$runner->set_slug( '' );
		$runner->set_mode( $mode );
		$runner->set_use_ai( false );

		$results = $runner->run();
	} catch ( Throwable $error ) {
		return array(
			'success' => false,
			'message' => $error->getMessage(),
		);
	}

	$errors   = $results->get_errors();
	$warnings = $results->get_warnings();
	$flat     = array_merge(
		mcp_plugin_check_flatten_results( $errors, 'error' ),
		mcp_plugin_check_flatten_results( $warnings, 'warning' )
	);

	return array(
		'success'              => true,
		'plugin'               => $plugin,
		'mode'                 => $mode,
		'include_experimental' => $include_experimental,
		'error_count'          => method_exists( $results, 'get_error_count' ) ? (int) $results->get_error_count() : count( mcp_plugin_check_flatten_results( $errors, 'error' ) ),
		'warning_count'        => method_exists( $results, 'get_warning_count' ) ? (int) $results->get_warning_count() : count( mcp_plugin_check_flatten_results( $warnings, 'warning' ) ),
		'results'              => array_slice( $flat, 0, $max_results ),
		'truncated'            => count( $flat ) > $max_results,
		'message'              => sprintf(
			'Plugin Check completed with %d errors and %d warnings.',
			method_exists( $results, 'get_error_count' ) ? (int) $results->get_error_count() : 0,
			method_exists( $results, 'get_warning_count' ) ? (int) $results->get_warning_count() : 0
		),
	);
}

/**
 * Register Plugin Check abilities.
 */
function mcp_register_plugin_check_abilities(): void {
	if ( ! mcp_plugin_check_dependencies_ok() ) {
		return;
	}

	wp_register_ability(
		'plugin-check/run',
		array(
			'label'               => 'Run WordPress.org Plugin Check',
			'description'         => 'Runs the official WordPress.org Plugin Check plugin against an installed plugin.',
			'category'            => 'site',
			'input_schema'        => array(
				'type'                 => 'object',
				'required'             => array( 'plugin' ),
				'properties'           => array(
					'plugin'               => array( 'type' => 'string' ),
					'checks'               => array(
						'type'    => 'array',
						'items'   => array( 'type' => 'string' ),
						'default' => array(),
					),
					'categories'           => array(
						'type'    => 'array',
						'items'   => array( 'type' => 'string' ),
						'default' => array(),
					),
					'include_experimental' => array( 'type' => 'boolean', 'default' => false ),
					'mode'                 => array(
						'type'    => 'string',
						'enum'    => array( 'new', 'update' ),
						'default' => 'new',
					),
					'max_results'          => array( 'type' => 'integer', 'default' => 100 ),
				),
				'additionalProperties' => false,
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'success'       => array( 'type' => 'boolean' ),
					'plugin'        => array( 'type' => 'string' ),
					'error_count'   => array( 'type' => 'integer' ),
					'warning_count' => array( 'type' => 'integer' ),
					'results'       => array( 'type' => 'array' ),
					'truncated'     => array( 'type' => 'boolean' ),
					'message'       => array( 'type' => 'string' ),
				),
			),
			'execute_callback'    => static function ( array $input = array() ): array {
				return mcp_plugin_check_run( $input );
			},
			'permission_callback' => static function (): bool {
				return current_user_can( 'activate_plugins' );
			},
			'meta'                => array(
				'annotations' => array(
					'readonly'    => true,
					'destructive' => false,
					'idempotent'  => true,
				),
			),
		)
	);
}
add_action( 'wp_abilities_api_init', 'mcp_register_plugin_check_abilities' );
