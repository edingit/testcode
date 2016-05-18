<?php
/**
 * WP List Table Admin Class.
 *
 * @since 1.5.0
 *
 * @package Envira_Gallery
 * @author  Tim Carr
 */
class Envira_Gallery_Table_Admin {

    /**
     * Holds the class object.
     *
     * @since 1.5.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Path to the file.
     *
     * @since 1.5.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Holds the base class object.
     *
     * @since 1.5.0
     *
     * @var object
     */
    public $base;

    /**
     * Holds the metabox class object.
     *
     * @since 1.5.0
     *
     * @var object
     */
    public $metabox;
    
    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = Envira_Gallery_Lite::get_instance();

        // Load the metabox class object.
        $this->metabox = Envira_Gallery_Metaboxes::get_instance();

        // Load CSS and JS.
        add_action( 'admin_enqueue_scripts', array( $this, 'styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );

        // Append data to various admin columns.
        add_filter( 'manage_edit-envira_columns', array( &$this, 'envira_columns' ) );
        add_action( 'manage_envira_posts_custom_column', array( &$this, 'envira_custom_columns'), 10, 2 );

    }
    
    /**
     * Loads styles for all Envira-based WP_List_Table Screens.
     *
     * @since 1.5.0
     *
     * @return null Return early if not on the proper screen.
     */
    public function styles() {

        // Get current screen.
        $screen = get_current_screen();
        
        // Bail if we're not on the Envira Post Type screen.
        if ( 'envira' !== $screen->post_type && 'envira_album' !== $screen->post_type ) {
            return;
        }

        // Bail if we're not on a WP_List_Table.
        if ( 'edit' !== $screen->base ) {
            return;
        }

        // Load necessary admin styles.
        wp_register_style( $this->base->plugin_slug . '-table-style', plugins_url( 'assets/css/table.css', $this->base->file ), array(), $this->base->version );
        wp_enqueue_style( $this->base->plugin_slug . '-table-style' );

        // Fire a hook to load in custom admin styles.
        do_action( 'envira_gallery_table_styles' );

    }

    /**
     * Loads scripts for all Envira-based Administration Screens.
     *
     * @since 1.5.0
     *
     * @return null Return early if not on the proper screen.
     */
    public function scripts() {

        // Get current screen.
        $screen = get_current_screen();
        
        // Bail if we're not on the Envira Post Type screen.
        if ( 'envira' !== $screen->post_type && 'envira_album' !== $screen->post_type ) {
            return;
        }

        // Bail if we're not on a WP_List_Table.
        if ( 'edit' !== $screen->base ) {
            return;
        }

        // Load necessary admin scripts
        wp_register_script( $this->base->plugin_slug . '-clipboard-script', plugins_url( 'assets/js/min/clipboard-min.js', $this->base->file ), array( 'jquery' ), $this->base->version );
        wp_enqueue_script( $this->base->plugin_slug . '-clipboard-script' );

        // Fire a hook to load in custom admin scripts.
        do_action( 'envira_gallery_admin_scripts' );

    }

    /**
     * Customize the post columns for the Envira post type.
     *
     * @since 1.0.0
     *
     * @param array $columns  The default columns.
     * @return array $columns Amended columns.
     */
    public function envira_columns( $columns ) {

        // Add additional columns we want to display.
        $envira_columns = array(
            'cb'            => '<input type="checkbox" />',
            'title'         => __( 'Title', 'envira-gallery' ),
            'image'         => __( '', 'envira-gallery' ),
            'shortcode'     => __( 'Shortcode', 'envira-gallery' ),
            'posts'         => __( 'Posts', 'envira-gallery' ),
            'modified'      => __( 'Last Modified', 'envira-gallery' ),
            'date'          => __( 'Date', 'envira-gallery' )
        );

        // Allow filtering of columns
        $envira_columns = apply_filters( 'envira_gallery_table_columns', $envira_columns, $columns );

        // Return merged column set.  This allows plugins to output their columns (e.g. Yoast SEO),
        // and column management plugins, such as Admin Columns, should play nicely.
        return array_merge( $envira_columns, $columns );

    }

    /**
     * Add data to the custom columns added to the Envira post type.
     *
     * @since 1.0.0
     *
     * @global object $post  The current post object
     * @param string $column The name of the custom column
     * @param int $post_id   The current post ID
     */
    public function envira_custom_columns( $column, $post_id ) {

        global $post;
        $post_id = absint( $post_id );

        switch ( $column ) {
            /**
            * Image
            */
            case 'image':
                // Get Gallery Images.
                $gallery_data = get_post_meta( $post_id, '_eg_gallery_data', true );
                if ( ! empty( $gallery_data['gallery'] ) && is_array( $gallery_data['gallery'] ) ) {
                    // Display the first image
                    $images = $gallery_data['gallery'];
                    reset( $images );
                    $key = key( $images );
                    if ( is_numeric( $key ) ) {
                        $thumb = wp_get_attachment_image_src( $key, 'thumbnail' );
                    } else {
                        $thumb = array( $image['src'] );
                    }

                    echo '<img src="' . $thumb[0] . '" width="75" /><br />';
                    printf( _n( '%d Image', '%d Images', count( $gallery_data['gallery'] ), 'envira-gallery' ), count( $gallery_data['gallery'] ) );
                }
                break;

            /**
            * Shortcode
            */
            case 'shortcode' :
                echo '
                <div class="envira-code">
                    <code id="envira_shortcode_' . $post_id . '">[envira-gallery id="' . $post_id . '"]</code>
                    <a href="#" title="' . __( 'Copy Shortcode to Clipboard', 'envira-gallery' ) . '" data-clipboard-target="#envira_shortcode_' . $post_id . '" class="dashicons dashicons-clipboard envira-clipboard">
                        <span>' . __( 'Copy to Clipboard', 'envira-gallery' ) . '</span>
                    </a>
                </div>';

                // Hidden fields are for Quick Edit
                // class is used by assets/js/admin.js to remove these fields when a search is about to be submitted, so we dont' get long URLs
                echo '<input class="envira-quick-edit" type="hidden" name="_envira_gallery_' . $post_id . '[columns]" value="' . $this->metabox->get_config( 'columns' ) . '" />
                <input class="envira-quick-edit" type="hidden" name="_envira_gallery_' . $post_id . '[gallery_theme]" value="' . $this->metabox->get_config( 'gallery_theme' ) . '" />
                <input class="envira-quick-edit" type="hidden" name="_envira_gallery_' . $post_id . '[gutter]" value="' . $this->metabox->get_config( 'gutter' ) . '" />
                <input class="envira-quick-edit" type="hidden" name="_envira_gallery_' . $post_id . '[margin]" value="' . $this->metabox->get_config( 'margin' ) . '" />
                <input class="envira-quick-edit" type="hidden" name="_envira_gallery_' . $post_id . '[crop_width]" value="' . $this->metabox->get_config( 'crop_width' ) . '" />
                <input class="envira-quick-edit" type="hidden" name="_envira_gallery_' . $post_id . '[crop_height]" value="' . $this->metabox->get_config( 'crop_height' ) . '" />';
                break;

            /**
            * Posts
            */
            case 'posts':
                $posts = get_post_meta( $post_id, '_eg_in_posts', true );
                if ( is_array( $posts ) ) {
                    foreach ( $posts as $in_post_id ) {
                        echo '<a href="' . get_permalink( $in_post_id ) . '" target="_blank">' . get_the_title( $in_post_id ).'</a><br />';
                    }
                }
                break; 

            /**
            * Last Modified
            */
            case 'modified' :
                the_modified_date();
                break;
        }

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.5.0
     *
     * @return object The Envira_Gallery_Table_Admin object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Gallery_Table_Admin ) ) {
            self::$instance = new Envira_Gallery_Table_Admin();
        }

        return self::$instance;

    }

}

// Load the table admin class.
$envira_gallery_table_admin = Envira_Gallery_Table_Admin::get_instance();