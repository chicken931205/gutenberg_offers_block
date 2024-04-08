<?php
/**
 * Plugin Name: Gutenberg Block - Offers
 * Plugin URI: https://github.com/chicken931205/gutenberg_offers_block
 * Description: This is a plugin displaying the offers using an external API.
 * Version: 1.1.0
 * Author: Golden Chicken
 *
 * @package gutenberg-block
 */

define( 'gutenberg_block_plugin_version', '1.0.0' );
define( 'gutenberg_block_plugin_file', __FILE__ );

if ( !class_exists( 'Gutenberg_Block' ) ) {
   class Gutenberg_Block {

	   	public function __construct() {
			add_action( 'init', array( &$this, 'offers__register_block' ) );
			add_action( 'enqueue_block_assets', array( &$this, 'load_block_editor_assets' ) );
	   	}

		function offers__register_block() {
			register_block_type( 
				__DIR__ . '/build' , 
				array(
					'render_callback' => array( &$this, 'render_block_offers' ),
				)
			);
		}	

	   function load_block_editor_assets() {
			$fontAwesomeCssPath = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css";
			wp_enqueue_style('fontawesom_css', $fontAwesomeCssPath, array(), gutenberg_block_plugin_version );
		
			$anmimationJsPath = "https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js";
			wp_enqueue_script( 'animation_js', $anmimationJsPath, array( 'jquery' ), gutenberg_block_plugin_version, true );

			$path = plugin_dir_url(gutenberg_block_plugin_file) . "src/asset/js/admin.js";
			wp_enqueue_script( 'admin_js', $path, array( 'jquery' ), gutenberg_block_plugin_version, true );
	   }

	   function isValidResponse( $response ) {
			if ( ! isset( $response ) || ! $response ) {
				return false;
			}

			if ( ! isset( $response->record ) || ! $response->record ) {
				return false;
			}

			if ( ! isset( $response->record->offers ) || ! $response->record->offers ) {
				return false;
			}

			if ( ! is_array( $response->record->offers ) ) {
				return false;
			}

			return true;
		}


	   function render_block_offers( $attributes ) {
			$api_url = $attributes['api_url'];
			if ( ! $api_url ) {
				return;
			}

			$response = wp_remote_get( $api_url );

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				return '<div class="components-placeholder"><div class="notice notice-error"><strong>' . __( 'Loading Error:' ) . '</strong> ' . esc_html( $error_message ) . '</div></div>';
			} else {
				$body = wp_remote_retrieve_body( $response );
				$res = json_decode( $body );

				if ( ! $this->isValidResponse( $res ) ) {
					return '<div class="components-placeholder"><div class="notice notice-error"><strong>' . __( 'Loading Error:' ) . '</strong> Invalid Response Format </div></div>';
				} else {
					$offers = $res->record->offers;

					$offer_items = "";
					foreach( $offers as $index => $offer ) {
						$deposits_img = "";
						foreach ( $offer->deposits as $deposit ) {
							$deposits_img .= "<img src='" . $deposit->dark_url . "' alt='" . $deposit->name . "'>";
						}

						$offer_item = "<div class='offer mt_20'>" .
											"<div class='logo'>" . 
												"<div class='ribbon mb_10'>" . $offer->ribbon . "</div>" . 
												"<div class='logo_img'>" . 
													"<img src='" . $offer->logo->dark . "' alt='logo'>" . 
												"</div>" .
												"<div class='preview mt_10'>" . 
													"<i class='fa fa-image'></i> Preview" . 
												"</div>" .
											"</div>" .
											"<div class='headlines'>"  . 
												"<div class='one'>"   . $offer->headlines->one->title   . "</div>" . 
												"<div class='two'>"   . $offer->headlines->two->title   . "</div>" . 
												"<div class='three'>" . $offer->headlines->three->title . "</div>" . 
											"</div>" .
											"<div class='stars_deposits'>"  . 
												"<div class='stars'>" . 
													"<div data-star='" . $offer->stars . "'>" .
														"<div class='before'>★★★★★</div>" .
														"<div class='after' style='width: " . $offer->stars * 20 . "%'>★★★★★</div>" . 
													"</div>" .												
												"</div>" .
												"<div class='carousel-container'>" .
													"<div class='carousel carousel_" . $index . "'>" .
														$deposits_img .
													"</div>" .
													"<div class='dots-container'>" .
														"<span class='dot active' onclick='handleDotClick(event, 0 , " . $index . ")'></span>" .
														"<span class='dot' onclick='handleDotClick(event, 1 , " . $index . ")'></span>" .
														"<span class='dot' onclick='handleDotClick(event, 2 , " . $index . ")'></span>" .
													"</div>" .
												"</div>" .
											"</div>" .
											"<div class='bullet_points'>"  . 
												"<div class='row'>" . 
													"<i class='fa fa-check'></i>" . 
													"<div class='title ml_5'>"   . $offer->bullet_points->one->title    . "</div>" . 
												"</div>" .
												"<div class='row'>" . 
													"<i class='fa fa-check'></i>" . 
													"<div class='title ml_5'>"   . $offer->bullet_points->two->title    . "</div>" . 
												"</div>" .
												"<div class='row'>" . 
													"<i class='fa fa-check'></i>" . 
													"<div class='title ml_5'>"   . $offer->bullet_points->three->title  . "</div>" . 
												"</div>" .
												"<div class='row'>" . 
													"<i class='fa fa-check'></i>" . 
													"<div class='title ml_5'>"   . $offer->bullet_points->four->title   . "</div>" . 
												"</div>" .
											"</div>" .
											"<div class='cta'>"  . 
												"<div class='one'>" . $offer->cta->one . "</div>" . 
												"<div class='two mt_5'>" . 
													"<a class='terms'>"  . $offer->cta->two   . "</a>" . 
													"<a class='review ml_5'> | Review</a>" . 
												"</div>" . 
											"</div>" .
										"</div>" . 
										"<div class='offer_footer mt_5'>" . $offer->fine_print . " | " . $offer->disclaimer . "</div>";
					
						$offer_items .= $offer_item;
					}

					return sprintf( '<div id = "offers">%s</div>', $offer_items );
				}
			}
	   }
   }
}

$gutenberg_block = new Gutenberg_Block;