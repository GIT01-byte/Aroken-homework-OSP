<?php defined( 'ABSPATH' ) || exit;

if ( ! defined( 'VGSE_WC_FILE' ) ) {
	define( 'VGSE_WC_FILE', __FILE__ );
}
if ( ! defined( 'VGSE_WC_DIR' ) ) {
	define( 'VGSE_WC_DIR', __DIR__ );
}
if ( ! class_exists( 'WP_Sheet_Editor_WooCommerce' ) ) {

	/**
	 * Edit all your products information in the spreadsheet editor.
	 */
	class WP_Sheet_Editor_WooCommerce {

		private static $instance                  = false;
		public $post_type                         = null;
		public $variations                        = null;
		public $core_columns_list                 = array();
		public $core_to_woo_importer_columns_list = array();
		public $special_columns_import_prefixes   = array();
		private $total_sales_cache                = array();
		private $total_sales_version              = null;

		private function __construct() {
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new WP_Sheet_Editor_WooCommerce();
				self::$instance->init();
			}
			return self::$instance;
		}

		/**
		 * Convert a value to boolean
		 * @param str|bool $item
		 * @deprecated 2.25.3-beta.1
		 * @return boolean
		 */
		function _do_booleable( $item ) {
			if ( in_array( $item, array( 'yes', 'instock', 'open', '1', 1, true, 'true', 'on' ), true ) ) {
				return true;
			}
			return false;
		}

		/**
		 * is woocommerce plugin active?
		 * @return boolean
		 */
		function is_woocommerce_active() {
			return class_exists( 'WooCommerce' );
		}

		function init() {

			$this->post_type = apply_filters( 'vg_sheet_editor/woocommerce/product_post_type_key', 'product' );

			// exit if woocommerce plugin is not active
			if ( ! $this->is_woocommerce_active() ) {
				return;
			}

			$this->special_columns_import_prefixes   = array(
				'downloads_name',
				'downloads_url',
				'attributes_name',
				'attributes_value',
				'attributes_taxonomy',
				'attributes_visible',
				'attributes_default',
			);
			$this->core_to_woo_importer_columns_list = array_merge(
				array(
					'ID'                           => 'ID',
					'post_content'                 => 'description',
					'post_title'                   => 'name',
					'post_excerpt'                 => 'short_description',
					'_vgse_variation_enabled'      => 'published',
					'comment_status'               => 'reviews_allowed',
					'post_parent'                  => 'parent_id',
					'post_type'                    => 'type',
					'_sale_price'                  => 'sale_price',
					'_sku'                         => 'sku',
					'_global_unique_id'            => 'global_unique_id',
					'_price'                       => 'price',
					'_sale_price_dates_from'       => 'date_on_sale_from',
					'_sale_price_dates_to'         => 'date_on_sale_to',
					'_tax_status'                  => 'tax_status',
					'_tax_class'                   => 'tax_class',
					'_manage_stock'                => '_manage_stock',
					'_backorders'                  => 'backorders',
					'_low_stock_amount'            => 'low_stock_amount',
					'_sold_individually'           => 'sold_individually',
					'_weight'                      => 'weight',
					'_length'                      => 'length',
					'_width'                       => 'width',
					'_height'                      => 'height',
					'_upsell_ids'                  => 'upsell_ids',
					'_crosssell_ids'               => 'cross_sell_ids',
					'product_shipping_class'       => 'shipping_class',
					'_purchase_note'               => 'purchase_note',
					'_default_attributes'          => 'attributes',
					'_virtual'                     => 'type',
					'_downloadable'                => 'type',
					'_download_limit'              => 'download_limit',
					'_download_expiry'             => 'download_expiry',
					'_stock'                       => 'stock',
					'_stock_status'                => 'stock_status',
					'_downloadable_files'          => 'downloads',
					'wpse_downloadable_file_names' => 'downloads',
					'wpse_downloadable_file_urls'  => 'downloads',
					'_product_attributes'          => 'attributes',
					'_regular_price'               => 'regular_price',
					'_product_image_gallery'       => 'images',
					'_thumbnail_id'                => 'images',
					'_variation_description'       => 'short_description',
					'_children'                    => '_children',
					'_product_url'                 => 'product_url',
					'_button_text'                 => 'button_text',
					'product_cat'                  => 'category_ids',
					'product_brand'                => 'brand_ids',
					'product_tag'                  => 'tag_ids',
					'product_visibility'           => 'catalog_visibility',
					'product_type'                 => 'type',
					'_download_type'               => 'download_type',
					'_featured'                    => 'featured',
					'default_attributes'           => 'attributes',
					'_vgse_create_attribute'       => 'attributes',
				),
				array_fill_keys( wc_get_attribute_taxonomy_names(), 'attributes' )
			);

			if ( ! empty( VGSE()->options['wc_use_separate_image_columns'] ) ) {
				unset( $this->core_to_woo_importer_columns_list['_thumbnail_id'] );
				unset( $this->core_to_woo_importer_columns_list['_product_image_gallery'] );
			}

			// Include files
			require_once 'inc/attributes.php';
			require_once 'inc/variations.php';
			require_once 'inc/import-export.php';
			require_once 'inc/downloadable.php';
			require_once 'inc/formatting.php';
			$this->variations = WP_Sheet_Editor_WooCommerce_Variations::get_instance();

			$this->core_columns_list = array_unique( array_merge( array_keys( $this->core_to_woo_importer_columns_list ), WP_Sheet_Editor_WooCommerce_Variations::get_instance()->get_variation_whitelisted_columns() ) );

			// init wp hooks
			add_action( 'vg_sheet_editor/columns/all_items', array( $this, 'filter_columns_settings' ), 10, 3 );
			add_action( 'vg_sheet_editor/editor/register_columns', array( $this, 'register_columns' ) );
			add_filter( 'vg_sheet_editor/allowed_post_types', array( $this, 'allow_product_post_type' ) );
			add_filter( 'vg_sheet_editor/add_new_posts/create_new_posts', array( $this, 'create_new_products' ), 10, 3 );

			add_filter( 'vg_sheet_editor/after_enqueue_assets', array( $this, 'enqueue_assets' ) );
			add_filter( 'vg_sheet_editor/load_rows/full_output', array( $this, 'calculate_inventory_totals' ), 10, 2 );
			add_filter( 'vg_sheet_editor/editor_page/console_items', array( $this, 'add_inventory_totals_holder_to_console' ), 10, 2 );
			add_filter( 'vg_sheet_editor/formulas/sql_execution/can_execute', array( $this, 'disallow_formula_sql_execution_on_special_columns' ), 10, 4 );
			add_filter( 'vg_sheet_editor/columns/blacklisted_columns', array( $this, 'disable_wc_private_columns' ), 10, 2 );
			add_filter( 'vg_sheet_editor/js_data', array( $this, 'watch_cells_to_lock' ), 10, 2 );
			add_filter( 'vg_sheet_editor/filters/allowed_fields', array( $this, 'register_filters' ), 11, 2 );
			add_filter( 'vg_sheet_editor/custom_columns/all_meta_keys', array( $this, 'disable_serialized_keys_from_automatic_columns' ), 10, 2 );
			add_filter( 'vg_sheet_editor/formulas/execute/get_duplicate_items_sql', array( $this, 'get_duplicate_skus_sql' ), 10, 6 );

			add_filter( 'vg_sheet_editor/options_page/options', array( $this, 'add_settings_page_options' ) );
			add_filter( 'vg_sheet_editor/load_rows/preload_data', array( $this, 'preload_total_sales' ), 10, 5 );

			if ( version_compare( WC()->version, '3.6.0' ) >= 0 ) {
				add_filter( 'vg_sheet_editor/filteres/search_by_keyword_clauses', array( $this, 'include_sku_in_search_by_keyword' ), 10, 4 );
			}
			add_filter( 'vg_sheet_editor/formulas/form_settings', array( $this, 'formulas_faciliate_copy_from_regular_price' ), 10, 2 );
			add_filter( 'vg_sheet_editor/formulas/quick_actions', array( $this, 'add_quick_bulk_actions' ), 10, 2 );
			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'render_shortcut_in_products_metabox' ) );
		}

		function render_shortcut_in_products_metabox() {
			global $post;
			$spreadsheet_url = esc_url(
				add_query_arg(
					array(
						'wpse_custom_filters'       => array(
							'keyword'               => $post->ID,
							'search_variations'     => 'yes',
							'wc_display_variations' => 'yes',
						),
						'wpse_custom_filters_nonce' => wp_create_nonce( 'bep-nonce' ),
					),
					VGSE()->helpers->get_editor_url( 'product' )
				)
			);
			include VGSE_WC_DIR . '/views/product-metabox-shortcut.php';
		}

		function add_quick_bulk_actions( $actions, $post_type ) {
			if ( $post_type !== $this->post_type ) {
				return $actions;
			}

			$actions['remove_duplicates_by_sku_latest'] = array(
				'label'                  => esc_html__( 'Remove duplicates by sku (delete the latest)', 'vg_sheet_editor' ),
				'columns'                => array( '_sku' ),
				'allow_to_select_column' => false,
				'type_of_edit'           => 'remove_duplicates',
				'values'                 => array( 'delete_latest' ),
				'wp_handler'             => false,
			);
			$actions['remove_duplicates_by_sku_oldest'] = array(
				'label'                  => esc_html__( 'Remove duplicates by sku (delete the oldest)', 'vg_sheet_editor' ),
				'columns'                => array( '_sku' ),
				'allow_to_select_column' => false,
				'type_of_edit'           => 'remove_duplicates',
				'values'                 => array( 'delete_oldest' ),
				'wp_handler'             => false,
			);

			return $actions;
		}

		function formulas_faciliate_copy_from_regular_price( $form_builder_args, $post_type ) {
			if ( $post_type !== $this->post_type ) {
				return $form_builder_args;
			}

			if ( isset( $form_builder_args['default_actions']['merge_columns'] ) ) {
				$form_builder_args['default_actions']['merge_columns']['disallowed_column_keys'][] = '_sale_price';
				$form_builder_args['default_actions']['merge_columns']['disallowed_column_keys'][] = '_regular_price';
			}

			$form_builder_args['columns_actions']['number']['wc_regular_price_decrease_number']     = 'default';
			$form_builder_args['columns_actions']['number']['wc_regular_price_decrease_percentage'] = 'default';
			$form_builder_args['default_actions']['wc_regular_price_decrease_number']               = array(
				'label'               => esc_html__( 'Copy regular price and decrease number', 'vg_sheet_editor' ),
				'description'         => '',
				'fields_relationship' => 'AND',
				'jsCallback'          => 'vgseWcRegularPriceDecreaseNumberFormula',
				'allowed_column_keys' => array( '_sale_price', '_bto_base_sale_price' ),
				'input_fields'        => array(
					array(
						'tag'        => 'input',
						'html_attrs' => array(
							'type' => 'number',
							'step' => '0.01',
						),
						'label'      => esc_html__( 'Decrease by', 'vg_sheet_editor' ),
					),
				),
			);
			$form_builder_args['default_actions']['wc_regular_price_decrease_percentage']           = array(
				'label'               => esc_html__( 'Copy regular price and decrease by percentage', 'vg_sheet_editor' ),
				'description'         => '',
				'fields_relationship' => 'AND',
				'jsCallback'          => 'vgseWcRegularPriceDecreasePercentageFormula',
				'allowed_column_keys' => array( '_sale_price', '_bto_base_sale_price' ),
				'input_fields'        => array(
					array(
						'tag'        => 'input',
						'html_attrs' => array(
							'type' => 'number',
							'step' => '0.01',
						),
						'label'      => esc_html__( 'Decrease by percentage', 'vg_sheet_editor' ),
					),
				),
			);

			return $form_builder_args;
		}

		function include_sku_in_search_by_keyword( $clauses, $raw_keywords, $operator, $internal_join ) {
			global $wpdb;
			if ( VGSE()->helpers->get_provider_from_query_string() !== 'product' ) {
				return $clauses;
			}
			$phrases         = array_map( 'trim', explode( ';', $raw_keywords ) );
			$checks          = array();
			$prepared_values = array();
			foreach ( $phrases as $phrase ) {
				$checks[]          = " $internal_join lookup.sku $operator %s ";
				$prepared_values[] = '%' . $wpdb->esc_like( $phrase ) . '%';
			}

			$clauses['join']     .= " INNER JOIN {$wpdb->wc_product_meta_lookup} AS lookup ON $wpdb->posts.ID = lookup.product_id ";
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$wc_query             = $wpdb->prepare( implode( '', $checks ), $prepared_values );
			$last_text_to_replace = ') ';
			// Find the position of the last " )"
			$last_parenthesis_position = strrpos( $clauses['where'], $last_text_to_replace );

			// Perform the replacement
			if ( $last_parenthesis_position !== false ) {
				$clauses['where'] = substr_replace( $clauses['where'], $wc_query . ' ) ', $last_parenthesis_position, strlen( $last_text_to_replace ) );
			}

			return $clauses;
		}

		/**
		 * Add fields to options page
		 * @param array $sections
		 * @return array
		 */
		function add_settings_page_options( $sections ) {
			$sections['speed']['fields'][] = array(
				'id'       => 'wc_products_variation_copy_batch_size',
				'type'     => 'text',
				'validate' => 'numeric',
				'title'    => esc_html__( 'Copy variations faster: Number of variations to copy per batch', 'vg_sheet_editor' ),
				'desc'     => esc_html__( 'Here you can control the batch size for the copy of variations. If you use a high number the copy will finish faster but it can saturate your server. We recommend a low value like 50 so it will not saturate your server although it might be slower', 'vg_sheet_editor' ),
				'default'  => 50,
			);
			$sections['wc_products']       = array(
				'icon'   => 'el-icon-cogs',
				'title'  => esc_html__( 'WooCommerce products', 'vg_sheet_editor' ),
				'fields' => array(
					array(
						'id'      => 'be_disable_woocommerce_inventory_stats',
						'type'    => 'switch',
						'title'   => esc_html__( 'Disable product inventory stats?', 'vg_sheet_editor' ),
						'desc'    => esc_html__( 'The WooCommerce products spreadsheet automatically generates inventory units and inventory price stats and shows the stats above the spreadsheet. This can slow down the sheet if you have several thousand products. Disable it if the spreadsheet is too slow or you see errors when loading rows.', 'vg_sheet_editor' ),
						'default' => false,
					),
					array(
						'id'      => 'wc_use_separate_image_columns',
						'type'    => 'switch',
						'title'   => esc_html__( 'Use separate columns for images during export and import?', 'vg_sheet_editor' ),
						'desc'    => esc_html__( 'By default, we export and import one column named "Images" with the featured image and gallery images combined. Activate this option to export and import one column with featured image and one column with gallery images', 'vg_sheet_editor' ),
						'default' => false,
					),
					array(
						'id'      => 'wc_allow_other_plugins_to_filter_stock',
						'type'    => 'switch',
						'title'   => esc_html__( 'Allow other plugins to filter the value when reading and saving the stock?', 'vg_sheet_editor' ),
						'desc'    => esc_html__( 'When enabled, other plugins will be able to filter the stock quantity using standard WooCommerce filters when reading and saving.', 'vg_sheet_editor' ),
						'default' => false,
					),
					array(
						'id'      => 'be_disable_wc_auto_attribute_used_for_variation',
						'type'    => 'switch',
						'title'   => esc_html__( 'Disable the automatic activation of "used for variations" for attributes on variable products?', 'vg_sheet_editor' ),
						'desc'    => esc_html__( 'By default, if the product is variable, we activate "used for variations" to save you time, so you can create your variations faster. But if you prefer to manually activate it on specific attributes, you can activate this option.', 'vg_sheet_editor' ),
						'default' => false,
					),
					array(
						'id'      => 'be_disable_wc_auto_attribute_visible',
						'type'    => 'switch',
						'title'   => esc_html__( 'Disable the automatic activation of "is visible" for attributes on products?', 'vg_sheet_editor' ),
						'desc'    => esc_html__( 'By default, we make the attributes visible in the products when you save any attribute in the spreadsheet to save you time, but if you prefer to manually activate it on specific attributes, you can activate this option.', 'vg_sheet_editor' ),
						'default' => false,
					),
					array(
						'id'    => 'wc_product_attributes_is_not_visible',
						'type'  => 'text',
						'title' => esc_html__( 'Product attributes not visible', 'vg_sheet_editor' ),
						'desc'  => esc_html__( 'The plugin will mark as visible all the attributes that DONT contain these keywords in the attribute key, enter multiple separated by comma. I.e. "car, airplane" would match "Car model, Car marker, Expensive Airplane, airplanes". This applies after editing a product in the spreadsheet cells.', 'vg_sheet_editor' ),
					),
					array(
						'id'    => 'wc_product_attributes_not_variation',
						'type'  => 'text',
						'title' => esc_html__( 'Product attributes not used for variations', 'vg_sheet_editor' ),
						'desc'  => esc_html__( 'The plugin will mark as used for variations all the attributes that DONT contain these keywords in the attribute key, enter multiple separated by comma. I.e. "car, airplane" would match "Car model, Car marker, Expensive Airplane, airplanes". This applies after editing a product in the spreadsheet cells.', 'vg_sheet_editor' ),
					),
					array(
						'id'    => 'maximum_variations_combination',
						'type'  => 'text',
						'title' => esc_html__( 'Maximum number of variations per combination of attributes', 'vg_sheet_editor' ),
						'desc'  => esc_html__( 'The "Create variations" tool allows you to create variation based on the combination of attributes. The default limit is 200 variations to not overload your server. You can increase the limit here if you need more variations.', 'vg_sheet_editor' ),
					),
					array(
						'id'    => 'wc_products_custom_attribute_names',
						'type'  => 'text',
						'title' => esc_html__( 'Custom attribute names', 'vg_sheet_editor' ),
						'desc'  => esc_html__( 'We will create columns for these custom attributes. Enter multiple names separated with commas', 'vg_sheet_editor' ),
					),
					array(
						'id'      => 'allow_to_see_variation_url_slug',
						'type'    => 'switch',
						'title'   => esc_html__( 'Allow to see the variation URL slug?', 'vg_sheet_editor' ),
						'desc'    => esc_html__( 'Variations dont use URL slugs but this can be useful if you want to see the previous URL of the variation after you converted a simple product into a variation.', 'vg_sheet_editor' ),
						'default' => false,
					),
					array(
						'id'      => 'update_lookup_table_always',
						'type'    => 'switch',
						'title'   => esc_html__( 'Update the lookup table and run the WC Webhooks always?', 'vg_sheet_editor' ),
						'desc'    => esc_html__( 'By default, for performance reasons, we trigger the WC API events when fields related to WC are edited: Prices, SKU, Stock, Attributes, Images, Type. Activate this option if you want to trigger the WC API events when any field is edited, including fields unrelated to WC.', 'vg_sheet_editor' ),
						'default' => false,
					),
					array(
						'id'      => 'update_parent_lookup_table_on_variation_edit',
						'type'    => 'switch',
						'title'   => esc_html__( 'Update the lookup table and run the WC Webhooks of the parent product when a variation is edited?', 'vg_sheet_editor' ),
						'desc'    => esc_html__( 'By default, we only update the lookup table and run webhooks for the variation that was edited, but maybe you also need the parent product to be updated.', 'vg_sheet_editor' ),
						'default' => false,
					),
					array(
						'id'      => 'wc_product_transients_purge',
						'type'    => 'new_select',
						'options' => array(
							''         => esc_html__( 'Clear the product transients when each product is edited (default)', 'vg_sheet_editor' ),
							'delayed'  => esc_html__( 'Clear the product transients 2 minutes after each batch of edits is completed', 'vg_sheet_editor' ),
							'disabled' => esc_html__( 'Do not clear the product transients, I\'ll do it manually or using custom code', 'vg_sheet_editor' ),
						),
						'title'   => esc_html__( 'How the product transients are purged', 'vg_sheet_editor' ),
						'desc'    => esc_html__( 'By default, we purge the product transients immediately after a product is edited, this allows you to see the changes applied in the front end instantly. But if your site receives a lot of visitors and you do many product edits, purging transients often can cause performance degradation, so you might want to delay the purge to a more convenient time', 'vg_sheet_editor' ),
						'default' => '',
					),
					array(
						'id'      => 'use_simple_wc_lookup_update',
						'type'    => 'new_select',
						'options' => array(
							''       => esc_html__( 'Call most WC hooks and update the meta lookup table (default)', 'vg_sheet_editor' ),
							'simple' => esc_html__( 'Only update the meta lookup table and purge WC caches, and don\'t call all the hooks (faster, prone to errors with other plugins)', 'vg_sheet_editor' ),
						),
						'title'   => esc_html__( 'What WC hooks to call after saving a product', 'vg_sheet_editor' ),
						'desc'    => esc_html__( 'By default, we call $product->save() after editing a product to ensure WooCommerce updates the meta lookup table and runs the regular hooks, but this is slow on large stores. You can select the other option to perform a lightweight cache purge and meta lookup update, which is 10x faster.', 'vg_sheet_editor' ),
						'default' => '',
					),
				),
			);
			return $sections;
		}

		function get_duplicate_skus_sql( $sql, $column, $post_type, $raw_form_data, $column_settings, $query ) {
			global $wpdb;
			if ( $post_type !== $this->post_type || $column !== '_sku' ) {
				return $sql;
			}

			$main_sql      = str_replace( array( "SQL_CALC_FOUND_ROWS  $wpdb->posts.*", 'SQL_CALC_FOUND_ROWS' ), array( "$wpdb->posts.ID", '' ), substr( $query->request, 0, strripos( $query->request, 'ORDER BY' ) ) );
			$get_items_sql = "SELECT meta_value 'value', count(meta_value) 'count', GROUP_CONCAT(post_id SEPARATOR ',') as post_ids  FROM $wpdb->postmeta pm WHERE post_id IN ($main_sql) AND meta_key = '_sku' AND meta_value <> '' GROUP BY meta_value having count(*) >= 2";

			return $get_items_sql;
		}

		/**
		 * The custom columns module finds all the meta keys and registers columns for them.
		 * In this case we remove the "serialized fields" from the list because we already register
		 * special columns for them.
		 *
		 * @param array $columns
		 * @param string $post_type
		 * @return array
		 */
		function disable_serialized_keys_from_automatic_columns( $columns, $post_type ) {
			if ( $post_type === $this->post_type ) {
				$disallowed_keys = array( '_crosssell_ids', '_upsell_ids', '_product_attributes', '_downloadable_files' );
				$columns         = array_diff( $columns, $disallowed_keys );
			}
			return $columns;
		}

		function register_filters( $filters, $post_type ) {

			if ( $post_type === $this->post_type && isset( $filters['post_parent'] ) ) {
				unset( $filters['post_parent'] );
			}
			return $filters;
		}

		function watch_cells_to_lock( $data, $post_type ) {
			if ( $post_type === $this->post_type ) {
				$data['watch_cells_to_lock'] = true;
				if ( empty( $data['export_keys_mapping'] ) ) {
					$data['export_keys_mapping'] = array();
				}
				$data['export_keys_mapping']           = array_merge( $data['export_keys_mapping'], $this->core_to_woo_importer_columns_list );
				$data['wc_repeatable_columns']         = array(
					/* translators: %d: Attribute number */
					__( 'Attribute %d name', 'woocommerce' ) => esc_html__( 'Attribute name', 'woocommerce' ),
					/* translators: %d: Attribute number */
					__( 'Attribute %d value(s)', 'woocommerce' ) => esc_html__( 'Attribute value(s)', 'woocommerce' ),
					/* translators: %d: Attribute number */
					__( 'Attribute %d visible', 'woocommerce' ) => esc_html__( 'Attribute visibility', 'woocommerce' ),
					/* translators: %d: Attribute number */
					__( 'Attribute %d global', 'woocommerce' ) => esc_html__( 'Is a global attribute?', 'woocommerce' ),
					/* translators: %d: Attribute number */
					__( 'Attribute %d default', 'woocommerce' ) => esc_html__( 'Default attribute', 'woocommerce' ),
					/* translators: %d: Download number */
					__( 'Download %d name', 'woocommerce' ) => esc_html__( 'Download name', 'woocommerce' ),
					/* translators: %d: Download number */
					__( 'Download %d URL', 'woocommerce' ) => esc_html__( 'Download URL', 'woocommerce' ),
					/* translators: %d: Meta number */
					__( 'Meta: %s', 'woocommerce' )        => esc_html__( 'Import as meta data', 'woocommerce' ),
					__( 'Tags', 'woocommerce' )            => esc_html__( 'Tags (comma separated)', 'woocommerce' ),
				);
				$data['wc_repeatable_columns_english'] = array(
					/* translators: %d: Attribute number */
					'Attribute %d name'     => esc_html__( 'Attribute name', 'woocommerce' ),
					/* translators: %d: Attribute number */
					'Attribute %d value(s)' => esc_html__( 'Attribute value(s)', 'woocommerce' ),
					/* translators: %d: Attribute number */
					'Attribute %d visible'  => esc_html__( 'Attribute visibility', 'woocommerce' ),
					/* translators: %d: Attribute number */
					'Attribute %d global'   => esc_html__( 'Is a global attribute?', 'woocommerce' ),
					/* translators: %d: Attribute number */
					'Attribute %d default'  => esc_html__( 'Default attribute', 'woocommerce' ),
					/* translators: %d: Download number */
					'Download %d name'      => esc_html__( 'Download name', 'woocommerce' ),
					/* translators: %d: Download number */
					'Download %d URL'       => esc_html__( 'Download URL', 'woocommerce' ),
					/* translators: %d: Meta number */
					'Meta: %s'              => esc_html__( 'Import as meta data', 'woocommerce' ),
					'Tags'                  => esc_html__( 'Tags (comma separated)', 'woocommerce' ),
				);

				// Remove numbers from attribute columns so the saved mappings work
				if ( ! empty( $data['import_saved_column_mappings'] ) ) {
					$all_column_mappings = json_encode( $data['import_saved_column_mappings'] );
					if ( strpos( $all_column_mappings, 'attributes:' ) !== false ) {
						foreach ( $data['import_saved_column_mappings'] as $csv_name => $import_name ) {
							if ( strpos( $import_name, 'attributes:' ) === 0 ) {
								$data['import_saved_column_mappings'][ $csv_name ] = preg_replace( '/[0-9]+/', '', $import_name );
							}
						}
					}
				}
			}
			return $data;
		}

		function disable_wc_private_columns( $blacklisted_columns, $provider ) {
			if ( $provider === $this->post_type ) {
				$blacklisted_columns = array_merge(
					$blacklisted_columns,
					array(
						'_max_price_variation_id',
						'_max_regular_price_variation_id',
						'_max_sale_price_variation_id',
						'_max_variation_price',
						'_max_variation_regular_price',
						'_max_variation_sale_price',
						'_min_price_variation_id',
						'_min_regular_price_variation_id',
						'_min_sale_price_variation_id',
						'_min_variation_price',
						'_min_variation_regular_price',
						'_min_variation_sale_price',
						'^_price$',
						// WPML WC Multilingual saves the price of each currency like _price_USD
						'^_price_[A-Z]{3}$',
						'^_visibility$',
						'_wc_attachment_source',
						'_product_version',
					)
				);
			}
			return $blacklisted_columns;
		}

		function disallow_formula_sql_execution_on_special_columns( $allowed, $formula, $column, $post_type ) {
			if ( $post_type !== $this->post_type ) {
				return $allowed;
			}
			$disallowed = array();
			// When we change post type from product to variation, we need to
			// migrate data so we're forced to use the slow formulas
			$disallowed[] = 'post_type';

			if ( in_array( $column['key'], $disallowed ) ) {
				$allowed = false;
			}
			return $allowed;
		}

		function get_product_type( $product_id ) {
			$types = wp_get_object_terms(
				$product_id,
				'product_type',
				array(
					'update_term_meta_cache' => false,
					'fields'                 => 'slugs',
				)
			);
			return implode( ',', $types );
		}

		function add_inventory_totals_holder_to_console( $items, $post_type ) {
			if ( $post_type !== $this->post_type || ! empty( VGSE()->options['be_disable_woocommerce_inventory_stats'] ) ) {
				return $items;
			}
			// Auto disable the inventory calculations if the store has > 50k products
			if ( VGSE()->helpers->get_current_provider()->get_total( $post_type ) > 50000 ) {
				return $items;
			}
			$items['wc-inventory-totals'] = array(
				'label' => esc_html__( 'Inventory', 'vg_sheet_editor' ),
				'value' => 0,
			);
			return $items;
		}

		function calculate_inventory_totals( $data, $qry ) {
			global $wpdb;
			if ( $qry['post_type'] !== $this->post_type || ! empty( VGSE()->options['be_disable_woocommerce_inventory_stats'] ) ) {
				return $data;
			}
			// Auto disable the inventory calculations if the store has > 50k products
			if ( VGSE()->helpers->get_current_provider()->get_total( $this->post_type ) > 50000 ) {
				return $data;
			}

			// We use custom queries for performance reasons.

			$main_query_sql        = $GLOBALS['wpse_main_query']->request;
			$main_products_ids_sql = str_replace(
				array(
					'SQL_CALC_FOUND_ROWS',
					"$wpdb->posts.*",
				),
				array(
					'',
					"$wpdb->posts.ID",
				),
				$main_query_sql
			);
			$main_products_ids_sql = substr( $main_products_ids_sql, 0, strpos( $main_products_ids_sql, 'ORDER BY ' ) );
			if ( empty( $main_products_ids_sql ) ) {
				return $data;
			}
			$variable_products_ids_sql = "SELECT ID FROM $wpdb->posts WHERE post_parent IN (" . $main_products_ids_sql . ')';

			$meta_table_name = VGSE()->helpers->get_current_provider()->get_meta_table_name( $this->post_type );

			$main_products_sql     = "SELECT SUM(m1.meta_value) as stock, SUM(m1.meta_value * m2.meta_value) as price FROM $meta_table_name as m1 JOIN $meta_table_name as m2 ON m1.post_id = m2.post_id WHERE m1.meta_key = '_stock' AND m2.meta_key = '_regular_price' AND m1.post_id IN (" . $main_products_ids_sql . ') ';
			$variable_products_sql = "SELECT SUM(m1.meta_value) as stock, SUM(m1.meta_value * m2.meta_value) as price FROM $meta_table_name as m1 JOIN $meta_table_name as m2 ON m1.post_id = m2.post_id  WHERE m1.meta_key = '_stock' AND m2.meta_key = '_regular_price' AND m1.post_id IN (" . $variable_products_ids_sql . ') ';

			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$main_products_results     = $wpdb->get_row( $main_products_sql, ARRAY_A );
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$variable_products_results = $wpdb->get_row( $variable_products_sql, ARRAY_A );

			$total_units           = $main_products_results['stock'] + $variable_products_results['stock'];
			$total_inventory_price = $main_products_results['price'] + $variable_products_results['price'];

			$data['total_inventory_units'] = $total_units;
			$data['total_inventory_price'] = wc_price( $total_inventory_price );

			return $data;
		}

		function enqueue_assets() {
			$current_post = VGSE()->helpers->get_provider_from_query_string();

			if ( $current_post !== $this->post_type ) {
				return;
			}

			wp_enqueue_script( 'wp-sheet-editor-wc-attributes', plugins_url( '/assets/js/init.js', VGSE_WC_FILE ), array( 'jquery' ), filemtime( __DIR__ . '/assets/js/init.js' ) );
			wp_localize_script(
				'wp-sheet-editor-wc-attributes',
				'vgse_wc_attr_data',
				array(
					'texts' => array(
						'variations_on_reload_needed'  => esc_html__( 'We need to reload the spreadsheet rows to load the variations. Please save your changes first or you will lose those changes. Do you want to reload now?', 'vg_sheet_editor' ),
						'variations_off_reload_needed' => esc_html__( 'We need to reload the Spreadsheet to remove the variations. Please save your changes first or you will lose those changes. Do you want to reload now?', 'vg_sheet_editor' ),
					),
				)
			);
		}

		/**
		 * Ejemplo de uso: $this->update_products_with_api( $this->convert_row_to_api_format( $rows ) );
		 */
		function update_products_with_api( $product, $version = 3 ) {
			$product = apply_filters( 'vg_sheet_editor/woocommerce/wc_rest_api_product_args', $product, $version );
			if ( isset( $product['ID'] ) ) {
				$out = VGSE()->helpers->create_rest_request( 'PUT', '/wc/v' . $version . '/products/' . $product['ID'], $product );
			} else {
				$out = VGSE()->helpers->create_rest_request( 'POST', '/wc/v' . $version . '/products', $product );
			}
			return $out;
		}

		/**
		 * Allow woocomerce product post type
		 * @param array $post_types
		 * @return array
		 */
		function allow_product_post_type( $post_types ) {

			if ( ! isset( $post_types[ $this->post_type ] ) ) {
				$post_types[ $this->post_type ] = VGSE()->helpers->get_post_type_label( $this->post_type );
			}
			return $post_types;
		}

		/**
		 * Modify spreadsheet columns settings.
		 *
		 * It changes the names and settings of some columns.
		 * @param array $spreadsheet_columns
		 * @param string $post_type
		 * @param bool $exclude_formatted_settings
		 * @return array
		 */
		function filter_columns_settings( $spreadsheet_columns ) {

			if ( ! isset( $spreadsheet_columns[ $this->post_type ] ) ) {
				return $spreadsheet_columns;
			}

			if ( ! empty( $spreadsheet_columns[ $this->post_type ]['post_excerpt'] ) ) {
				$spreadsheet_columns[ $this->post_type ]['post_excerpt']['title']                 = esc_html__( 'Short description', 'woocommerce' );
				$spreadsheet_columns[ $this->post_type ]['post_excerpt']['formatted']['renderer'] = 'wp_tinymce';
			}
			if ( ! empty( $spreadsheet_columns[ $this->post_type ]['comment_status'] ) ) {
				$spreadsheet_columns[ $this->post_type ]['comment_status']['title'] = esc_html__( 'Enable reviews', 'woocommerce' );
			}

			return $spreadsheet_columns;
		}

		/**
		 * Create new products using WC API
		 * @param array $post_ids
		 * @param str $post_type
		 * @param int $number
		 * @return array Post ids
		 */
		public function create_new_products( $post_ids, $post_type, $number ) {

			if ( $post_type !== $this->post_type || ! empty( $post_ids ) ) {
				return $post_ids;
			}

			for ( $i = 0; $i < $number; $i++ ) {
				$api_response = $this->update_products_with_api(
					array(
						'name'   => esc_html__( '...', 'vg_sheet_editor' ),
						'status' => 'draft',
					),
					3
				);

				if ( $api_response->status === 200 || $api_response->status === 201 ) {
					$api_data   = $api_response->get_data();
					$post_ids[] = $api_data['id'];
				}
			}

			return $post_ids;
		}

		/**
		 * Register spreadsheet columns
		 */
		function register_columns( $editor ) {
			$post_type = $this->post_type;

			if ( ! in_array( $post_type, $editor->args['enabled_post_types'] ) ) {
				return;
			}

			$editor->args['columns']->register_item(
				'post_author',
				$post_type,
				array(
					'data_type'         => 'post_data',
					'column_width'      => 120,
					'title'             => esc_html__( 'Vendor', 'vg_sheet_editor' ),
					'type'              => '',
					'supports_formulas' => true,
					'formatted'         => array(
						'type'   => 'autocomplete',
						'source' => 'searchUsers',
					),
					'allow_to_rename'   => true,
				)
			);

			$product_type_tax = 'product_type';
			$editor->args['columns']->register_item(
				$product_type_tax,
				$post_type,
				array(
					'data_type'         => 'post_terms',
					'column_width'      => 150,
					'title'             => esc_html__( 'Type', 'woocommerce' ),
					'supports_formulas' => true,
					'formatted'         => array(
						'data'          => $product_type_tax,
						'editor'        => 'select',
						'selectOptions' => VGSE()->data_helpers->get_taxonomy_terms( $product_type_tax ),
					),
				)
			);
			$editor->args['columns']->register_item(
				'_product_image_gallery',
				$post_type,
				array(
					'data_type'         => 'meta_data',
					'column_width'      => 300,
					'supports_formulas' => true,
					'title'             => esc_html__( 'Product gallery', 'woocommerce' ),
					'type'              => 'boton_gallery_multiple',
				)
			);
			$editor->args['columns']->register_item(
				'_sku',
				$post_type,
				array(
					'data_type'             => 'meta_data',
					'column_width'          => 150,
					'title'                 => esc_html__( 'SKU', 'woocommerce' ),
					'supports_formulas'     => true,
					// We must use the slow execution method to sync with the lookup table
					'supports_sql_formulas' => false,
				)
			);

			$editor->args['columns']->register_item(
				'_regular_price',
				$post_type,
				array(
					'data_type'             => 'meta_data',
					'column_width'          => 100,
					'title'                 => esc_html__( 'Regular price', 'woocommerce' ),
					'supports_formulas'     => true,
					'value_type'            => 'number',
					// We must use the slow execution method to sync with the lookup table
					'supports_sql_formulas' => false,
				)
			);

			$editor->args['columns']->register_item(
				'_sale_price',
				$post_type,
				array(
					'value_type'            => 'number',
					'data_type'             => 'meta_data',
					'column_width'          => 100,
					'title'                 => esc_html__( 'Sale price', 'woocommerce' ),
					'supports_formulas'     => true,
					// We must use the slow execution method to sync with the lookup table
					'supports_sql_formulas' => false,
				)
			);

			$editor->args['columns']->register_item(
				'_sale_price_dates_from',
				$post_type,
				array(
					'data_type'                 => 'meta_data',
					'column_width'              => 150,
					'title'                     => esc_html__( 'Sale start date', 'woocommerce' ),
					'supports_formulas'         => true,
					'formatted'                 => array(
						'data'                 => '_sale_price_dates_from',
						'type'                 => 'date',
						'dateFormatPhp'        => 'Y-m-d',
						'customDatabaseFormat' => 'Y-m-d',
						'correctFormat'        => true,
						'defaultDate'          => '',
						'datePickerConfig'     => array(
							'firstDay'       => 0,
							'showWeekNumber' => true,
							'numberOfMonths' => 1,
						),
					),
					// We must use the slow execution method to sync with the lookup table
					'supports_sql_formulas'     => false,
					'save_value_callback'       => array( WPSE_WC_Products_Data_Formatting_Obj(), 'save_sale_date' ),
					'prepare_value_for_display' => array( WPSE_WC_Products_Data_Formatting_Obj(), 'prepare_sale_dates_for_display' ),
				)
			);

			$editor->args['columns']->register_item(
				'_sale_price_dates_to',
				$post_type,
				array(
					'data_type'                 => 'meta_data',
					'column_width'              => 150,
					'title'                     => esc_html__( 'Sale end date', 'woocommerce' ),
					'supports_formulas'         => true,
					'formatted'                 => array(
						'data'                 => '_sale_price_dates_to',
						'type'                 => 'date',
						'dateFormatPhp'        => 'Y-m-d',
						'customDatabaseFormat' => 'Y-m-d',
						'correctFormat'        => true,
						'defaultDate'          => '',
						'datePickerConfig'     => array(
							'firstDay'       => 0,
							'showWeekNumber' => true,
							'numberOfMonths' => 1,
						),
					),
					// We must use the slow execution method to sync with the lookup table
					'supports_sql_formulas'     => false,
					'save_value_callback'       => array( WPSE_WC_Products_Data_Formatting_Obj(), 'save_sale_date' ),
					'prepare_value_for_display' => array( WPSE_WC_Products_Data_Formatting_Obj(), 'prepare_sale_dates_for_display' ),
				)
			);
			$editor->args['columns']->register_item(
				'_manage_stock',
				$post_type,
				array(
					'data_type'         => 'meta_data',
					'column_width'      => 150,
					'title'             => esc_html__( 'Manage stock', 'woocommerce' ),
					'supports_formulas' => true,
					'formatted'         => array(
						'data'              => '_manage_stock',
						'type'              => 'checkbox',
						'checkedTemplate'   => 'yes',
						'uncheckedTemplate' => 'no',
					),
					'default_value'     => 'no',
				)
			);

			$stock_statuses = wc_get_product_stock_status_options();

			$editor->args['columns']->register_item(
				'_stock_status',
				$post_type,
				array(
					'data_type'             => 'meta_data',
					'column_width'          => 150,
					'title'                 => esc_html__( 'Stock status', 'woocommerce' ),
					'supports_formulas'     => true,
					'formatted'             => array(
						'editor'        => 'select',
						'selectOptions' => $stock_statuses,
					),
					// We must use the slow execution method to sync with the lookup table
					'supports_sql_formulas' => false,
				)
			);

			$stock_args = array(
				'data_type'             => 'meta_data',
				'column_width'          => 75,
				'title'                 => esc_html__( 'Stock', 'woocommerce' ),
				'supports_formulas'     => true,
				// We must use the slow execution method to sync with the lookup table
				'supports_sql_formulas' => false,
			);

			if ( ! empty( VGSE()->options['wc_allow_other_plugins_to_filter_stock'] ) ) {
				$stock_args['save_value_callback']       = array( $this, 'save_stock_value' );
				$stock_args['prepare_value_for_display'] = array( $this, 'prepare_stock_value_for_display' );
			}

			$editor->args['columns']->register_item(
				'_stock',
				$post_type,
				$stock_args
			);

			$editor->args['columns']->register_item(
				'_weight',
				$post_type,
				array(
					'data_type'         => 'meta_data',
					'column_width'      => 100,
					'title'             => esc_html__( 'Weight', 'woocommerce' ),
					'supports_formulas' => true,
				)
			);

			$editor->args['columns']->register_item(
				'_width',
				$post_type,
				array(
					'data_type'         => 'meta_data',
					'column_width'      => 100,
					'title'             => esc_html__( 'Width', 'woocommerce' ),
					'supports_formulas' => true,
				)
			);

			$editor->args['columns']->register_item(
				'_height',
				$post_type,
				array(
					'data_type'         => 'meta_data',
					'column_width'      => 100,
					'title'             => esc_html__( 'Height', 'woocommerce' ),
					'supports_formulas' => true,
				)
			);

			$editor->args['columns']->register_item(
				'_length',
				$post_type,
				array(
					'data_type'         => 'meta_data',
					'column_width'      => 100,
					'title'             => esc_html__( 'Length', 'woocommerce' ),
					'supports_formulas' => true,
				)
			);
			$editor->args['columns']->register_item(
				'_crosssell_ids',
				$post_type,
				array(
					'data_type'                  => 'meta_data',
					'column_width'               => 150,
					'title'                      => esc_html__( 'Cross-sells', 'woocommerce' ),
					'supports_formulas'          => true,
					'prepare_value_for_display'  => array( WPSE_WC_Products_Data_Formatting_Obj(), 'prepare_linked_product_value_for_display' ),
					'prepare_value_for_database' => array( WPSE_WC_Products_Data_Formatting_Obj(), 'prepare_linked_product_value_for_database' ),
					'list_separation_character'  => ',',
					'supports_sql_formulas'      => false,
					'formatted'                  => array(
						'comment' => array( 'value' => esc_html__( 'Enter multiple SKUs or IDs separated by commas', 'vg_sheet_editor' ) ),
					),
				)
			);
			$editor->args['columns']->register_item(
				'_upsell_ids',
				$post_type,
				array(
					'data_type'                  => 'meta_data',
					'column_width'               => 150,
					'title'                      => esc_html__( 'Upsells', 'woocommerce' ),
					'supports_formulas'          => true,
					'prepare_value_for_display'  => array( WPSE_WC_Products_Data_Formatting_Obj(), 'prepare_linked_product_value_for_display' ),
					'prepare_value_for_database' => array( WPSE_WC_Products_Data_Formatting_Obj(), 'prepare_linked_product_value_for_database' ),
					'list_separation_character'  => ',',
					'supports_sql_formulas'      => false,
					'formatted'                  => array(
						'comment' => array( 'value' => esc_html__( 'Enter multiple SKUs or IDs separated by commas', 'vg_sheet_editor' ) ),
					),
				)
			);

			$visibility_taxonomy = 'product_visibility';
			$editor->args['columns']->register_item(
				$visibility_taxonomy,
				$post_type,
				array(
					'data_type'         => 'post_terms',
					'column_width'      => 150,
					'title'             => esc_html__( 'Visibility', 'woocommerce' ),
					'supports_formulas' => true,
					'formatted'         => array(
						'data'   => $visibility_taxonomy,
						'type'   => 'autocomplete',
						'source' => 'loadTaxonomyTerms',
					),
				)
			);

			$editor->args['columns']->register_item(
				'_virtual',
				$post_type,
				array(
					'data_type'             => 'meta_data',
					'column_width'          => 150,
					'title'                 => esc_html__( 'Virtual', 'woocommerce' ),
					'supports_formulas'     => true,
					'formatted'             => array(
						'data'              => '_virtual',
						'type'              => 'checkbox',
						'checkedTemplate'   => 'yes',
						'uncheckedTemplate' => 'no',
					),
					'default_value'         => 'no',
					// We must use the slow execution method to sync with the lookup table
					'supports_sql_formulas' => false,
				)
			);
			$editor->args['columns']->register_item(
				'_sold_individually',
				$post_type,
				array(
					'data_type'         => 'meta_data',
					'column_width'      => 150,
					'title'             => esc_html__( 'Sold individually', 'woocommerce' ),
					'supports_formulas' => true,
					'formatted'         => array(
						'data'              => '_sold_individually',
						'type'              => 'checkbox',
						'checkedTemplate'   => 'yes',
						'uncheckedTemplate' => 'no',
					),
					'default_value'     => 'no',
				)
			);
			$editor->args['columns']->register_item(
				'_featured',
				$post_type,
				array(
					'data_type'                 => 'meta_data',
					'column_width'              => 150,
					'title'                     => esc_html__( 'Is featured?', 'woocommerce' ),
					'supports_formulas'         => true,
					'formatted'                 => array(
						'data'              => '_featured',
						'type'              => 'checkbox',
						'checkedTemplate'   => 'featured',
						'uncheckedTemplate' => 'no',
					),
					'prepare_value_for_display' => array( WPSE_WC_Products_Data_Formatting_Obj(), 'prepare_featured_value_for_display' ),
					'default_value'             => 'no',
				)
			);
			$editor->args['columns']->register_item(
				'_backorders',
				$post_type,
				array(
					'data_type'         => 'meta_data',
					'column_width'      => 150,
					'title'             => esc_html__( 'Backorders allowed?', 'woocommerce' ),
					'supports_formulas' => true,
					'formatted'         => array(
						'data'          => '_backorders',
						'editor'        => 'select',
						'selectOptions' => array(
							'no'     => esc_html__( 'Do not allow', 'woocommerce' ),
							'notify' => esc_html__( 'Allow, but notify customer', 'woocommerce' ),
							'yes'    => esc_html__( 'Allow', 'woocommerce' ),
						),
					),
					'default_value'     => 'no',
				)
			);

			$editor->args['columns']->register_item(
				'_purchase_note',
				$post_type,
				array(
					'data_type'         => 'meta_data',
					'column_width'      => 250,
					'title'             => esc_html__( 'Purchase note', 'woocommerce' ),
					'supports_formulas' => true,
				)
			);

			$shipping_tax_name = 'product_shipping_class';
			$editor->args['columns']->register_item(
				$shipping_tax_name,
				$post_type,
				array(
					'data_type'         => 'post_terms',
					'column_width'      => 150,
					'title'             => esc_html__( 'Shipping class', 'woocommerce' ),
					'supports_formulas' => true,
					'formatted'         => array(
						'data'   => $shipping_tax_name,
						'type'   => 'autocomplete',
						'source' => 'loadTaxonomyTerms',
					),
				)
			);

			$editor->args['columns']->register_item(
				'_wc_average_rating',
				$post_type,
				array(
					'data_type'     => 'meta_data',
					'title'         => esc_html__( 'Average rating', 'woocommerce' ),
					'allow_to_save' => false,
					'is_locked'     => true,
				)
			);
			$editor->args['columns']->register_item(
				'_wc_review_count',
				$post_type,
				array(
					'data_type'     => 'meta_data',
					'title'         => esc_html__( 'Review count', 'woocommerce' ),
					'allow_to_save' => false,
					'is_locked'     => true,
				)
			);
			$editor->args['columns']->register_item(
				'total_sales',
				$post_type,
				array(
					'data_type'          => 'meta_data',
					'title'              => esc_html__( 'Total sales', 'woocommerce' ),
					'allow_to_save'      => true,
					'is_locked'          => true,
					'lock_template_key'  => 'enable_lock_cell_template',
					'get_value_callback' => array( $this, 'get_total_sales' ),
					'formatted'          => array(
						'comment' => array( 'value' => esc_html__( 'These numbers are cached for 1 day for performance reasons. You can click on "Settings > Scan DB" to update the cache.', 'vg_sheet_editor' ) ),
					),
				)
			);
		}
		function get_total_sales( $post, $cell_key, $cell_args ) {
			global $wpdb;

			if ( isset( $this->total_sales_cache[ $post->ID ] ) ) {
				return $this->total_sales_cache[ $post->ID ];
			}

			$value = 0;
			if ( $post->post_type === 'product' ) {
				$value = (int) $wpdb->get_var( $wpdb->prepare( "SELECT SUM(product_qty) FROM {$wpdb->prefix}wc_order_product_lookup WHERE product_id = %d", $post->ID ) );
			} elseif ( $post->post_type === 'product_variation' ) {
				$value = (int) $wpdb->get_var( $wpdb->prepare( "SELECT SUM(product_qty) FROM {$wpdb->prefix}wc_order_product_lookup WHERE variation_id = %d", $post->ID ) );
			}

			$this->total_sales_cache[ $post->ID ] = $value;

			return $value;
		}

		public function get_total_sales_version() {
			if ( null !== $this->total_sales_version ) {
				return $this->total_sales_version;
			}

			$version = get_transient( 'vgse_total_sales_cache_version' );
			$rescan_requested = false;
			if ( isset( VGSE()->helpers ) && method_exists( VGSE()->helpers, 'can_rescan_db_fields' ) ) {
				$rescan_requested = VGSE()->helpers->can_rescan_db_fields( 'product' );
			}

			if ( ! $version || $rescan_requested ) {
				$version = time();
				set_transient( 'vgse_total_sales_cache_version', $version, DAY_IN_SECONDS );
			}

			$this->total_sales_version = $version;
			return $this->total_sales_version;
		}

		public function preload_total_sales( $data, $posts, $wp_query_args, $settings, $spreadsheet_columns ) {
			global $wpdb;

			if ( empty( $posts ) || ! isset( $spreadsheet_columns['total_sales'] ) ) {
				return $data;
			}

			$post_ids = wp_list_pluck( $posts, 'ID' );
			sort( $post_ids );
			$hash = md5( implode( ',', $post_ids ) );

			$version       = $this->get_total_sales_version();
			$transient_key = "vgse_total_sales_{$version}_{$hash}";

			$cached_sales = get_transient( $transient_key );

			if ( is_array( $cached_sales ) ) {
				foreach ( $cached_sales as $id => $val ) {
					$this->total_sales_cache[ $id ] = $val;
				}
				return $data;
			}

			$product_ids_to_query   = array();
			$variation_ids_to_query = array();
			$sales_to_cache         = array();

			foreach ( $posts as $post ) {
				if ( $post->post_type === 'product' ) {
					$product_ids_to_query[] = $post->ID;
				} elseif ( $post->post_type === 'product_variation' ) {
					$variation_ids_to_query[] = $post->ID;
				}
				$sales_to_cache[ $post->ID ] = 0;
			}

			if ( ! empty( $product_ids_to_query ) ) {
				$placeholders = implode( ', ', array_fill( 0, count( $product_ids_to_query ), '%d' ) );
				$sql = $wpdb->prepare(
					"SELECT product_id, SUM(product_qty) as total_sales
					   FROM {$wpdb->prefix}wc_order_product_lookup
					  WHERE product_id IN ({$placeholders})
				   GROUP BY product_id",
					$product_ids_to_query
				);
				$results = $wpdb->get_results( $sql, ARRAY_A );
				if ( $results ) {
					foreach ( $results as $row ) {
						$sales_to_cache[ (int) $row['product_id'] ] = (int) $row['total_sales'];
					}
				}
			}

			if ( ! empty( $variation_ids_to_query ) ) {
				$placeholders = implode( ', ', array_fill( 0, count( $variation_ids_to_query ), '%d' ) );
				$sql = $wpdb->prepare(
					"SELECT variation_id, SUM(product_qty) as total_sales
					   FROM {$wpdb->prefix}wc_order_product_lookup
					  WHERE variation_id IN ({$placeholders})
				   GROUP BY variation_id",
					$variation_ids_to_query
				);
				$results = $wpdb->get_results( $sql, ARRAY_A );
				if ( $results ) {
					foreach ( $results as $row ) {
						$sales_to_cache[ (int) $row['variation_id'] ] = (int) $row['total_sales'];
					}
				}
			}

			set_transient( $transient_key, $sales_to_cache, DAY_IN_SECONDS );

			foreach ( $sales_to_cache as $id => $val ) {
				$this->total_sales_cache[ $id ] = $val;
			}

			return $data;
		}

		public function save_stock_value( $post_id, $key, $value, $post_type, $column_settings, $spreadsheet_columns ) {
			$product = wc_get_product( $post_id );
			if ( $product ) {
				$qty = $value;
				if ( $qty !== '' && $qty !== null ) {
					update_post_meta( $post_id, '_manage_stock', 'yes' );
				}
				wc_update_product_stock( $product, $qty, 'set' );
			}
		}

		public function prepare_stock_value_for_display( $value, $post, $key, $column_settings ) {
			$post_id = is_object( $post ) ? $post->ID : ( is_array( $post ) ? $post['ID'] : $post );
			$product = wc_get_product( $post_id );
			if ( $product ) {
				if ( $product->is_type( 'variation' ) ) {
					$value = apply_filters( 'woocommerce_product_variation_get_stock_quantity', $value, $product );
				} else {
					$value = apply_filters( 'woocommerce_product_get_stock_quantity', $value, $product );
				}
			}
			return $value;
		}

		function __set( $name, $value ) {
			$this->$name = $value;
		}

		function __get( $name ) {
			return $this->$name;
		}

	}

	add_action( 'vg_sheet_editor/initialized', 'vgse_woocommerce_init' );

	function vgse_woocommerce_init() {
		WP_Sheet_Editor_WooCommerce::get_instance();
		VGSE()->WC = WP_Sheet_Editor_WooCommerce::get_instance();
	}
}
