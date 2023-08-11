<?php
/*
Plugin Name: Weather Widget Plugin
Plugin URI:
Description: This plugin adds a weather widget.
Version: 1.0
Author: Irek
Author URI:
License: GPL2
*/

// The widget class
class My_Custom_Widget extends WP_Widget {

    // Main constructor
    public function __construct() {
        parent::__construct(
            'my_weather_widget',
            __( 'My Weather Widget', 'text_domain' ),
            array(
                'customize_selective_refresh' => true,
            )
        );
    }

    // The widget form (for the backend )
    public function form( $instance ) {

        // Set widget defaults
        $defaults = array(
            'title'    => 'Test',
            'select_city'   => '',
            'select_country'   => '',
        );

        // Parse current settings with defaults
        extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

        <?php // Widget Title ?>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

        <?php // Dropdown ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'select_city' ); ?>"><?php _e( 'Select city', 'text_domain' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'select_city' ); ?>" id="<?php echo $this->get_field_id( 'select_city' ); ?>" class="widefat">
                <?php
                // Your options array
                $options = array(
                    ''        => __( 'Select City', 'text_domain' ),
                    'Grenoble' => __( 'Grenoble', 'text_domain' ),
                    'Crest' => __( 'Crest', 'text_domain' ),
                );

                // Loop through options and add each one to the select dropdown
                foreach ( $options as $key => $name ) {
                    echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $select_city, $key, false ) . '>'. $name . '</option>';

                } ?>
            </select>
        </p>

        <?php // Dropdown 2 ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'select_country' ); ?>"><?php _e( 'Select country', 'text_domain' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'select_country' ); ?>" id="<?php echo $this->get_field_id( 'select_country' ); ?>" class="widefat">
                <?php
                // Your options array
                $options = array(
                    ''        => __( 'Select Country', 'text_domain' ),
                    'France' => __( 'France', 'text_domain' ),
                );

                // Loop through options and add each one to the select dropdown
                foreach ( $options as $key => $name ) {
                    echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $select_country, $key, false ) . '>'. $name . '</option>';

                } ?>
            </select>
        </p>

    <?php }

    // Update widget settings
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title']    = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
        $instance['select_city']   = isset( $new_instance['select_city'] ) ? wp_strip_all_tags( $new_instance['select_city'] ) : '';
        $instance['select_country']   = isset( $new_instance['select_country'] ) ? wp_strip_all_tags( $new_instance['select_country'] ) : '';
        return $instance;
    }

    // Display the widget
    public function widget( $args, $instance ) {

        extract( $args );

        // Check the widget options
        $title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
        $select_city   = isset( $instance['select_city'] ) ? $instance['select_city'] : '';
        $select_country   = isset( $instance['select_country'] ) ? $instance['select_country'] : '';
        $language = 'french';

        $api_url = "https://www.weatherwp.com/api/common/publicWeatherForLocation.php?city=$select_city&country=$select_country&language=$language";
        $response = wp_remote_get($api_url);

        $data = json_decode(wp_remote_retrieve_body($response));

        $temperature = $data->temp;
        $icon_url = $data->icon;
        $description = $data->description;

        // WordPress core before_widget hook (always include )
        echo $before_widget;

        // Display the widget
        ?>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
              integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
              crossorigin=""/>
        <div id="cnalps-weather-widget">
            <div class="weather-title">Météo à <? echo $select_city . ' - ' . $select_country ?></div>
            <?
            echo "$temperature °C - $description";
            echo "<img src='$icon_url' alt='Weather Icon'>";
            ?>
        </div>

        <label for="city">Choose city:</label>
        <select name="city" id="city">
            <option value="">--- Choose a city ---</option>
            <optgroup label="France">
                <option value="grenoble">Grenoble</option>
                <option value="crest">Crest</option>
            </optgroup>
            <optgroup label="Poland">
                <option value="opole">Opole</option>
            </optgroup>
        </select>
        <div id="map" style="height: 180px"></div>
        <?php

        // WordPress core after_widget hook (always include )
        echo ' <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
   integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
   crossorigin=""></script>';
        echo '<script src="/wp-content/plugins/widget-plugin/widget_weather.js"></script>';
        echo $after_widget;

    }

}

// Register the widget
function my_register_custom_widget() {
    register_widget( 'My_Custom_Widget' );
}
add_action( 'widgets_init', 'my_register_custom_widget' );