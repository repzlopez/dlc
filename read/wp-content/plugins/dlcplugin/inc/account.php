<?php
/**
*@package DLCPlugin
*/

class DLCPluginAccount
{
     function __construct() {
          add_action( 'init', array( $this, 'init_accounts' ) );
     }

     function register() {
          add_filter( 'the_title', array( $this, 'distributor_title_update'), 10, 2 );
          add_filter( 'pre_wp_nav_menu', array( $this, 'distributor_remove_title_filter_nav_menu'), 10, 2 );
          add_filter( 'wp_nav_menu_items', array( $this, 'distributor_add_title_filter_non_menu'), 10, 2 );
          add_filter( 'wp_nav_menu_items', array( $this, 'add_login_menu' ), 10, 2 );

          add_filter( 'the_content', array( $this, 'load_accounts') );

          add_action( 'init', array( $this, 'unset_accounts' ) );
     }

     function init_accounts() {
          global $dlcuser, $resetstatus;

          if ( isset($_SESSION['resetstatus']) ) {
               $resetstatus = $_SESSION['resetstatus'];
          }

          if ( isset($_SESSION['isLogged'])&&$_SESSION['isLogged'] ) {
               $dlcuser['id'] = $_SESSION['u_site'];
               $dlcuser['name'] = $_SESSION['u_name'];

               if( isset($_GET['otherid'] ) ) {
                    $dlcuser['id'] = $_GET['otherid'];
               }
          }
     }

     function unset_accounts() {
          unset($_SESSION['catch_login']);
          unset($_SESSION['resetstatus']);
     }

     function load_accounts( $content ) {
          global $post,$resetstatus;

          if ( is_page() ) {
               if ( $post->post_title == 'Account' ) {
                    require_once plugin_dir_path( __DIR__ ) . 'templates/accounts.php';
               } elseif ( $post->post_title == 'Profile' ) {
                    require_once plugin_dir_path( __DIR__ ) . 'templates/profile.php';
               } elseif ( $post->post_title == 'Welcome to DLC Philippines' ) {
                    require_once plugin_dir_path( __DIR__ ) . 'templates/refer.php';
               } elseif ( $post->post_title == 'Be One of Us' ) {
                    require_once plugin_dir_path( __DIR__ ) . 'templates/beoneofus.php';
               } elseif ( $post->post_title == 'Reseller Dashboard' ) {
                    require_once plugin_dir_path( __DIR__ ) . 'templates/reseller.php';
               } elseif ( $post->post_title == 'Registration' ) {
                    require_once plugin_dir_path( __DIR__ ) . 'templates/registration.php';
               } elseif ( $post->post_title == 'Forgot Password' ) {
                    $style = substr($resetstatus,0,1) == 'U' ? 'red' : 'green';
                    return $content . '<p style="color:' . $style . '">' . $resetstatus . '</p>';
               } else return $content;
          } else return $content;
     }

     function add_login_menu( $items, $args ) {
          global $dlcuser;
          if ( $args->theme_location == 'primary' ) {
               if ( isset($_SESSION['isLogged']) && $_SESSION['isLogged'] ) {

                    $account  = get_page_by_title( 'Account', '', 'page' );
                    $profile  = get_page_by_title( 'Profile', '', 'page' );
                    $profurl  = strpos(get_permalink($profile->ID),'?')!==false?'&':'?';
                    $cart     = get_page_by_title( 'Cart', '', 'page' );
                    $cartcls  = ( isset($_SESSION['shoplist']) ? '' : 'no_cart' );

                    $items .= '<li class="menu-item menu-item-has-children"><a href="'.get_permalink($account->ID).'">Account</a><span class="expand" role="button" tabindex="0"></span><ul class="sub-menu">';
                         $items .= '<li class="item"><a href="'.get_permalink($profile->ID).$profurl.'i='.$dlcuser['id'].'">Profile</a></li>';
                         $items .= '<li class="item"><a href="/distrilog/mypage.php">Dashboard</a></li>';
                         $items .= '<li class="item"><a href="/distrilog/myorders.php">Orders</a></li>';
                         $items .= '<li class="item '.$cartcls.'" id="cart_menu"><a href="'.get_permalink($cart->ID).'">Cart</a></li>';
                    $items .= '</ul></li>';

                    $items .= '<li class="item"><a href="/logout">Logout</a></li>';
               } elseif ( isset($_SESSION['rfr']) ) {
                    $items .= '<li class="item"><a href="/logout">Logout Guest</a></li>';
               } else {
                    $items .= '<li class="item"><a href="/login">Login</a></li>';
               }
          }
          return $items;
     }

     //-----MOD POST TITLE---------------------
     function distributor_title_update( $title, $id = null ) {
          global $dlcuser;
          if ( ! is_admin() && ! is_null( $id ) ) {
               $post = get_post( $id );
               if ( is_page() && ( $post->post_title == 'Account' || $post->post_title == 'Profile' )) {
                    $new_titile = $dlcuser['id'];
                    if( ! empty( $new_titile ) ) {
                         return $new_titile;
                    }
               }
          }
          return $title;
     }

     function distributor_remove_title_filter_nav_menu( $nav_menu, $args ) {
         // we are working with menu, so remove the title filter
         remove_filter( 'the_title', array( $this, 'distributor_title_update'), 10, 2 );
         return $nav_menu;
     }// this filter fires just before the nav menu item creation process

     function distributor_add_title_filter_non_menu( $items, $args ) {
         // we are done working with menu, so add the title filter back
         add_filter( 'the_title', array( $this, 'distributor_title_update'), 10, 2 );
         return $items;
     }// this filter fires after nav menu item creation is done
     //-----MOD POST TITLE---------------------
}
