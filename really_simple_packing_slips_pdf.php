<?php

/*  
* Plugin Name: Really Simple Packing Slips PDF
* Description: Generate simple pdf packing slips
* Author: Jaan1234  
* Version: 1.0.1
* Tested up to: 5.6
* WC tested up to: 5.0
* License: GPL 2.0
* Text Domain: woocommerce
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Dompdf\Dompdf as Dompdf;

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    if ( ! class_exists( 'RSPL_Really_Simple_Packing_List' ) ) :
		class RSPL_Really_Simple_Packing_Slip {
			/**
			* Construct the plugin.
			*/
			public function __construct() 
			{
				define( 'RSPL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
				define( 'RSPL_TEMPLATE_DIR', 'templates/');
				/**
					* Date select filter only in Woo orders page
				 **/
				if (!is_admin() || !isset($_GET['post_type']) ||  sanitize_text_field($_GET['post_type']) !='shop_order')return false;
				add_action( 'plugins_loaded', array( $this, 'RSPL_init' ) );
				
			}
			/**
			* Initialize the plugin.
			*/
			public function RSPL_init()
			{	
				add_filter('bulk_actions-edit-shop_order', function($bulk_actions) 
				{
					$bulk_actions['get_simple_packing_slip'] = __('Simple packing slips', 'txtdomain');
					$bulk_actions['get_simple_packing_slip_group_by_order'] = __('Simple packing slips group by order', 'txtdomain');
					return $bulk_actions;
				});
				add_filter('handle_bulk_actions-edit-shop_order', array($this,'RSPL_generate_packing_slip'), 10, 3);
			}

			public static function RSPL_generate_packing_slip($redirect_url, $action, $order_ids) 
			{
				if($action == 'get_simple_packing_slip' || $action == 'get_simple_packing_slip_group_by_order')
				{
					include_once( RSPL_PLUGIN_DIR.'includes/dompdf/autoload.inc.php');
					ob_start();
					Switch($action)
					{
						case 'get_simple_packing_slip': include(RSPL_TEMPLATE_DIR.'template1.php'); break;
						case 'get_simple_packing_slip_group_by_order': include(RSPL_TEMPLATE_DIR.'template2.php'); break;
					}
					$html=ob_get_clean();
					$dompdf = new Dompdf();
					$dompdf->loadHtml($html);
					$dompdf->set_option('isRemoteEnabled', true);
					$dompdf->set_option("enable_php", true);
					$dompdf->setPaper('A4', 'portrait');
					$dompdf->render();
					$dompdf->stream( 'Simple_Packing_Slip.pdf', array("Attachment" => true));
					exit();
					//return $redirect_url;
				}
			}

		}
	
	$RSPL_Really_Simple_Packing_Slip  = new RSPL_Really_Simple_Packing_Slip( __FILE__ );
	endif;
}
?>