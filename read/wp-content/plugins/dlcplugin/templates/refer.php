<?php
$beoneofus     = get_page_by_title( 'Be One of Us', '', 'page' );
$title         = get_page_by_title( 'Lifestyle Shop', '', 'page' );
$url_products  = get_permalink( $title->ID );
?>

<p><iframe src="https://www.youtube.com/embed/XfecVverhHw?rel=0&amp;autoplay=1" width="800" height="450" frameborder="0" allowfullscreen="allowfullscreen"></iframe></p>

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link" href="<?php echo $url_products; ?>">BUY PRODUCTS</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:paragraph -->
<p>Choose from our wide selection of <em>safe, natural, and eco-friendly products</em></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link" href="<?php echo get_permalink( $beoneofus->ID );?>">BE ONE OF US</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:paragraph -->
<p>Become one of our amazing distributors and <em>Turn Your Expenses into Income</em></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link" href="<?php echo get_permalink( $beoneofus->ID) . '?pcmstore=1';?>">OPEN MY STORE</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:paragraph -->
<p>Don't know what to do with your extra money? Open your <em>very own DLC store in your area</em></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

