<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title></title>
	<style>
	table, th, td {
	  border: 1px solid black;
	  border-collapse: collapse;
	}
	th, td {
	  padding: 5px;
	  text-align: left;
	  font-family: Verdana,sans-serif;
	  font-size: 12px;
	}
</style>
</head>
<body>
	<table><?php
		if( sizeof( $order_ids ) > 0 ) {
			$group_by_categories = array();
			$products_count = 0;
			foreach ($order_ids as $order_id) {
				$order = wc_get_order( $order_id );
				foreach ( $order->get_items() as $item_id => $item ) 
				{
					$product = $item->get_product();
					$product->item = $item;
					$categories = wp_get_post_terms($product->id, 'product_cat');
					if(is_array($categories)) 
					{
						$slugs = '';
						foreach($categories as $category) $slugs.=$category->slug;
						$group_by_categories[$slugs][$product->id][]=$product;
						$products_count+=$item->get_quantity();
					}
					ksort($group_by_categories);
				}
			}
			?>
			<tr>
				<td style="width:100%" colspan=3><strong>Products: <?php echo $products_count?></strong></td>
			</tr>
			<?php
			foreach ( $group_by_categories as $item_id => $groupproducts ) 
			{
				 foreach ( $groupproducts as $products) 
				 {
					 foreach ( $products as $product) 
					 {?> 
						<tr>
						<td style="width:65px"><?php 
						    if($img = wp_get_attachment_image_src($product->get_image_id()))
							{?>
								<img src="<?php echo $img[0]?>" width="65">					
							<?php
							}
							?>
						</td>
						<td style="width:400px">
							<?php echo $product->item->get_name(); ?>
							
						</td>

						<td style="width:40px;text-align: center;">
							<strong><?php echo $product->item->get_quantity();?></strong>
						</td>
					</tr><?php
					}
				 }
			}
		}
		?>
	</table>
	<div id="footer">
		<div class="page-number"></div>
	</div>
	<script type="text/php">
    if (isset($pdf)) {
        $text = "page {PAGE_NUM} / {PAGE_COUNT}";
        $size = 8;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 2;
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>
</body>
</html>