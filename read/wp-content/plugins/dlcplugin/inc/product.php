<?php
/**
*@package DLCPlugin
*/

class DLCPluginProducts
{
     public function __construct() {
          add_filter( 'init', array( $this, 'init_products') );
     }

     function register() {
          add_filter( 'the_title', array( $this, 'product_title_update'), 10, 2 );
          add_filter( 'the_content', array( $this, 'load_products') );
          add_filter( 'pre_wp_nav_menu', array( $this, 'product_remove_title_filter_nav_menu'), 10, 2 );
          add_filter( 'wp_nav_menu_items', array( $this, 'product_add_title_filter_non_menu'), 10, 2 );
     }

     function init_products( ) {
          wp_cache_set( 'shortlink', strpos($_SERVER['SERVER_NAME'],'local')!==false?'index.php/':'' );

          if ( isset($_GET['rfr']) ) {
               $_SESSION['rfr'] = $_GET['rfr'];
          }

          if ( isset($_GET['pid']) && $_GET['pid']!='' ) {
               $_SESSION['pid'] = $_GET['pid'];
               require_once plugin_dir_path( __DIR__ ) . 'templates/init_products.php';
          }
     }

     function load_products( $content ) {
          global $post;
          if ( is_page() ) {
               if ( $post->post_title == 'Cart' ) {
                    require_once plugin_dir_path( __DIR__ ) . 'templates/cart.php';

               } elseif ( $post->post_title == 'Products' ) {
                    require_once plugin_dir_path( __DIR__ ) . 'templates/product_list.php';

               } elseif ( isset($_GET['pid']) && $_GET['pid']!='' ) {
                    require_once plugin_dir_path( __DIR__ ) . 'templates/products.php';

               } else return $content;

          } else return $content;
     }

     //-----MOD POST TITLE---------------------
     function product_title_update( $title, $id = null ) {
          global $dlcuser;

          if ( ! is_admin() && ! is_null( $id ) ) {
               $post = get_post( $id );
               if ( is_page() && ( $post->post_title == 'Products' )) {
                    if( isset($dlcuser['product_name'] ) ) {
                         $new_titile = $dlcuser['product_name'];
                         if( ! empty( $new_titile ) ) {
                              return $new_titile;
                         }
                    }
               }
          }
          return $title;
     }

     function product_remove_title_filter_nav_menu( $nav_menu, $args ) {
         // we are working with menu, so remove the title filter
         remove_filter( 'the_title', array( $this, 'product_title_update'), 10, 2 );
         return $nav_menu;
     }// this filter fires just before the nav menu item creation process

     function product_add_title_filter_non_menu( $items, $args ) {
         // we are done working with menu, so add the title filter back
         add_filter( 'the_title', array( $this, 'product_title_update'), 10, 2 );
         return $items;
     }// this filter fires after nav menu item creation is done
     //-----MOD POST TITLE---------------------
}
