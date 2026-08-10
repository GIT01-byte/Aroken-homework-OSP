<?php defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPSE_WPML_Posts' ) ) {

	class WPSE_WPML_Posts {

		private static $instance = null;

		private function __construct() {
		}

		public function init() {
			add_action( 'vg_sheet_editor/editor/register_columns', array( $this, 'register_columns' ) );
			add_action( 'vg_sheet_editor/editor/register_columns', array( $this, 'register_wc_columns_for_wpml_sync' ), 99 );
			add_action( 'woocommerce_rest_insert_product_variation_object', array( $this, 'sync_translation_fields_after_wc_rest_variation_inserted' ), 90, 2 );
			add_action( 'product_variation_linked', array( $this, 'sync_translation_fields' ), 90, 1 );
			add_action( 'vg_sheet_editor/save_rows/before_saving_rows', array( $this, 'stop_automatic_wpml_syncing' ), 10, 2 );
			add_filter( 'vg_sheet_editor/formulas/sql_execution/can_execute', array( $this, 'disable_sql_formulas_to_allow_translation_syncing' ), 9999 );
			add_action( 'vg_sheet_editor/woocommerce/after_variations_created', array( $this, 'add_lang_to_new_variations' ) );

			add_action( 'vg_sheet_editor/save_rows/after_saving_post', array( $this, 'sync_translation_fields_after_saving_post' ), 50, 2 );
			add_filter( 'vg_sheet_editor/save_rows/row_data_before_save', array( $this, 'sync_fields_if_new_post' ), 10, 3 );
			add_action( 'vg_sheet_editor/provider/post/post_converted_to_product', array( $this, 'post_converted_to_product' ) );
			add_action( 'vg_sheet_editor/formulas/execute_formula/after_execution_on_field', array( $this, 'sync_translation_fields_after_formula' ), 10, 4 );

			add_action( 'vg_sheet_editor/woocommerce/variable_product_updated', array( $this, 'after_wc_variations_updated' ), 10, 4 );
			add_action( 'vg_sheet_editor/filters/after_advanced_fields_section', array( $this, 'add_wpml_language_search' ) );
			add_filter( 'posts_clauses', array( $this, 'search_by_wpml_translation' ), 10, 2 );
			add_filter( 'vg_sheet_editor/filters/sanitize_request_filters', array( $this, 'register_custom_filters' ), 10, 2 );
			add_filter( 'vg_sheet_editor/woocommerce/wc_rest_api_product_args', array( $this, 'add_current_language_to_wc_rest_api_requests' ) );
			add_action( 'vg_sheet_editor/add_new_posts/after_all_posts_created', array( $this, 'set_current_language_to_new_rows' ), 10, 2 );
			add_filter( 'vg_sheet_editor/import/save_rows_args', array( $this, 'remove_sku_from_wc_product_translations_import' ) );

			add_action( 'vg_sheet_editor/save_rows/after_saving_post', array( $this, 'product_updated_on_spreadsheet' ), 10, 4 );
			add_action( 'vg_sheet_editor/formulas/execute_formula/after_execution_on_field', array( $this, 'product_updated_with_formula' ), 10, 8 );
			add_action( 'vg_sheet_editor/formulas/execute_formula/after_sql_execution', array( $this, 'product_updated_with_sql_formula' ), 10, 5 );
			add_filter( 'vg_sheet_editor/options_page/options', array( $this, 'add_settings_page_options' ) );
		}

		/**
		 * Add save_value_callbacks to WooCommerce columns requiring synchronization
		 */
		public function register_wc_columns_for_wpml_sync( $editor ) {
			if ( ! class_exists( 'WooCommerce' ) || $editor->provider->key === 'user' || ! $editor->provider->is_post_type ) {
				return;
			}

			$post_type = 'product';
			if ( ! in_array( $post_type, $editor->args['enabled_post_types'], true ) ) {
				return;
			}

			// If this is the default lang, exit
			if ( WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) {
				return;
			}

			// Fields that should always be saved to the original language product
			$fields_to_sync = array(
				'_sku',
				'_stock',
				'_stock_status',
				'_manage_stock',
				'_backorders',
				'_sold_individually',
				'_regular_price',
				'_sale_price',
				// Can't use save_value_callback for sale price dates because they already have a callback
				// '_sale_price_dates_from',
				// '_sale_price_dates_to',
				'_upsell_ids',
				'_crosssell_ids',
				'_thumbnail_id',
				'_product_image_gallery',
			);

			foreach ( $fields_to_sync as $field_key ) {
				$column = $editor->args['columns']->get_item( $field_key, $post_type );

				if ( $column ) {
					$editor->args['columns']->register_item(
						$field_key,
						$post_type,
						array(
							'save_value_callback' => array( $this, 'save_wc_synced_field_to_original' ),
						),
						true
					);
				}
			}
			$editor->args['columns']->clear_cache( $post_type );
		}

		/**
		 * Callback to save specific fields from translation rows to the original product.
		 * Because WPML won't sync non-translated fields back to the main product when you edit them in a translation product.
		 */
		public function save_wc_synced_field_to_original( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			// 1. Get the original product ID
			$original_product_id = apply_filters( 'wpml_original_element_id', null, $post_id, 'post_' . $post_type );

			// Fallback in case the filter fails (e.g., WPML is inactive)
			if ( ! $original_product_id ) {
				$original_product_id = $post_id;
			}

			// 2. Load the original product
			$original_product = wc_get_product( $original_product_id );
			if ( ! $original_product ) {
				return;
			}

			// 3. Update the specific field and save
			switch ( $cell_key ) {
				case '_sku':
					$original_product->set_sku( $data_to_save );
					break;
				case '_regular_price':
					$original_product->set_regular_price( $data_to_save );
					break;
				case '_sale_price':
					$original_product->set_sale_price( $data_to_save );
					break;
				case '_sale_price_dates_from':
					$original_product->set_date_on_sale_from( $data_to_save );
					break;
				case '_sale_price_dates_to':
					$original_product->set_date_on_sale_to( $data_to_save );
					break;
				case '_stock':
					$original_product->set_stock_quantity( $data_to_save );
					break;
				case '_stock_status':
					$original_product->set_stock_status( $data_to_save );
					break;
				case '_manage_stock':
					$manage_stock = wc_string_to_bool( $data_to_save );
					$original_product->set_manage_stock( $manage_stock );
					break;
				case '_backorders':
					$original_product->set_backorders( $data_to_save );
					break;
				case '_sold_individually':
					$sold_individually = wc_string_to_bool( $data_to_save );
					$original_product->set_sold_individually( $sold_individually );
					break;
				case '_upsell_ids':
					$ids = is_array( $data_to_save ) ? $data_to_save : array_filter( array_map( 'trim', explode( ',', $data_to_save ) ) );
					$original_product->set_upsell_ids( $ids );
					break;
				case '_crosssell_ids':
					$ids = is_array( $data_to_save ) ? $data_to_save : array_filter( array_map( 'trim', explode( ',', $data_to_save ) ) );
					$original_product->set_cross_sell_ids( $ids );
					break;
				case '_thumbnail_id':
				case '_product_image_gallery':
				default:
					delete_post_meta( $original_product_id, $cell_key );
					$gallery_image_ids = array_filter( VGSE()->helpers->maybe_replace_urls_with_file_ids( explode( ',', $data_to_save ), $original_product_id ) );
					update_post_meta( $original_product_id, $cell_key, implode( ',', $gallery_image_ids ) );
					break;
			}

			$original_product->save();
		}

		/**
		 * Add fields to options page
		 * @param array $sections
		 * @return array
		 */
		function add_settings_page_options( $sections ) {
			$sections['misc']['fields'][] = array(
				'id'      => 'wpml_use_post_ids_instead_titles',
				'type'    => 'switch',
				'title'   => esc_html__( 'WPML - Use IDs in the column "Translation of" instead of Titles?', 'vg_sheet_editor' ),
				'desc'    => esc_html__( 'By default, we use post titles to connect translations with the default language, but it can cause issues if you have duplicate titles, so you can enable this option to display IDs and save using IDs. This applies to all the spreadsheets related to a post type.', 'vg_sheet_editor' ),
				'default' => false,
			);
			return $sections;
		}

		function product_updated_with_sql_formula( $column, $formula, $post_type, $spreadsheet_columns, $post_ids ) {
			if ( $post_type !== VGSE()->WC->post_type ) {
				return;
			}

			foreach ( $post_ids as $post_id ) {
				$this->_trigger_wpml_hook_after_wc_prices_updated( $post_id, array( $column ) );
			}
		}

		function product_updated_with_formula( $post_id, $initial_data, $modified_data, $column, $formula, $post_type, $cell_args, $spreadsheet_columns ) {
			if ( $post_type !== VGSE()->WC->post_type ) {
				return;
			}

			$this->_trigger_wpml_hook_after_wc_prices_updated( $post_id, array( $column ) );
		}

		function product_updated_on_spreadsheet( $product_id, $item, $data, $post_type ) {
			if ( ! in_array( $post_type, array( VGSE()->WC->post_type, 'product_variation' ), true ) ) {
				return;
			}

			$this->_trigger_wpml_hook_after_wc_prices_updated( $product_id, array_keys( $item ) );
		}

		function _trigger_wpml_hook_after_wc_prices_updated( $post_id, $updated_keys ) {
			global $woocommerce_wpml;

			if ( ! is_object( $woocommerce_wpml ) || ! is_object( $woocommerce_wpml->multi_currency ) ) {
				return;
			}

			$keywords_that_require_sync_regex = '/(sale_price|regular_price|wcml_schedule|sale_price_dates_from|sale_price_dates_to)/';
			if ( ! preg_match( $keywords_that_require_sync_regex, implode( ',', $updated_keys ) ) ) {
				return;
			}

			$currencies         = $woocommerce_wpml->multi_currency->get_currencies();
			$has_currency_price = false;
			foreach ( $currencies as $code => $currency ) {
				$sale_price    = wc_format_decimal( get_post_meta( $post_id, '_sale_price_' . $code, true ) );
				$regular_price = wc_format_decimal( get_post_meta( $post_id, '_regular_price_' . $code, true ) );

				if ( ! $has_currency_price && ( ! empty( $sale_price ) || ! empty( $regular_price ) ) ) {
					$has_currency_price = true;
					update_post_meta( $post_id, '_wcml_custom_prices_status', 1 );
				}

				$schedule  = get_post_meta( $post_id, '_wcml_schedule_' . $code, true );
				$date_from = get_post_meta( $post_id, '_sale_price_dates_from_' . $code, true );
				$date_to   = get_post_meta( $post_id, '_sale_price_dates_to_' . $code, true );

				$date_from = $schedule && ! empty( $date_from ) ? $date_from : '';
				$date_to   = $schedule && ! empty( $date_to ) ? $date_to : '';

				$custom_prices = apply_filters(
					'wcml_update_custom_prices_values',
					array(
						'_regular_price'         => $regular_price,
						'_sale_price'            => $sale_price,
						'_wcml_schedule'         => $schedule,
						'_sale_price_dates_from' => $date_from,
						'_sale_price_dates_to'   => $date_to,
					),
					$code,
					$post_id
				);
				$product_price = $woocommerce_wpml->multi_currency->custom_prices->update_custom_prices( $post_id, $custom_prices, $code );

				do_action( 'wcml_after_save_custom_prices', $post_id, $product_price, $custom_prices, $code );
			}
		}


		/**
		 * Don't import SKUs on WooCommerce product translations
		 * because it causes a bug in WPML where it updates the original product
		 *
		 * @param  array $args
		 * @return array
		 */
		function remove_sku_from_wc_product_translations_import( $args ) {
			if ( class_exists( 'WooCommerce' ) && $args['post_type'] === 'product' && ! WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) {
				foreach ( $args['data'] as $index => $row ) {
					if ( ! empty( $row['sku'] ) ) {
						unset( $args['data'][ $index ]['sku'] );
					}
				}
			}
			return $args;
		}

		function set_current_language_to_new_rows( $new_posts_ids, $post_type ) {
			if ( post_type_exists( $post_type ) ) {
				foreach ( $new_posts_ids as $post_id ) {
					$this->sync_translation_fields( $post_id );
				}
			}
			return $new_posts_ids;
		}

		function add_current_language_to_wc_rest_api_requests( $product ) {
			global $sitepress;
			if ( ! isset( $product['lang'] ) ) {
				$product['lang'] = $sitepress->get_current_language();
			}
			return $product;
		}
		function register_custom_filters( $sanitized_filters, $dirty_filters ) {

			if ( isset( $dirty_filters['wpml_translations_missing'] ) ) {
				$sanitized_filters['wpml_translations_missing'] = array();
				if ( is_array( $dirty_filters['wpml_translations_missing'] ) ) {
					foreach ( $dirty_filters['wpml_translations_missing'] as $language ) {
						if ( is_string( $language ) && preg_match( '/^[A-Za-z]{2}([-][A-Za-z]{2})?$/', $language ) ) {
							$sanitized_filters['wpml_translations_missing'][] = sanitize_text_field( $language );
						}
					}
				}
			}
			return $sanitized_filters;
		}


		function search_by_wpml_translation( $clauses, $wp_query ) {
			global $sitepress, $wpdb;

			if ( empty( $wp_query->query['wpse_original_filters'] ) || empty( $wp_query->query['wpse_original_filters']['wpml_translations_missing'] ) ) {
				return $clauses;
			}
			if ( ! WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) {
				return $clauses;
			}

			$missing_languages = $wp_query->query['wpse_original_filters']['wpml_translations_missing'];
			$wpml_languages    = wp_list_pluck( $sitepress->get_active_languages(), 'display_name', 'code' );

			// Sanitize. We remove any value received not found in the active wpml languages,
			// and any value that doesn't have 2 letters only.
			foreach ( $missing_languages as $index => $missing_language ) {
				if ( ! isset( $wpml_languages[ $missing_language ] ) || ! preg_match( '/^[A-Za-z]{2}([-][A-Za-z]{2})?$/', $missing_language ) ) {
					unset( $missing_languages[ $index ] );
				}
			}

			$sql = " AND wpml_translations.trid IN (
				SELECT trid
				FROM {$wpdb->prefix}icl_translations translations
				WHERE NOT EXISTS (
				SELECT inner_translations.trid
				FROM {$wpdb->prefix}icl_translations inner_translations
				WHERE inner_translations.trid = translations.trid
				AND inner_translations.language_code IN ('" . implode( "','", $missing_languages ) . "') ) ) ";

			$clauses['where'] .= $sql;

			return $clauses;
		}
		function add_wpml_language_search( $spreadsheet_key ) {
			global $sitepress;
			if ( ! VGSE()->helpers->get_current_provider()->is_post_type ) {
				return;
			}
			if ( ! WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) {
				return;
			}

			if ( ! is_post_type_translated( $spreadsheet_key ) ) {
				return;
			}
			$wpml_languages = wp_list_pluck( $sitepress->get_active_languages(), 'display_name', 'code' );
			?>
						<li class="wpml-languages-without-translations">
							<label><?php echo esc_html__( 'WPML - Missing translations in these languages', 'vg_sheet_editor' ); ?>  <a href="#" data-wpse-tooltip="right" aria-label="<?php esc_attr_e( 'For example, select "Spanish" and "German" here and we\'ll find products that don\'t have spanish translations or german translations.', 'vg_sheet_editor' ); ?>">( ? )</a></label>
							<select name="wpml_translations_missing[]" multiple class="select2" x-model="activeFilters.wpml_translations_missing" x-init="initSelect2($el)">
<option value="">--</option>
			<?php
			foreach ( $wpml_languages as $code => $display_name ) {
				?>
	<option value="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $display_name ); ?></option>
				<?php
			}
			?>
							</select>
						</li>

						<?php
		}

		public function add_lang_to_new_variations( $variation_ids ) {
			$this->add_lang_to_posts_if_missing( $variation_ids, 'product_variation' );
		}

		public function add_lang_to_posts_if_missing( $post_ids, $post_type ) {
			global $sitepress, $wpdb;
			$current_language = $sitepress->get_current_language();
			foreach ( $post_ids as $post_id ) {
				$has_lang = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}icl_translations WHERE element_type = %s AND element_id = %d", 'post_' . $post_type, $post_id ) );
				if ( ! $has_lang ) {
					$trid     = 1 + (int) $wpdb->get_var( "SELECT MAX(trid) FROM {$wpdb->prefix}icl_translations" );
					$response = $wpdb->insert(
						$wpdb->prefix . 'icl_translations',
						array(
							'element_type'  => 'post_' . $post_type,
							'element_id'    => $post_id,
							'trid'          => $trid,
							'language_code' => $current_language,
						)
					);
				}
			}
		}


		public function get_fields_to_sync( $row ) {
			if ( ! function_exists( 'wpml_get_setting' ) ) {
				return $row;
			}
			$out                 = array();
			$wpml_config         = wpml_get_setting( 'translation-management' );
			$wpml_custom_fields  = $wpml_config['custom_fields_translation'];
			$wpml_taxonomies     = wpml_get_setting( 'taxonomies_sync_option' );
			$wpml_custom_fields  = array_merge( $wpml_custom_fields, $wpml_taxonomies );
			$current_post        = get_post( $row['ID'] );
			$excluded_keys       = array( 'ID' );
			$row_post_type       = empty( $row['post_type'] ) ? $current_post->post_type : $row['post_type'];
			$spreadsheet_columns = VGSE()->helpers->get_unfiltered_provider_columns( $row_post_type );

			if ( $row_post_type === $current_post->post_type ) {
				$excluded_keys[] = 'post_type';
			}

			foreach ( $row as $field_key => $value ) {
				$is_meta_column = isset( $spreadsheet_columns[ $field_key ] ) && in_array( $spreadsheet_columns[ $field_key ]['data_type'], array( 'post_meta', 'meta_data' ), true );

				$wpml_field_key = $field_key;
				if ( isset( $spreadsheet_columns[ $field_key ] ) && ! empty( $spreadsheet_columns[ $field_key ]['serialized_field_original_key'] ) ) {
					$wpml_field_key = $spreadsheet_columns[ $field_key ]['serialized_field_original_key'];
				}

				// Exclude if it's a meta column and it's not found in the WPML config
				if ( ( $is_meta_column && ! isset( $wpml_custom_fields[ $wpml_field_key ] ) ) ||
				// Exclude if the field exists in the WPML config and it's marked as ignore
				// or translate (they don't require syncing because they're translated separately in each post)
				( isset( $wpml_custom_fields[ $wpml_field_key ] ) && in_array( (int) $wpml_custom_fields[ $wpml_field_key ], array( 0, 2 ), true ) ) ||
				// Exclude if the field is found in our manual exclusion list
				in_array( $field_key, $excluded_keys, true ) ) {
					continue;
				}
				$out[ $field_key ] = $value;
			}
			return $out;
		}

		public function after_wc_variations_updated( $final, $request, $variations_rows, $original_variation_rows ) {
			$has_fields_to_sync = false;
			foreach ( $original_variation_rows as $row ) {
				$syncable_fields = $this->get_fields_to_sync( $row );
				if ( $syncable_fields ) {
					$has_fields_to_sync = true;
					break;
				}
			}

			if ( $has_fields_to_sync ) {
				$this->sync_translation_fields( $final['ID'], $syncable_fields );
			}
		}
		public function sync_translation_fields_after_saving_post( $post_id, $item ) {
			if ( ! VGSE()->helpers->get_current_provider()->is_post_type ) {
				return;
			}
			$syncable_fields = $this->get_fields_to_sync( $item );
			if ( ! $syncable_fields ) {
				return;
			}
			// We don't sync variation changes here because variation haven't been saved yet. We save variations later in the page cycle
			if ( class_exists( 'WooCommerce' ) && ! empty( $item['post_type'] ) && $item['post_type'] === 'product_variation' ) {
				return;
			}

			$this->sync_translation_fields( $post_id, $syncable_fields );
		}

		public function sync_translation_fields_after_formula( $post_id, $initial_data, $modified_data, $column ) {

			if ( ! VGSE()->helpers->get_current_provider()->is_post_type ) {
				return;
			}
			$syncable_fields = $this->get_fields_to_sync( array( $column => $modified_data ) );
			if ( $syncable_fields ) {
				$this->sync_translation_fields( $post_id, $syncable_fields );
			}
		}

		public function disable_sql_formulas_to_allow_translation_syncing( $allowed ) {
			if ( VGSE()->helpers->get_current_provider()->is_post_type ) {
				$allowed = false;
			}
			return $allowed;
		}

		public function post_converted_to_product( $post_id ) {
			global $wpdb;
			$wpdb->update(
				$wpdb->prefix . 'icl_translations',
				array(
					'element_type' => 'post_product',
				),
				array(
					'element_type' => 'post_post',
					'element_id'   => $post_id,
				)
			);
		}

		public function sync_fields_if_new_post( $item, $post_id, $post_type ) {
			global $wpdb;
			if ( ! VGSE()->helpers->get_current_provider()->is_post_type ) {
				return $item;
			}
			$post = get_post( $post_id );
			$sql  = "SELECT * FROM {$wpdb->prefix}icl_translations WHERE element_type = %s AND element_id = %d";
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$row_exists = $wpdb->get_row( $wpdb->prepare( $sql, 'post_' . $post->post_type, $post_id ) );
			if ( ! $row_exists ) {
				$this->sync_translation_fields( $post_id );
			}
			return $item;
		}

		public function sync_translation_fields_after_wc_rest_variation_inserted( $variation, $request ) {
			// Exit if this REST request didn't come from WPSE
			if ( empty( $request['wpse_source'] ) ) {
				return;
			}
			// Exit if this REST request came from the save rows process
			// Because the save rows process has its own WPML syncing mechanism
			// We use this sync function only for other REST calls
			if ( $request['wpse_source'] === 'save_rows' ) {
				return;
			}
			$this->sync_translation_fields( $variation->get_id() );
		}



		/**
		 * Helper to map custom field keys/columns to WCML component types
		 * * @param array $field_keys Keys of the fields that were modified
		 * @return array Required components to synchronize
		 */
		private function get_wcml_components_from_fields( $field_keys ) {
			if ( ! class_exists( '\WCML\Synchronization\Store' ) ) {
				return array( 'meta' );
			}

			$components = array();

			foreach ( $field_keys as $key ) {
				if ( in_array( $key, array( '_stock', '_stock_status', '_manage_stock', '_backorders', 'stock', 'stock_status' ) ) ) {
					$components['stock'] = \WCML\Synchronization\Store::COMPONENT_STOCK;
				} elseif ( in_array( $key, array( '_upsell_ids', '_crosssell_ids', '_children', 'upsell_ids', 'crosssell_ids' ) ) ) {
					$components['linked'] = \WCML\Synchronization\Store::COMPONENT_LINKED;
				} elseif ( in_array( $key, array( '_thumbnail_id', '_product_image_gallery', 'thumbnail_id', 'product_image_gallery' ) ) ) {
					$components['attachments'] = \WCML\Synchronization\Store::COMPONENT_ATTACHMENTS;
				} elseif ( strpos( $key, 'pa_' ) === 0 || in_array( $key, array( 'product_cat', 'product_tag', 'tax:product_cat', 'tax:product_tag' ) ) ) {
					$components['taxonomies'] = \WCML\Synchronization\Store::COMPONENT_TAXONOMIES;
				} else {
					$components['meta'] = \WCML\Synchronization\Store::COMPONENT_META;
				}
			}

			return array_values( $components );
		}

		/**
		 * Trigger the proper synchronization logic
		 * * @param int $post_id The post ID to sync translations for
		 * @param array $syncable_fields Key/value pairs of fields that were modified. Empty = sync all.
		 */
		public function sync_translation_fields( $post_id, $syncable_fields = array() ) {
			if ( ! VGSE()->helpers->get_current_provider()->is_post_type ) {
				return;
			}

			$post_type = get_post_type( $post_id );
			if ( ! $post_type ) {
				return;
			}

			// Only run if WPML is active
			if ( ! has_filter( 'wpml_original_element_id' ) ) {
				return;
			}

			$element_type = 'post_' . $post_type;

			// Add translation language if missing
			$this->add_lang_to_posts_if_missing( array( $post_id ), $post_type );
			$original_post_id = apply_filters( 'wpml_original_element_id', null, $post_id, $element_type );

			if ( ! $original_post_id ) {
				$original_post_id = $post_id;
			}
			// For regular posts and other custom post types, use standard WPML core sync
			// WC also needs this to sync general custom fields
			do_action( 'wpml_sync_all_custom_fields', $original_post_id );

			// Use the WCML Hooks abstraction for WooCommerce entities
			if ( in_array( $post_type, array( 'product', 'product_variation' ), true ) ) {
				if ( class_exists( '\WCML\Synchronization\Hooks' ) ) {

					$trid                   = apply_filters( 'wpml_element_trid', 0, $original_post_id, 'post_' . $post_type );
					$translations           = apply_filters( 'wpml_get_element_translations', array(), $trid, $element_type );
					$translations_ids       = array();
					$translations_languages = array();

					if ( is_array( $translations ) ) {
						foreach ( $translations as $translation ) {
							if ( $translation->element_id != $original_post_id ) {
								$translations_ids[]                                 = $translation->element_id;
								$translations_languages[ $translation->element_id ] = $translation->language_code;
							}
						}
					}

					if ( ! empty( $translations_ids ) ) {
						// 1. If we are saving a variation, use the variation-specific hook.
						if ( $post_type === 'product_variation' ) {
							do_action(
								\WCML\Synchronization\Hooks::HOOK_SYNCHRONIZE_PRODUCT_VARIATION_TRANSLATIONS,
								get_post( $original_post_id ),
								$translations_ids,
								$translations_languages
							);

						} else {
							// 2. Parent Product - Check if we have specific fields mapped to run specific components
							if ( empty( $syncable_fields ) ) {
								do_action(
									\WCML\Synchronization\Hooks::HOOK_SYNCHRONIZE_PRODUCT_TRANSLATIONS,
									get_post( $original_post_id ),
									$translations_ids,
									$translations_languages
								);
							} else {
								$components = $this->get_wcml_components_from_fields( array_keys( $syncable_fields ) );

								foreach ( $components as $component ) {
									do_action(
										\WCML\Synchronization\Hooks::HOOK_SYNCHRONIZE_PRODUCT_COMPONENT,
										get_post( $original_post_id ),
										$translations_ids,
										$translations_languages,
										$component
									);
								}
							}
						}
					}
				}
			}
		}

		public function get_main_post_id( $post_id ) {
			global $wpdb;
			$main_trid = (int) WP_Sheet_Editor_WPML_Obj()->get_main_translation_id( $post_id, 'post_' . get_post_type( $post_id ), true );

			$main_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid = %d AND source_language_code IS NULL", (int) $main_trid ) );

			return $main_id;
		}

		public function stop_automatic_wpml_syncing( $data, $post_type ) {
			global $wpml_post_translations;
			if ( ! VGSE()->helpers->get_current_provider()->is_post_type ) {
				return;
			}
			remove_action( 'save_post', array( $wpml_post_translations, 'save_post_actions' ), 100 );
		}

		/**
		 * Register spreadsheet columns
		 */
		public function register_columns( $editor ) {
			global $sitepress;
			if ( $editor->provider->key === 'user' ) {
				return;
			}
			if ( ! $editor->provider->is_post_type ) {
				return;
			}
			$post_types     = $editor->args['enabled_post_types'];
			$languages      = wp_list_pluck( $sitepress->get_active_languages(), 'display_name', 'code' );
			$term_separator = VGSE()->helpers->get_term_separator();
			$default_lang   = $sitepress->get_default_language();
			if ( isset( $languages[ $default_lang ] ) ) {
				unset( $languages[ $default_lang ] );
			}
			foreach ( $post_types as $post_type ) {
				if ( WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) {
					$editor->args['columns']->register_item(
						'wpml_duplicate',
						$post_type,
						array(
							'data_type'             => 'meta_data',
							'column_width'          => 150,
							'title'                 => esc_html__( 'WPML - Duplicate', 'vg_sheet_editor' ),
							'supports_formulas'     => true,
							'supports_sql_formulas' => false,
							'allow_plain_text'      => true,
							'formatted'             => array(
								'editor'        => 'wp_chosen',
								'selectOptions' => $languages,
								'chosenOptions' => array(
									'multiple'        => true,
									'search_contains' => true,
								),
								/* translators: %1$s: Term separator character, %2$s: Term separator character */
								'comment'       => array( 'value' => sprintf( esc_html__( 'Enter multiple language codes separated by %1$s and we will create copies of the main language. For example: en%2$s es. Existing languages will be skipped.', 'vg_sheet_editor' ), $term_separator, $term_separator ) ),
							),
							'save_value_callback'   => array( $this, 'duplicate_to_language' ),
						)
					);
				}
				$editor->args['columns']->register_item(
					'icl_translation_of',
					$post_type,
					array(
						'data_type'             => 'meta_data',
						'column_width'          => 200,
						'title'                 => esc_html__( 'WPML - Translation of', 'vg_sheet_editor' ),
						'supports_formulas'     => true,
						'supports_sql_formulas' => false,
						'allow_plain_text'      => true,
						'get_value_callback'    => array( $this, 'get_translation_of_cell' ),
						'save_value_callback'   => array( $this, 'update_translation_of_cell' ),
						'is_locked'             => WP_Sheet_Editor_WPML_Obj()->is_the_default_language(),
						'allow_to_save'         => ( WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) ? false : true,
					)
				);
				$editor->args['columns']->register_item(
					'wpml_relationship',
					$post_type,
					array(
						'data_type'             => 'meta_data',
						'column_width'          => 150,
						'title'                 => esc_html__( 'WPML - Relationship', 'vg_sheet_editor' ),
						'supports_formulas'     => true,
						'supports_sql_formulas' => false,
						'allow_plain_text'      => true,
						'formatted'             => array(
							'editor'        => 'select',
							'selectOptions' => array(
								''                     => '',
								'duplicate_from_main'  => esc_html__( 'Duplicate from the main language', 'vg_sheet_editor' ),
								'translate_separately' => esc_html__( 'Translate separately', 'vg_sheet_editor' ),
							),
						),
						'save_value_callback'   => array( $this, 'set_translation_relationship' ),
						'get_value_callback'    => array( $this, 'get_translation_relationship' ),
						'is_locked'             => WP_Sheet_Editor_WPML_Obj()->is_the_default_language(),
						'allow_to_save'         => ( WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) ? false : true,
					)
				);
				$editor->args['columns']->register_item(
					'wpml_language',
					$post_type,
					array(
						'data_type'             => 'meta_data',
						'column_width'          => 150,
						'title'                 => esc_html__( 'WPML - Language', 'vg_sheet_editor' ),
						'supports_formulas'     => true,
						'supports_sql_formulas' => false,
						'allow_plain_text'      => true,
						'allow_to_save'         => true,
						'formatted'             => array(
							'editor'        => 'select',
							'selectOptions' => wp_list_pluck( $sitepress->get_active_languages(), 'display_name', 'code' ),
							'comment'       => ( WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) ? null : array( 'value' => esc_html__( 'You can change the language of this post. If the translation for the new language exists, this change will not be applied.', 'vg_sheet_editor' ) ),
						),
						'get_value_callback'    => array( $this, 'get_post_language' ),
						'save_value_callback'   => array( $this, 'save_post_language' ),
					)
				);
				$editor->args['columns']->register_item(
					'translation_priority',
					$post_type,
					array(
						'data_type'         => 'post_terms',
						'column_width'      => 150,
						'title'             => esc_html__( 'WPML - Translation priority', 'vg_sheet_editor' ),
						'supports_formulas' => true,
						'formatted'         => array(
							'type'   => 'autocomplete',
							'source' => 'loadTaxonomyTerms',
						),
					)
				);
			}
		}

		public function get_post_language( $post, $cell_key, $cell_args ) {
			global $wpdb;

			return $wpdb->get_var( $wpdb->prepare( 'SELECT language_code FROM ' . $wpdb->prefix . 'icl_translations WHERE element_type = %s AND element_id = %d', 'post_' . $post->post_type, $post->ID ) );
		}

		public function get_translation_relationship( $post, $cell_key, $cell_args ) {
			$duplicate_of = (int) get_post_meta( $post->ID, '_icl_lang_duplicate_of', true );
			$value        = $duplicate_of ? 'duplicate_from_main' : 'translate_separately';
			return $value;
		}

		public function get_translation_of_cell( $post, $cell_key, $cell_args ) {
			$main_id = (int) $this->get_main_post_id( $post->ID );
			if ( $main_id === $post->ID ) {
				return '';
			}
			$value = $main_id;
			$value = VGSE()->get_option( 'wpml_use_post_ids_instead_titles' ) ? $value : get_the_title( $value );
			return $value;
		}

		public function update_translation_of_cell( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			global $wpdb, $sitepress;
			$data_to_save = trim( $data_to_save );
			if ( empty( $data_to_save ) ) {
				$wpdb->update(
					$wpdb->prefix . 'icl_translations',
					array(
						'source_language_code' => null,
						'language_code'        => $sitepress->get_current_language(),
					),
					array(
						'element_id'   => (int) $post_id,
						'element_type' => 'post_' . esc_sql( $post_type ),
					),
					array( '%s', '%s' ),
					array( '%d', '%s' )
				);
				return;
			}

			if ( is_numeric( $data_to_save ) && get_post_status( (int) $data_to_save ) ) {
				$main_post_id = (int) $data_to_save;
			} else {
				$main_post_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s AND post_title = %s LIMIT 1 ", $post_type, $data_to_save ) );
			}
			if ( $main_post_id ) {
				$trid = WP_Sheet_Editor_WPML_Obj()->get_main_translation_id( $main_post_id, 'post_' . esc_sql( $post_type ), is_numeric( $data_to_save ) );
				$wpdb->update(
					$wpdb->prefix . 'icl_translations',
					array(
						'trid'                 => $trid,
						'source_language_code' => $sitepress->get_default_language(),
					),
					array(
						'element_type' => 'post_' . esc_sql( $post_type ),
						'element_id'   => (int) $post_id,
					)
				);
			}
		}

		public function set_translation_relationship( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			global $iclTranslationManagement, $sitepress, $wpdb;

			if ( $data_to_save === 'duplicate_from_main' ) {
				$original_id = (int) $this->get_main_post_id( $post_id );
				$iclTranslationManagement->set_duplicate( $original_id, $sitepress->get_current_language() );
			} elseif ( $data_to_save === 'translate_separately' ) {
				$iclTranslationManagement->reset_duplicate_flag( $post_id );
			} else {
				return;
			}
		}

		public function duplicate_to_language( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			global $iclTranslationManagement, $wpdb;

			// Skip if the post to be duplicated is not the original post
			$main_post_id = $this->get_main_post_id( $post_id );
			if ( $main_post_id !== $post_id ) {
				return;
			}

			// Remove the flag _icl_lang_duplicate_of because main posts are not duplicates of other posts
			// If this flag is found, it will cause a server timeout because it should never exist in original posts
			$duplicate_of = get_post_meta( $post_id, '_icl_lang_duplicate_of', true );
			if ( $duplicate_of ) {
				delete_post_meta( $post_id, '_icl_lang_duplicate_of' );
			}

			$mdata                       = array(
				'duplicate_to' => array(),
			);
			$mdata['iclpost']            = array( $post_id );
			$new_langs                   = array_filter( array_map( 'trim', explode( VGSE()->helpers->get_term_separator(), strtolower( $data_to_save ) ) ) );
			$existing_languages_for_post = $wpdb->get_col( $wpdb->prepare( "SELECT language_code FROM {$wpdb->prefix}icl_translations WHERE trid IN (SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id = %d AND element_type LIKE %s)", $post_id, 'post\_%' ) );
			$new_langs                   = array_diff( $new_langs, $existing_languages_for_post );
			if ( empty( $new_langs ) ) {
				return;
			}
			foreach ( $new_langs as $lang ) {
				$mdata['duplicate_to'][ $lang ] = 1;
			}

			$iclTranslationManagement->make_duplicates( $mdata );
			do_action( 'wpml_new_duplicated_terms', (array) $mdata['iclpost'], false );
		}

		public function save_post_language( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			global $wpdb, $sitepress;

			$new_language = strtolower( $data_to_save );
			// This if was preventing us from being able to move translations from one language to another
			// if ( ! icl_is_language_active( $data_to_save ) ) {
			//  return;
			// }

			// Exit if there is a translation in the new language already
			$translation_for_new_language_exists = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'icl_translations WHERE language_code = %s AND element_type = %s AND element_id = %d ', $new_language, 'post_' . $post_type, $post_id ) );
			if ( $translation_for_new_language_exists ) {
				return;
			}

			$current_lang = $this->get_post_language( get_post( $post_id ), null, null );

			$args = array(
				'language_code'        => $new_language,
				// Don't set a source lang if the new language is the default language, or we're moving from the default lang into another lang
				'source_language_code' => ( $new_language === $sitepress->get_default_language() || $current_lang === $sitepress->get_default_language() ) ? null : $sitepress->get_default_language(),
			);

			$wpdb->update(
				$wpdb->prefix . 'icl_translations',
				$args,
				array(
					'element_type' => 'post_' . esc_sql( $post_type ),
					'element_id'   => (int) $post_id,
				)
			);

			// If we change the language of a parent post, automatically change the language of the children.
			// I.e. if we change the language of a WC product, change it in the variations too
			$children = $wpdb->get_results( $wpdb->prepare( "SELECT ID,post_type FROM {$wpdb->posts} WHERE post_parent = %d", $post_id ), ARRAY_A );
			foreach ( $children as $child ) {
				$this->save_post_language( (int) $child['ID'], $cell_key, $data_to_save, $child['post_type'], $cell_args, $spreadsheet_columns );
			}
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new WPSE_WPML_Posts();
				self::$instance->init();
			}
			return self::$instance;
		}

		public function __set( $name, $value ) {
			$this->$name = $value;
		}

		public function __get( $name ) {
			return $this->$name;
		}
	}

}

if ( ! function_exists( 'WPSE_WPML_Posts_Obj' ) ) {

	function WPSE_WPML_Posts_Obj() {
		return WPSE_WPML_Posts::get_instance();
	}
}
WPSE_WPML_Posts_Obj();
