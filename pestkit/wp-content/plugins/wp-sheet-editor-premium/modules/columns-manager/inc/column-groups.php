<?php defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'WPSE_Column_Groups' ) ) {

	class WPSE_Column_Groups {



		private static $instance = false;
		private static $cached_groups_option = null;
		private static $cached_active_group = null;
		private static $cached_allowed_groups = array();
		private static $cached_group_usage_allowed = array();
		private static $cached_group_columns = array();
		public $groups_key       = 'vgse_column_groups';

		private function __construct() {}

		function init() {

			if ( ! apply_filters( 'vg_sheet_editor/column_groups_feature_allowed', true ) ) {
				return;
			}
			// Column groups
			if ( VGSE()->helpers->user_can_manage_options() ) {
				add_action( 'vg_sheet_editor/columns_visibility/after_fields', array( $this, 'render_save_columns_view_option' ) );
				add_action( 'vg_sheet_editor/columns_visibility/after_options_saved', array( $this, 'save_column_group' ), 11, 2 );
			} else {
				add_action( 'vg_sheet_editor/editor/before_init', array( $this, 'register_toolbar_items' ) );
			}

			$this->maybe_switch_to_group();
			add_action( 'vg_sheet_editor/editor/before_init', array( $this, 'register_groups_toolbar_items' ) );
			add_action( 'wp_ajax_vgse_delete_saved_columns_manager', array( $this, 'delete_saved_group' ) );
			add_filter( 'vg_sheet_editor/columns_visibility/options', array( $this, 'set_current_group_columns' ), 10, 2 );

			add_action( 'show_user_profile', array( $this, 'render_user_profile_fields' ) );
			add_action( 'edit_user_profile', array( $this, 'render_user_profile_fields' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_user_profile_fields' ) );
			add_action( 'personal_options_update', array( $this, 'save_user_profile_fields' ) );
			add_action( 'vg_sheet_editor/columns_visibility/after_saving_newly_detected_columns', array( $this, 'save_newly_detected_columns' ) );
		}

		public function save_newly_detected_columns( $new_columns ) {
			$option = $this->get_groups_option( array() );
			if ( empty( $option ) || empty( $option['groups'] ) ) {
				return;
			}

			foreach ( $new_columns as $post_type => $columns ) {
				if ( ! isset( $option['groups'][ $post_type ] ) ) {
					continue;
				}

				if ( ! isset( $option['sheetColumns'][ $post_type ] ) ) {
					$option['sheetColumns'][ $post_type ] = array();
				}
				$option['sheetColumns'][ $post_type ] = array_merge( $option['sheetColumns'][ $post_type ], $columns );

				foreach ( $option['groups'][ $post_type ] as $group_key => $group_data ) {
					$option['groups'][ $post_type ][ $group_key ]['enabled'] = array_values( array_unique( array_merge( $option['groups'][ $post_type ][ $group_key ]['enabled'], array_keys( $columns ) ) ) );
				}
			}

			$this->update_groups_option( $option );
		}

		/**
		 * Register toolbar item to edit columns visibility live on the spreadsheet
		 */
		function register_toolbar_items( $editor ) {
			$post_types = $editor->args['enabled_post_types'];
			foreach ( $post_types as $post_type ) {
				$allowed_groups = $this->get_allowed_groups_for_post_type( $post_type );
				if ( count( $allowed_groups ) < 2 ) {
					continue;
				}
				$editor->args['toolbars']->register_item(
					'columns_manager',
					array(
						'type'              => 'button',
						'content'           => esc_html__( 'Spreadsheet views', 'vg_sheet_editor' ),
						'url'               => 'javascript:void(0)',
						'toolbar_key'       => 'secondary',
						'allow_in_frontend' => false,
					),
					$post_type
				);
			}
		}

		function save_user_profile_fields( $user_id ) {
			if ( empty( VGSE()->options['enable_spreadsheet_views_restrictions'] ) ) {
				return;
			}
			if ( ! WP_Sheet_Editor_Helpers::current_user_can( 'edit_user', $user_id ) || ! VGSE()->helpers->user_can_manage_options() || ! isset( $_REQUEST['wpse_allowed_column_groups'] ) ) {
				return false;
			}

			$data = VGSE()->helpers->safe_text_only( $_REQUEST['wpse_allowed_column_groups'] );
			update_user_meta( $user_id, 'wpse_allowed_column_groups', $data );
		}

		function render_user_profile_fields( $user ) {
			if ( empty( VGSE()->options['enable_spreadsheet_views_restrictions'] ) ) {
				return;
			}
			if ( ! VGSE()->helpers->user_can_manage_options() ) {
				return;
			}
			$sheets             = VGSE()->helpers->get_prepared_post_types();
			$all_allowed_groups = get_user_meta( $user->ID, 'wpse_allowed_column_groups', true );
			if ( empty( $all_allowed_groups ) ) {
				$all_allowed_groups = array();
			}
			?>
			<h3><?php esc_html_e( 'WP Sheet Editor', 'vg_sheet_editor' ); ?></h3>
			<p><?php esc_html_e( 'Note. This is not a security feature, the user can edit all the fields in the normal editor based on the role. This option is for convenience, so they only see the columns they need when they open the spreadsheet editor. If you leave these fields empty they can switch between all the existing views', 'vg_sheet_editor' ); ?></p>

			<table class="form-table">
				<?php
				foreach ( $sheets as $sheet ) {
					$key  = $sheet['key'];
					$name = $sheet['label'];

					if ( ! empty( $sheet['is_disabled'] ) ) {
						continue;
					}

					if ( ! isset( $all_allowed_groups[ $key ] ) ) {
						$all_allowed_groups[ $key ] = '';
					}
					?>
					<tr>
						<th><label for="wpse-allowed-column-groups<?php echo esc_attr( $key ); ?>">
																			<?php
																				/* translators: %s: Post type name */
																				printf( esc_html__( '%s: Allowed spreadsheet views', 'vg_sheet_editor' ), esc_html( $name ) );
																			?>
						</label></th>
						<td>
							<input type="text" name="wpse_allowed_column_groups[<?php echo esc_attr( $key ); ?>]" id="wpse-allowed-column-groups<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $all_allowed_groups[ $key ] ); ?>" class="regular-text" /><br />
							<span class="description"><?php esc_html_e( 'Please enter the name of the spreadsheet views separated by commas.', 'vg_sheet_editor' ); ?></span>
						</td>
					</tr>
				<?php } ?>
			</table>
			<?php
		}

		function get_allowed_groups_for_post_type( $post_type ) {
			if ( isset( self::$cached_allowed_groups[ $post_type ] ) ) {
				return self::$cached_allowed_groups[ $post_type ];
			}

			if ( empty( VGSE()->options['enable_spreadsheet_views_restrictions'] ) ) {
				$existing_groups = $this->get_groups_option();
				$allowed_groups  = ( isset( $existing_groups['groups'] ) && isset( $existing_groups['groups'][ $post_type ] ) ) ? array_keys( $existing_groups['groups'][ $post_type ] ) : array();
			} else {
				$all_allowed_groups = get_user_meta( get_current_user_id(), 'wpse_allowed_column_groups', true );
				$allowed_groups     = isset( $all_allowed_groups[ $post_type ] ) ? array_filter( array_map( 'sanitize_title', array_map( 'trim', explode( ',', $all_allowed_groups[ $post_type ] ) ) ) ) : array();
			}

			// FIX: Force all array values to strings to prevent strict comparison failures on numeric keys.
			$result = array_map( 'strval', $allowed_groups );
			self::$cached_allowed_groups[ $post_type ] = $result;
			return $result;
		}

		function is_group_usage_allowed( $group_key, $post_type ) {
			// FIX: Cast group_key to string to ensure strict comparisons work when
			// the key originates from a database array with auto-casted integer keys.
			$group_key = strval( $group_key );

			if ( empty( $post_type ) || $group_key === '' ) {
				return false;
			}

			if ( isset( self::$cached_group_usage_allowed[ $post_type ][ $group_key ] ) ) {
				return self::$cached_group_usage_allowed[ $post_type ][ $group_key ];
			}

			$allowed_groups  = $this->get_allowed_groups_for_post_type( $post_type );
			$existing_groups = $this->get_groups_option();

			$out = true;

			// Disallow if group doesn't exist
			if ( empty( $existing_groups ) || empty( $existing_groups['groups'] ) || empty( $existing_groups['groups'][ $post_type ] ) || empty( $existing_groups['groups'][ $post_type ][ $group_key ] ) ) {
				$out = false;
			}

			// Disallow if the user is restricted to specific groups and the group is not whitelisted for this user
			if ( ! empty( $allowed_groups ) && ! in_array( $group_key, $allowed_groups, true ) ) {
				$out = false;
			}

			self::$cached_group_usage_allowed[ $post_type ][ $group_key ] = $out;
			return $out;
		}

		function get_active_group() {
			if ( self::$cached_active_group !== null ) {
				return self::$cached_active_group;
			}

			if ( ! VGSE()->helpers->is_editor_page() && ! wp_doing_ajax() ) {
				self::$cached_active_group = false;
				return false;
			}

			$post_type        = VGSE()->helpers->get_provider_from_query_string();
			$last_groups_used = get_user_meta( get_current_user_id(), 'wpse_last_column_group', true );

			if ( empty( $post_type ) ) {
				self::$cached_active_group = false;
				return false;
			}

			$existing_groups = $this->get_groups_option();
			if ( empty( $existing_groups ) || empty( $existing_groups['groups'] ) ) {
				self::$cached_active_group = false;
				return false;
			}

			$last_group     = isset( $last_groups_used[ $post_type ] ) ? $last_groups_used[ $post_type ] : null;
			$allowed_groups = $this->get_allowed_groups_for_post_type( $post_type );

			$current_group = false;
			if ( $last_group && ( in_array( $last_group, $allowed_groups, true ) || empty( $allowed_groups ) ) ) {
				$current_group = $last_group;
			} elseif ( ! empty( $allowed_groups ) ) {
				$current_group = current( $allowed_groups );
			}

			self::$cached_active_group = $current_group;
			return $current_group;
		}

		function set_current_group_columns( $visibility_options, $post_type ) {
			if ( ! apply_filters( 'vg_sheet_editor/columns_groups_enabled', true, $post_type ) ) {
				return $visibility_options;
			}
			$group_key = $this->get_active_group();
			if ( ! $post_type ) {
				$post_type = VGSE()->helpers->get_provider_from_query_string();
			}
			if ( ! $this->is_group_usage_allowed( $group_key, $post_type ) ) {
				return $visibility_options;
			}

			if ( isset( self::$cached_group_columns[ $post_type ][ $group_key ] ) ) {
				$visibility_options[ $post_type ] = self::$cached_group_columns[ $post_type ][ $group_key ];
				return $visibility_options;
			}

			if ( empty( $visibility_options ) ) {
				$visibility_options = array();
			}
			if ( empty( $visibility_options[ $post_type ] ) ) {
				$visibility_options[ $post_type ] = array();
			}

			$option      = $this->get_groups_option();
			$saved_group = isset( $option['groups'][ $post_type ][ $group_key ] ) ? $option['groups'][ $post_type ][ $group_key ] : null;
			if ( ! $saved_group ) {
				return $visibility_options;
			}

			$sheet_columns = isset( $option['sheetColumns'][ $post_type ] ) ? $option['sheetColumns'][ $post_type ] : array();

			// Reconstruct group_columns in the old format (associative arrays columnKey => label)
			$group_columns = array(
				'enabled'  => array(),
				'disabled' => array(),
				'name'     => isset( $saved_group['name'] ) ? $saved_group['name'] : '',
			);

			$saved_enabled  = isset( $saved_group['enabled'] ) && is_array( $saved_group['enabled'] ) ? $saved_group['enabled'] : array();
			$saved_disabled = isset( $saved_group['disabled'] ) && is_array( $saved_group['disabled'] ) ? $saved_group['disabled'] : array();

			foreach ( $saved_enabled as $col_key ) {
				$group_columns['enabled'][ $col_key ] = isset( $sheet_columns[ $col_key ] ) ? $sheet_columns[ $col_key ] : $col_key;
			}
			foreach ( $saved_disabled as $col_key ) {
				$group_columns['disabled'][ $col_key ] = isset( $sheet_columns[ $col_key ] ) ? $sheet_columns[ $col_key ] : $col_key;
			}

			// Original logic: ensure any columns not explicitly in enabled or disabled groups are added to disabled
			$all_columns = $sheet_columns;
			if ( ! empty( $visibility_options[ $post_type ] ) ) {
				foreach ( array( 'enabled', 'disabled' ) as $status ) {
					if ( ! empty( $visibility_options[ $post_type ][ $status ] ) && is_array( $visibility_options[ $post_type ][ $status ] ) ) {
						foreach ( $visibility_options[ $post_type ][ $status ] as $col_key => $col_title ) {
							if ( ! isset( $all_columns[ $col_key ] ) ) {
								$all_columns[ $col_key ] = $col_title;
							}
						}
					}
				}
			}

			foreach ( $all_columns as $column_key => $column_title ) {
				if ( ! isset( $group_columns['enabled'][ $column_key ] ) && ! isset( $group_columns['disabled'][ $column_key ] ) ) {
					$group_columns['disabled'][ $column_key ] = $column_title;
				}
			}

			self::$cached_group_columns[ $post_type ][ $group_key ] = $group_columns;
			$visibility_options[ $post_type ] = $group_columns;
			return $visibility_options;
		}

		function save_last_columns_group( $group_key, $post_type, $user_id = null ) {
			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}
			$last_groups_used = get_user_meta( $user_id, 'wpse_last_column_group', true );

			if ( empty( $last_groups_used ) ) {
				$last_groups_used = array();
			}
			if ( empty( $last_groups_used[ $post_type ] ) ) {
				$last_groups_used[ $post_type ] = array();
			}

			$last_groups_used[ $post_type ] = $group_key;
			update_user_meta( $user_id, 'wpse_last_column_group', $last_groups_used );

			self::$cached_active_group = $group_key;
		}

		function maybe_switch_to_group() {
			if ( empty( $_GET['wpse_cmg'] ) ) {
				return;
			}

			$post_type = VGSE()->helpers->get_provider_from_query_string();
			$group_key = sanitize_title( $_GET['wpse_cmg'] );

			if ( $this->is_group_usage_allowed( $group_key, $post_type ) ) {
				$this->save_last_columns_group( $group_key, $post_type );
			}

			$url = esc_url( remove_query_arg( 'wpse_cmg' ) );
			wp_redirect( $url );
			exit();
		}

		function register_groups_toolbar_items( $editor ) {
			$option = $this->get_groups_option();
			if ( empty( $option ) || empty( $option['groups'] ) ) {
				return;
			}

			$post_types       = $editor->args['enabled_post_types'];
			$active_group_key = strval( $this->get_active_group() );
			foreach ( $post_types as $post_type ) {
				if ( ! isset( $option['groups'][ $post_type ] ) ) {
					continue;
				}

				ksort( $option['groups'][ $post_type ] );
				foreach ( $option['groups'][ $post_type ] as $group_key => $group ) {
					$group_key = strval( $group_key );
					if ( ! $this->is_group_usage_allowed( $group_key, $post_type ) ) {
						continue;
					}

					$delete_button_attribute = ( $active_group_key && $group_key === $active_group_key ) ? 'data-active-item data-saved-item' : 'data-saved-item';
					$extra_attributes        = 'data-saved-type="columns_manager" ' . $delete_button_attribute . ' data-item-name="' . esc_attr( $group_key ) . '" ';
					$css_class               = '';
					if ( $active_group_key && $group_key === $active_group_key ) {
						$extra_attributes .= ' data-reload="1" ';
						$css_class         = 'wpse-active-group';
					}
					$editor->args['toolbars']->register_item(
						'cm_g_' . $group_key,
						array(
							'type'                  => 'button',
							'toolbar_key'           => 'secondary',
							'allow_in_frontend'     => false,
							'parent'                => 'columns_manager',
							'content'               => $group['name'],
							'url'                   => esc_url( add_query_arg( 'wpse_cmg', $group_key ) ),
							'extra_html_attributes' => $extra_attributes,
							'css_class'             => $css_class,
						),
						$post_type
					);
				}
			}
		}

		function delete_saved_group() {
			if ( empty( $_REQUEST['post_type'] ) || ! VGSE()->helpers->verify_nonce_from_request() || ! VGSE()->helpers->user_can_manage_options() ) {
				wp_send_json_error( array( 'message' => esc_html__( 'You dont have enough permissions to view this page.', 'vg_sheet_editor' ) ) );
			}

			$post_type = VGSE()->helpers->sanitize_table_key( wp_unslash( $_REQUEST['post_type'] ) );
			$group_key = sanitize_title( urldecode( $_REQUEST['search_name'] ) );

			$option = $this->get_groups_option();
			if ( empty( $option ) || empty( $option['groups'] ) ) {
				wp_send_json_success();
			}

			if ( ! isset( $option['groups'][ $post_type ] ) || ! isset( $option['groups'][ $post_type ][ $group_key ] ) ) {
				wp_send_json_success();
			}
			unset( $option['groups'][ $post_type ][ $group_key ] );

			if ( empty( $option['groups'][ $post_type ] ) ) {
				unset( $option['groups'][ $post_type ] );
				if ( isset( $option['sheetColumns'][ $post_type ] ) ) {
					unset( $option['sheetColumns'][ $post_type ] );
				}
			}

			$this->update_groups_option( $option );
			wp_send_json_success();
		}

		function save_column_group( $post_type, $options ) {
			if ( ! isset( $_REQUEST['wpse_group_name'] ) ) {
				return;
			}
			$name   = sanitize_text_field( wp_unslash( $_REQUEST['wpse_group_name'] ) );
			$option = $this->get_groups_option();
			if ( empty( $option ) ) {
				$option = array(
					'sheetColumns' => array(),
					'groups'       => array(),
				);
			}

			if ( ! isset( $option['sheetColumns'] ) ) {
				$option['sheetColumns'] = array();
			}
			if ( ! isset( $option['groups'] ) ) {
				$option['groups'] = array();
			}

			if ( empty( $option['sheetColumns'][ $post_type ] ) ) {
				$option['sheetColumns'][ $post_type ] = array();
			}
			if ( empty( $option['groups'][ $post_type ] ) ) {
				$option['groups'][ $post_type ] = array();
			}

			$group = $options[ $post_type ];

			$enabled_labels  = isset( $group['enabled'] ) && is_array( $group['enabled'] ) ? $group['enabled'] : array();
			$disabled_labels = isset( $group['disabled'] ) && is_array( $group['disabled'] ) ? $group['disabled'] : array();

			$option['sheetColumns'][ $post_type ] = array_merge(
				$option['sheetColumns'][ $post_type ],
				$enabled_labels,
				$disabled_labels
			);

			$new_group = array(
				'enabled'  => array_keys( $enabled_labels ),
				'disabled' => array_keys( $disabled_labels ),
				'name'     => $name,
			);

			$key                                    = sanitize_title( $name );
			$option['groups'][ $post_type ][ $key ] = $new_group;

			$this->update_groups_option( $option );

			// Activate the group that we just created, otherwise they can enable columns
			// and those columns wont appear enabled until they manually switch to the new columns group
			$this->save_last_columns_group( $key, $post_type );
		}

		function render_save_columns_view_option( $post_type ) {
			if ( ! VGSE()->helpers->is_editor_page() ) {
				return;
			}
			$option           = $this->get_groups_option();
			$active_group_key = $this->get_active_group();
			$name             = ( $active_group_key && isset( $option['groups'][ $post_type ][ $active_group_key ] ) ) ? $option['groups'][ $post_type ][ $active_group_key ]['name'] : '';
			?>
			<li class="vgse-save-preset">
				<label><?php esc_html_e( 'Add a name for this group of columns', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php esc_attr_e( 'We will save this group of columns as a spreadsheet view and you can switch between spreadsheets in the toolbar', 'vg_sheet_editor' ); ?>">( ? )</a></label>
				<input required name="wpse_group_name" type="text" value="<?php echo esc_attr( $name ); ?>" />
			</li>
			<?php
		}

		public function get_groups_option( $default = array() ) {
			if ( self::$cached_groups_option !== null ) {
				return self::$cached_groups_option;
			}

			$raw_value = get_option( $this->groups_key, $default );

			if ( empty( $raw_value ) ) {
				self::$cached_groups_option = $default;
				return $default;
			}

			// If it is already a serialized/uncompressed array (not migrated yet), return it.
			if ( is_array( $raw_value ) ) {
				if ( ! isset( $raw_value['groups'] ) && ! isset( $raw_value['sheetColumns'] ) ) {
					$migrated = $this->migrate_to_new_format( $raw_value );
					$this->update_groups_option( $migrated );
					self::$cached_groups_option = $migrated;
					return $migrated;
				}
				self::$cached_groups_option = $raw_value;
				return $raw_value;
			}

			// If it's a string, it must be JSON (potentially compressed & base64 encoded)
			if ( is_string( $raw_value ) ) {
				$decoded = null;
				if ( function_exists( 'gzuncompress' ) ) {
					$base64_decoded = base64_decode( $raw_value, true );
					if ( $base64_decoded !== false ) {
						$decompressed = @gzuncompress( $base64_decoded );
						if ( $decompressed !== false ) {
							$decoded = json_decode( $decompressed, true );
						}
					}
				}

				if ( $decoded === null ) {
					$decoded = json_decode( $raw_value, true );
				}

				if ( is_array( $decoded ) ) {
					if ( ! isset( $decoded['groups'] ) && ! isset( $decoded['sheetColumns'] ) ) {
						$migrated = $this->migrate_to_new_format( $decoded );
						$this->update_groups_option( $migrated );
						self::$cached_groups_option = $migrated;
						return $migrated;
					}
					self::$cached_groups_option = $decoded;
					return $decoded;
				}
			}

			self::$cached_groups_option = $default;
			return $default;
		}

		public function update_groups_option( $value ) {
			if ( ! is_array( $value ) ) {
				return false;
			}

			self::$cached_groups_option = $value;
			self::$cached_active_group = null;
			self::$cached_allowed_groups = array();
			self::$cached_group_usage_allowed = array();
			self::$cached_group_columns = array();

			$json_string = json_encode( $value );
			if ( $json_string === false ) {
				return false;
			}

			if ( function_exists( 'gzcompress' ) ) {
				$compressed = gzcompress( $json_string, 9 );
				if ( $compressed !== false ) {
					$option_value = base64_encode( $compressed );
				} else {
					$option_value = $json_string;
				}
			} else {
				$option_value = $json_string;
			}

			return update_option( $this->groups_key, $option_value, false );
		}

		public function migrate_to_new_format( $old_value ) {
			$new_value = array(
				'sheetColumns' => array(),
				'groups'       => array(),
			);

			if ( ! is_array( $old_value ) ) {
				return $new_value;
			}

			foreach ( $old_value as $post_type => $groups ) {
				if ( ! is_array( $groups ) ) {
					continue;
				}

				if ( ! isset( $new_value['sheetColumns'][ $post_type ] ) ) {
					$new_value['sheetColumns'][ $post_type ] = array();
				}
				if ( ! isset( $new_value['groups'][ $post_type ] ) ) {
					$new_value['groups'][ $post_type ] = array();
				}

				foreach ( $groups as $group_key => $group_data ) {
					if ( ! is_array( $group_data ) ) {
						continue;
					}

					$name = isset( $group_data['name'] ) ? $group_data['name'] : '';

					$enabled_keys   = array();
					$enabled_labels = isset( $group_data['enabled'] ) && is_array( $group_data['enabled'] ) ? $group_data['enabled'] : array();
					foreach ( $enabled_labels as $col_key => $col_label ) {
						$new_value['sheetColumns'][ $post_type ][ $col_key ] = $col_label;
						$enabled_keys[]                                      = $col_key;
					}

					$disabled_keys   = array();
					$disabled_labels = isset( $group_data['disabled'] ) && is_array( $group_data['disabled'] ) ? $group_data['disabled'] : array();
					foreach ( $disabled_labels as $col_key => $col_label ) {
						$new_value['sheetColumns'][ $post_type ][ $col_key ] = $col_label;
						$disabled_keys[]                                     = $col_key;
					}

					$new_value['groups'][ $post_type ][ $group_key ] = array(
						'enabled'  => $enabled_keys,
						'disabled' => $disabled_keys,
						'name'     => $name,
					);
				}
			}

			return $new_value;
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( null == WPSE_Column_Groups::$instance ) {
				WPSE_Column_Groups::$instance = new WPSE_Column_Groups();
				WPSE_Column_Groups::$instance->init();
			}
			return WPSE_Column_Groups::$instance;
		}

		function __set( $name, $value ) {
			$this->$name = $value;
		}

		function __get( $name ) {
			return $this->$name;
		}
	}
}

if ( ! function_exists( 'WPSE_Column_Groups_Obj' ) ) {

	function WPSE_Column_Groups_Obj() {
		return WPSE_Column_Groups::get_instance();
	}
}
WPSE_Column_Groups_Obj();
