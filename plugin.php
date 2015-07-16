<?php
/**
 * Plugin Name: Clockwork API Example
 * Plugin URI: http://github.com/chrismccoy/clockwork
 * Description: Send a Text Message via an Input form using the Clockwork API
 * Version: 1.0
 * Author: Chris McCoy
 * Author URI: http://github.com/chrismccoy

 * @copyright 2015
 * @author Chris McCoy
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package Clockwork_SMS
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Initiate Clockwork_SMS Class on plugins_loaded
 *
 * @since 1.0
 */

if ( !function_exists( 'clockwork_sms' ) ) {

	function clockwork_sms() {
		$clockwork_sms = new Clockwork_SMS();
	}

	add_action( 'plugins_loaded', 'clockwork_sms' );
}

/**
 * Clockwork SMS Class for ajax, shortcode and clockwork api
 *
 * @since 1.0
 */

if( !class_exists( 'Clockwork_SMS' ) ) {

	class Clockwork_SMS {

               	// the api key which has credits
               	public $API_KEY = 'api key here';

		// default message to send
		public $text_message = 'Default Message to Send';

		/**
 		* Hook into hooks for ajax, shortcode, and clockwork api
 		*
 		* @since 1.0
 		*/
		public function __construct() {

			define( 'CLOCKWORK_DIR', plugin_dir_path( __FILE__ ) );

			require_once(CLOCKWORK_DIR . 'clockwork/class-Clockwork.php');
			require_once(CLOCKWORK_DIR . 'clockwork/class-ClockworkException.php');

			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
			add_action( 'wp_ajax_clockwork_send_sms', array( $this, 'wp_ajax_clockwork_send_sms' ));
			add_action( 'wp_ajax_nopriv_clockwork_send_sms', array( $this, 'wp_ajax_clockwork_send_sms'));
			add_shortcode( 'smsform', array( $this, 'smsform' ) );
		}

		/**
		 * enqueue ajax javascript
		 *
		 * @since 1.0
		 */
		public function wp_enqueue_scripts() {
			wp_enqueue_script('clockwork', plugins_url('js/clockwork.js', __FILE__), array( 'jquery' ), '1.0', true);
			wp_localize_script('clockwork', 'clockworkajax', array(
       				'ajaxurl' => admin_url('admin-ajax.php'),
       				'clockworkNonce' => wp_create_nonce('clockwork-nonce'),
       				'loading' => plugins_url('images/ajax-loader.gif', __FILE__)
			));
        	}

		/**
		 * html markup for the sms form
		 *
		 * @since 1.0
		 */
		public function smsform($atts, $content) {
			       $content = '<form>
						<label>Phone Number</label>
						<input type="text" style="width:35%" id="phone" name="phone" value="" />
						<input type="button" id="submit" name="submit" value="Submit" />
      					</form>
       					<br/><br/>
					<div id="result"></div>
        			';

        			return $content;
		}

       		/**
       		* function to send a text message using the clockwork api
       		*
       		* @since 1.0
       		*/
		public function wp_ajax_clockwork_send_sms() {

                	$nonce = $_POST['clockworkNonce'];

                	if ( ! wp_verify_nonce( $nonce, 'clockwork-nonce' ) )
                        	die ( 'Access Denied!');

                	$phone = $_POST['phone'];

                	try {
                    		$clockwork = new Clockwork( $this->API_KEY );
                    		$message = array( 'to' => $phone, 'message' => $this->text_message );
                    		$result = $clockwork->send( $message );

                    		if($result['success']) {
                        		echo 'Message sent - ID: ' . $result['id'];
                    		} else {
                        		echo 'Message failed - Error: ' . $result['error_message'];
                    		}
            		}

            		catch (ClockworkException $e) {
                		echo 'Exception sending SMS: ' . $e->getMessage();
            		}

        		die();
		}
	}
}
