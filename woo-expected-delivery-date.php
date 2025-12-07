<?php
/**
 * Plugin Name:       Woo Expected Delivery Date
 * Plugin URI:        https://stapolin.com
 * Description:       Display expected delivery dates for WooCommerce shipping methods based on business days.
 * Version:           1.0.0
 * Author:            Stapolin
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       woo-expected-delivery-date
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Woo_Expected_Delivery_Date' ) ) {
    class Woo_Expected_Delivery_Date {
        public function __construct() {
            add_action( 'init', [ $this, 'register_shipping_method_fields' ] );
            add_filter( 'woocommerce_cart_shipping_method_full_label', [ $this, 'append_expected_delivery_to_label' ], 10, 2 );
        }

        public function register_shipping_method_fields() {
            if ( ! function_exists( 'WC' ) ) {
                return;
            }

            $shipping = WC()->shipping();
            $shipping->load_shipping_methods();

            foreach ( $shipping->get_shipping_methods() as $method ) {
                if ( empty( $method->id ) ) {
                    continue;
                }

                add_filter( 'woocommerce_shipping_instance_form_fields_' . $method->id, [ $this, 'add_expected_delivery_field' ] );
            }

            if ( ! has_filter( 'woocommerce_shipping_instance_form_fields_free_shipping', [ $this, 'add_expected_delivery_field' ] ) ) {
                add_filter( 'woocommerce_shipping_instance_form_fields_free_shipping', [ $this, 'add_expected_delivery_field' ] );
            }
        }

        public function add_expected_delivery_field( $fields ) {
            $fields['expected_delivery_days'] = [
                'title'             => __( 'Expected delivery (business days)', 'woo-expected-delivery-date' ),
                'type'              => 'number',
                'description'       => __( 'Number of business days starting from the next business day (weekends excluded).', 'woo-expected-delivery-date' ),
                'desc_tip'          => true,
                'default'           => '',
                'sanitize_callback' => 'absint',
                'custom_attributes' => [
                    'min'  => 0,
                    'step' => 1,
                ],
            ];

            return $fields;
        }

        public function append_expected_delivery_to_label( $label, $method ) {
            $days = $this->get_expected_delivery_days( $method );

            if ( null === $days ) {
                return $label;
            }

            $expected_date = $this->calculate_expected_delivery_date( $days );

            if ( ! $expected_date ) {
                return $label;
            }

            $formatted = wp_date( 'j M Y', $expected_date->getTimestamp() );

            return sprintf(
                _x( '%1$s Expected delivery by %2$s', 'shipping label with expected delivery date', 'woo-expected-delivery-date' ),
                $label,
                $formatted
            );
        }

        private function get_expected_delivery_days( $method ) {
            if ( ! $method || ! method_exists( $method, 'get_instance_id' ) ) {
                return null;
            }

            $instance_id = $method->get_instance_id();

            if ( ! $instance_id ) {
                return null;
            }

            $shipping_method = WC_Shipping_Zones::get_shipping_method( $instance_id );

            if ( ! $shipping_method || ! is_array( $shipping_method->instance_settings ) ) {
                return null;
            }

            if ( ! array_key_exists( 'expected_delivery_days', $shipping_method->instance_settings ) ) {
                return null;
            }

            if ( $shipping_method->instance_settings['expected_delivery_days'] === '' ) {
                return null;
            }

            return max( 0, (int) $shipping_method->instance_settings['expected_delivery_days'] );
        }

        private function calculate_expected_delivery_date( int $days ) {
            $timezone = wp_timezone();
            $date     = new DateTimeImmutable( 'now', $timezone );
            $counter  = -1;

            while ( $counter < $days ) {
                $date = $date->modify( '+1 day' );
                $day  = (int) $date->format( 'N' );

                if ( $day < 6 ) {
                    ++$counter;
                }
            }

            return $date;
        }
    }

    new Woo_Expected_Delivery_Date();
}
