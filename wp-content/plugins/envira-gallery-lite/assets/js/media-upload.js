/**
 * Hooks into the global Plupload instance ('uploader'), which is set when includes/admin/metaboxes.php calls media_form()
 * We hook into this global instance and apply our own changes during and after the upload.
 *
 * @since 1.3.1.3
 */
(function( $ ) {
    $(function() {

        if ( typeof uploader !== 'undefined' ) {

            // Change "Select Files" button in the pluploader to "Select Files from Your Computer"
            $( 'input#plupload-browse-button' ).val( envira_gallery_metabox.uploader_files_computer );

            // Set a custom progress bar
            $( '#envira-gallery .drag-drop-inside' ).append( '<div class="envira-progress-bar"><div></div></div>' );
            var envira_bar      = $( '#envira-gallery .envira-progress-bar' ),
                envira_progress = $( '#envira-gallery .envira-progress-bar div' ),
                envira_output   = $( '#envira-gallery-output'),
                envira_error    = $( '#envira-gallery-upload-error' );

            // Files Added for Uploading
            uploader.bind( 'FilesAdded', function ( up, files ) {

                // Hide any existing errors
                $( envira_error ).html( '' );

                // Fade in the upload progress bar
                $( envira_bar ).fadeIn();

            } );

            // File Uploading - show progress bar
            uploader.bind( 'UploadProgress', function( up, file ) {
                $( envira_progress ).css({
                    'width': up.total.percent + '%'
                });
            });

            // File Uploaded - AJAX call to process image and add to screen.
            uploader.bind( 'FileUploaded', function( up, file, info ) {

                // AJAX call to Envira to store the newly uploaded image in the meta against this Gallery
                $.post(
                    envira_gallery_metabox.ajax,
                    {
                        action:  'envira_gallery_load_image',
                        nonce:   envira_gallery_metabox.load_image,
                        id:      info.response,
                        post_id: envira_gallery_metabox.id
                    },
                    function(res){
                        // Prepend or append the new image to the existing grid of images,
                        // depending on the media_position setting
                        switch ( envira_gallery_metabox.media_position ) {
                            case 'before':
                                $(envira_output).prepend(res);
                                break;
                            case 'after':
                            default:
                                $(envira_output).append(res);
                                break;
                        }

                        // Repopulate the Envira Gallery Image Collection
                        EnviraGalleryImagesUpdate( false );

                    },
                    'json'
                );
            });

            // Files Uploaded
            uploader.bind( 'UploadComplete', function() {

                // Hide Progress Bar
                $( envira_bar ).fadeOut();

            });

            // File Upload Error
            uploader.bind('Error', function(up, err) {

                // Show message
                $('#envira-gallery-upload-error').html( '<div class="error fade"><p>' + err.file.name + ': ' + err.message + '</p></div>' );
                up.refresh();

            });

        }

    });
})( jQuery );