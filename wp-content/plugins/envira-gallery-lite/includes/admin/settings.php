<?php
/**
 * Settings class.
 *
 * @since 1.0.0
 *
 * @package Envira_Gallery
 * @author  Thomas Griffin
 */
class Envira_Gallery_Settings {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Path to the file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Holds the base class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;

    /**
     * Holds the submenu pagehook.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $hook;

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

    }

    /**
     * Helper method for getting a setting's value. Falls back to the default
     * setting value if none exists in the options table.
     *
     * @since 1.3.3.6
     *
     * @param string $key   The setting key to retrieve.
     * @return string       Key value on success, false on failure.
     */
    public function get_setting( $key ) {

        // Prefix the key
        $prefixed_key = 'envira_gallery_' . $key;

        // Get the option value
        $value = get_option( $prefixed_key );

        // If no value exists, fallback to the default
        if ( ! $value ) {
            $value = $this->get_setting_default( $key );
        }

        // Allow devs to filter
        $value = apply_filters( 'envira_gallery_get_setting', $value, $key, $prefixed_key );

        return $value;

    }

    /**
     * Helper method for getting a setting's default value
     *
     * @since 1.3.3.6
     *
     * @param string $key   The default setting key to retrieve.
     * @return string       Key value on success, false on failure.
     */
    public function get_setting_default( $key ) {

        // Prepare default values.
        $defaults = $this->get_setting_defaults();

        // Return the key specified.
        return isset( $defaults[ $key ] ) ? $defaults[ $key ] : false;

    }

    /**
     * Retrieves the default settings
     *
     * @since 1.3.3.6
     *
     * @return array       Array of default settings.
     */
    public function get_setting_defaults() {

        // Prepare default values.
        $defaults = array(
            'media_position' => 'after',
        );

        // Allow devs to filter the defaults.
        $defaults = apply_filters( 'envira_gallery_settings_defaults', $defaults );
        
        return $defaults;

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Envira_Gallery_Settings object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Gallery_Settings ) ) {
            self::$instance = new Envira_Gallery_Settings();
        }

        return self::$instance;

    }

}

// Load the settings class.
$envira_gallery_settings = Envira_Gallery_Settings::get_instance();