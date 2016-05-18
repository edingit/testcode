/**
 * Handles:
 * - Selection and deselection of media in an Envira Gallery
 * - Toggling edit / delete button states when media is selected / deselected,
 * - Toggling the media list / grid view
 * - Storing the user's preferences for the list / grid view
 */

 // Setup some vars
var envira_gallery_output = '#envira-gallery-output',
    envira_gallery_shift_key_pressed = false,
    envira_gallery_last_selected_image = false;

jQuery( document ).ready( function( $ ) {
	
    // Enable sortable functionality on images
	envira_gallery_sortable( $ );

} );

/**
 * Enables sortable functionality on a grid of Envira Gallery Images
 *
 * @since 1.5.0
 */
function envira_gallery_sortable( $ ) {

    // Add sortable support to Envira Gallery Media items
    $( envira_gallery_output ).sortable( {
        containment: envira_gallery_output,
        items: 'li',
        cursor: 'move',
        forcePlaceholderSize: true,
        placeholder: 'dropzone',
        helper: function( e, item ) {

            // Basically, if you grab an unhighlighted item to drag, it will deselect (unhighlight) everything else
            if ( ! item.hasClass( 'selected' ) ) {
                item.addClass( 'selected' ).siblings().removeClass( 'selected' );
            }
            
            // Clone the selected items into an array
            var elements = item.parent().children( '.selected' ).clone();
            
            // Add a property to `item` called 'multidrag` that contains the 
            // selected items, then remove the selected items from the source list
            item.data( 'multidrag', elements ).siblings( '.selected' ).remove();
            
            // Now the selected items exist in memory, attached to the `item`,
            // so we can access them later when we get to the `stop()` callback
            
            // Create the helper
            var helper = $( '<li/>' );
            return helper.append( elements );

        },
        stop: function( e, ui ) {
            // Remove the helper so we just display the sorted items
            var elements = ui.item.data( 'multidrag' );
            ui.item.after(elements).remove();

            // Remove the selected class from everything
            $( 'li.selected', $( envira_gallery_output ) ).removeClass( 'selected' );
            
            // Send AJAX request to store the new sort order
            $.ajax( {
                url:      envira_gallery_metabox.ajax,
                type:     'post',
                async:    true,
                cache:    false,
                dataType: 'json',
                data: {
                    action:  'envira_gallery_sort_images',
                    order:   $( envira_gallery_output ).sortable( 'toArray' ).toString(),
                    post_id: envira_gallery_metabox.id,
                    nonce:   envira_gallery_metabox.sort
                },
                success: function( response ) {
                    // Repopulate the Envira Gallery Backbone Image Collection
                    EnviraGalleryImagesUpdate( false );
                    return;
                },
                error: function( xhr, textStatus, e ) {
                    // Inject the error message into the tab settings area
                    $( envira_gallery_output ).before( '<div class="error"><p>' + textStatus.responseText + '</p></div>' );
                }
            } );
        }
    } );

}