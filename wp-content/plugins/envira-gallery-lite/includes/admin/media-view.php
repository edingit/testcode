<?php
/**
 * Media View class.
 *
 * @since 1.0.3
 *
 * @package Envira_Gallery
 * @author  Tim Carr
 */
class Envira_Gallery_Media_View {

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
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Base
        $this->base = Envira_Gallery_Lite::get_instance();

        // Modals
        add_filter( 'envira_gallery_media_view_strings', array( $this, 'media_view_strings' ) );
        add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );

    }

    /**
    * Adds media view (modal) strings
    *
    * @since 1.0.3
    *
    * @param    array   $strings    Media View Strings
    * @return   array               Media View Strings
    */ 
    public function media_view_strings( $strings ) {

        return $strings;

    }

    /**
    * Outputs backbone.js wp.media compatible templates, which are loaded into the modal
    * view
    *
    * @since 1.0.3
    */
    public function print_media_templates() {

        // Always output certain print media templates
        // Insert Gallery (into Visual / Text Editor)
        // Use: wp.media.template( 'envira-selection' )
        ?>
        <script type="text/html" id="tmpl-envira-selection">
            <div class="media-frame-title">
                <h1><?php _e( 'Insert', 'envira-gallery' ); ?></h1>
            </div>
            <div class="media-frame-content">
                <div class="attachments-browser envira-gallery envira-gallery-editor">
                    <ul class="attachments">
                    </ul>

                    <!-- Helpful Tips -->
                    <div class="media-sidebar">
                        <h3><?php _e( 'Helpful Tips', 'envira-gallery' ); ?></h3>
                        <strong><?php _e( 'Choosing Your Gallery', 'envira-gallery' ); ?></strong>
                        <p>
                            <?php _e( 'To choose your gallery, simply click on one of the boxes to the left. The "Insert Gallery" button will be activated once you have selected a gallery.', 'envira-gallery' ); ?>
                        </p>

                        <strong><?php _e( 'Inserting Your Gallery', 'envira-gallery' ); ?></strong>
                        <p>
                            <?php _e( 'To insert your gallery into the editor, click on the "Insert Gallery" button below.', 'envira-gallery' ); ?>
                        </p>
                    </div>

                    <!-- Search -->
                    <div class="media-toolbar">
                        <div class="media-toolbar-secondary">
                            <span class="spinner"></span>
                        </div>
                        <div class="media-toolbar-primary search-form">
                            <label for="envira-gallery-search" class="screen-reader-text"><?php _e( 'Search', 'envira-gallery' ); ?></label>
                            <input type="search" placeholder="<?php _e( 'Search', 'envira-gallery' ); ?>" id="envira-gallery-search" class="search" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Bar -->
            <div class="media-frame-toolbar">
                <div class="media-toolbar">
                    <div class="media-toolbar-primary search-form">
                        <button type="button" class="button media-button button-primary button-large media-button-insert" disabled="disabled">
                            <?php _e( 'Insert', 'envira-gallery' ); ?>
                        </button>
                    </div>
                </div>
            </div>
        </script>
        <?php

        // Single Selection Item (Gallery or Album)
        // Use: wp.media.template( 'envira-selection-item' )
        ?>
        <script type="text/html" id="tmpl-envira-selection-item"> 
            <div class="attachment-preview" data-id="{{ data.id }}">
                <div class="thumbnail">
                    <# 
                    if ( data.thumbnail != '' ) { 
                        #>
                        <img src="{{ data.thumbnail }}" alt="{{ data.title }}" />
                        <# 
                    } 
                    #>
                    <strong>
                        <span>{{ data.title }}</span>
                    </strong>
                    <code>
                        [envira-{{ data.action }} id="{{ data.id }}"]
                    </code>
                </div>
            </div>

            <a class="check">
                <div class="media-modal-icon"></div>
            </a>
        </script>
        <?php

        // Error
        // Use: wp.media.template( 'envira-gallery-error' )
        ?>
        <script type="text/html" id="tmpl-envira-gallery-error">
            <p>
                {{ data.error }}
            </p>
        </script> 

        <?php
    	// Only load other Backbone templates if we're on an Envira CPT.
    	global $post;
    	if ( isset( $post ) ) {
    		$post_id = absint( $post->ID );
    	} else {
    		$post_id = 0;
    	}

    	// Bail if we're not editing an Envira Gallery
    	if ( get_post_type( $post_id ) != 'envira' ) {
    		return;
    	}

        // Single Image Editor
        // Use: wp.media.template( 'envira-meta-editor' )
        ?>
        <script type="text/html" id="tmpl-envira-meta-editor">
			<div class="edit-media-header">
				<button class="left dashicons"><span class="screen-reader-text"><?php _e( 'Edit previous media item' ); ?></span></button>
				<button class="right dashicons"><span class="screen-reader-text"><?php _e( 'Edit next media item' ); ?></span></button>
			</div>
			<div class="media-frame-title">
				<h1><?php _e( 'Edit Metadata', 'envira-gallery' ); ?></h1>
			</div>
			<div class="media-frame-content">
				<div class="attachment-details save-ready">
					<!-- Left -->
	                <div class="attachment-media-view portrait">
	                    <div class="thumbnail thumbnail-image">
	                        <img class="details-image" src="{{ data.src }}" draggable="false" />
	                    </div>
	                </div>
	                
	                <!-- Right -->
	                <div class="attachment-info">
	                    <!-- Settings -->
	                    <div class="settings">
	                    	<!-- Attachment ID -->
	                    	<input type="hidden" name="id" value="{{ data.id }}" />
	                        
	                        <!-- Image Title -->
	                        <label class="setting">
	                            <span class="name"><?php _e( 'Title', 'envira-gallery' ); ?></span>
	                            <input type="text" name="title" value="{{ data.title }}" />
	                            <div class="description">
	                            	<?php _e( 'Image titles can take any type of HTML. You can adjust the position of the titles in the main Lightbox settings.', 'envira-gallery' ); ?>
	                            </div>
	                        </label>
	                        
	                        <!-- Alt Text -->
	                        <label class="setting">
	                            <span class="name"><?php _e( 'Alt Text', 'envira-gallery' ); ?></span>
	                            <input type="text" name="alt" value="{{ data.alt }}" />
	                            <div class="description">
									<?php _e( 'Very important for SEO, the Alt Text describes the image.', 'envira-gallery' ); ?>
								</div>
	                        </label>
	                        
	                        <!-- Link -->
	                        <label class="setting">
	                            <span class="name"><?php _e( 'URL', 'envira-gallery' ); ?></span>
	                            <input type="text" name="link" value="{{ data.link }}" />
	                            <# if ( typeof( data.id ) === 'number' ) { #>
		                            <span class="buttons">
		                            	<button class="button button-small media-file"><?php _e( 'Media File', 'envira-gallery' ); ?></button>
										<button class="button button-small attachment-page"><?php _e( 'Attachment Page', 'envira-gallery' ); ?></button>
									</span>
								<# } #>
								<span class="description">
									<?php _e( 'Enter a hyperlink if you wish to link this image to somewhere other than its full size image.', 'envira-gallery' ); ?>
								</span>
							</label>

                            <label class="setting">
                                <!-- Upgrade -->
                                <?php
                                Envira_Gallery_Notice_Admin::get_instance()->display_inline_notice( 
                                    'envira_gallery_edit_metadata',
                                    __( 'Want Captions and more options?', 'envira-gallery' ),
                                    __( 'By upgrading to Envira Pro, you can get access to numerous other features, including: HTML captions, open links in new windows, WooCommerce product integration and so much more!', 'envira-gallery' ),
                                    'warning',
                                    __( 'Click here to Upgrade', 'envira-gallery' ),
                                    Envira_Gallery_Common_Admin::get_instance()->get_upgrade_link(),
                                    false
                                );
                                ?>
                            </label>
	                    </div>
	                    <!-- /.settings -->     
	                   
	                    <!-- Actions -->
	                    <div class="actions">
	                        <a href="#" class="envira-gallery-meta-submit button media-button button-large button-primary media-button-insert" title="<?php esc_attr_e( 'Save Metadata', 'envira-gallery' ); ?>">
	                        	<?php _e( 'Save Metadata', 'envira-gallery' ); ?>
	                        </a>

							<!-- Save Spinner -->
	                        <span class="settings-save-status">
		                        <span class="spinner"></span>
		                        <span class="saved"><?php _e( 'Saved.', 'envira-gallery' ); ?></span>
	                        </span>
	                    </div>
	                    <!-- /.actions -->
	                </div>
	            </div>
			</div>
		</script> 

        <?php

    }
	
    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Envira_Gallery_Media_View object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Gallery_Media_View ) ) {
            self::$instance = new Envira_Gallery_Media_View();
        }

        return self::$instance;

    }

}

// Load the media class.
$envira_gallery_media_view = Envira_Gallery_Media_View::get_instance();