/**
* Controller: Modal Window
* - Used by most Envira Backbone views to display information e.g. bulk edit, edit single image etc.
*/
if ( typeof EnviraGalleryModalWindow == 'undefined' ) {

    var EnviraGalleryModalWindow = new wp.media.view.Modal( {
        controller: {
            trigger: function() {
            }
        }
    } );

}

/**
* View: Error
* - Renders a WordPress style error message when something goes wrong.
*
* @since 1.4.3.0
*/
wp.media.view.EnviraGalleryError = wp.Backbone.View.extend( {

    // The outer tag and class name to use. The item is wrapped in this
    tagName   : 'div',
    className : 'notice error envira-gallery-error',

    render: function() {

        // Load the template to render
        // See includes/admin/media-views.php
        this.template = wp.media.template( 'envira-gallery-error' );

        // Define the HTML for the template
        this.$el.html( this.template( this.model ) );

        // Return the template
        return this;

    }

} );

/**
* View: Single Item (Gallery or Album)
* - Renders an <li> element within the "Choose your Gallery / Album" view
*
* @since 1.5.0
*/
var EnviraGallerySelectionItemView = wp.Backbone.View.extend( {
    
    /**
    * The Tag Name and Tag's Class(es)
    */
    tagName:    'li',
    className:  'attachment',

    /**
    * Template
    * - The template to load inside the above tagName element
    */
    template:   wp.template( 'envira-selection-item' ),

    /**
    * Initialize
    *
    * @param object model   EnviraGalleryImage Backbone Model
    */
    initialize: function( args ) {

        // Assign the model to this view
        this.model = args.model;

    },

    /**
    * Render
    * - Binds the model to the view, so we populate the view's fields and data
    */
    render: function() {

        // Get HTML
        this.$el.html( this.template( this.model.attributes ) );
        return this;

    }

} );

/**
* Gallery Selection View
*/
var EnviraGallerySelectionView = wp.Backbone.View.extend( {

    /**
    * The Tag Name and Tag's Class(es)
    */
    tagName:    'div',
    className:  'media-frame mode-select wp-core-ui hide-router hide-menu',

    /**
    * Template
    * - The template to load inside the above tagName element
    */
    template:   wp.template( 'envira-selection' ),

    /**
    * Events
    * - Functions to call when specific events occur
    */
    events: {
        // Clicked a gallery
        'click .attachment':                'click',

        // Used the search input
        'keyup':                            'search',
        'search':                           'search',

        // Insert Button
        'click button.media-button-insert': 'insert',
    },

    /**
    * Initialize
    */
    initialize: function( args ) {

        // Whether we're inserting galleries or albums
        this.action             = args.action;

        // Define a collection, which will store the Galleries
        this.selection          = new Backbone.Collection(); // The galleries / albums the user has selected
        this.collection         = new Backbone.Collection(); // The available galleries / albums

        // Define some other flags.
        this.is_loading         = false;
        this.search_timeout     = false;

        // Define loading and loaded events
        this.on( 'loading', this.loading, this );
        this.on( 'loaded',  this.loaded, this );

        // Get Galleries
        this.getItems( false, '' );

    },

    /**
     * Called when a Gallery is clicked
     *
     * @param object    event   Event
     */
    click: function( event ) {

        // Get the target element, whether it's a directory and its ID
        var target  = jQuery( event.currentTarget ),
            id      = jQuery( 'div.attachment-preview', target ).attr( 'data-id' );

        // Add or remove item from the selection, depending on its current state
        if ( target.hasClass( 'selected' ) ) {
            // Remove
            this.removeFromSelection( target, id );
        } else {
            // Add
            this.addToSelection( target, id );
        }
        
    },

    /**
     * Called when the search event is fired (the user types into the search field)
     *
     * @param object    event   Event
     */
    search: function( event ) {

        // If we're already loading something, bail
        if ( this.is_loading ) {
            return;
        }

        // Clear any existing timeout
        clearTimeout( this.search_timeout );

        // Check if a search term exists, and is at least 3 characters
        var search = event.target.value;

        // If search is empty, return the entire folder's contents
        if ( search.length == 0 ) {
            this.getItems( false, '' );
            return;
        }

        // If search isn't empty but less than 3 characters, don't do anything
        if ( search.length < 3 ) {
            return;
        }

        // Set a small timeout before we perform the search. If the user keeps typing,
        // this ensures we don't return the wrong results too early.
        var that = this;
        this.search_timeout = setTimeout( function() {
            that.getItems( true, search );  
        }, 1000 );

    },

    /**
    * Gets galleries by sending an AJAX request
    *
    * @param    bool    is_search       Is a search request
    * @param    string  search_terms    Search Terms
    */
    getItems: function( is_search, search_terms ) {

        // If we're already loading something, bail
        if ( this.is_loading ) {
            return;
        }

        // Clear the existing collection
        this.clearSelection();
        this.$el.find( 'ul.attachments' ).empty();
        this.$el.find( 'div.envira-gallery-error' ).remove();

        // Update the loading flag
        this.trigger( 'loading' );

        // Determine whether we're going to retrieve Galleries or Albums.
        var action = '';
        switch ( this.action ) {
            case 'gallery':
                action = 'envira_gallery_editor_get_galleries';
                break;
            case 'album':
                action = 'envira_albums_editor_get_albums';
                break;
        }

        // Perform AJAX request to get Galleries or Albums.
        wp.media.ajax( action, {
            context: this,
            data: {
                nonce:          envira_gallery_editor.get_galleries_nonce,
                search:         is_search,
                search_terms:   search_terms,
            },
            success: function( items ) {

                // Define a collection
                var collection = new Backbone.Collection( items );

                // Reset the collection
                this.collection.reset();

                // Add the collection's models (items) to this class' collection
                this.collection.add( collection.models );

                // Render each item in the collection
                this.collection.each( function( model ) {

                    // Init with model
                    var child_view = new EnviraGallerySelectionItemView( {
                        model: model
                    } );

                    // Render view within our main view
                    this.$el.find( 'ul.attachments' ).append( child_view.render().el );

                }, this );

                // Tell wp.media we've finished loading items
                this.trigger( 'loaded' );

            },
            error: function( error_message ) {

                // Tell wp.media we've finished loading items, and send the error message
                // for output
                this.trigger( 'loaded', error_message );

            }
        } );

    },

    /**
    * Render
    * - Binds the collection to the view, so we populate the view's attachments grid
    */
    render: function() {

        // Get HTML
        this.$el.html( this.template() );

        // Return
        return this;
        
    },

    /**
    * Renders an error using
    * wp.media.view.EnviraGalleryError
    */
    renderError: function( error ) {

        // Define model
        var model = {};
        model.error = error;

        // Define view
        var view = new wp.media.view.EnviraGalleryError( {
            model: model
        } );

        // Return rendered view
        return view.render().el;

    },

    /**
    * Tells the view we're loading by displaying a spinner
    */
    loading: function() {

        // Set a flag so we know we're loading data
        this.is_loading = true;

        // Show the spinner
        this.$el.find( '.spinner' ).css( 'visibility', 'visible' );

    },

    /**
    * Hides the loading spinner
    */
    loaded: function( response ) {

        // Set a flag so we know we're not loading anything now
        this.is_loading = false;

        // Hide the spinner
        this.$el.find( '.spinner' ).css( 'visibility', 'hidden' );

        // Display the error message, if it's provided
        if ( typeof response !== 'undefined' ) {
            this.$el.find( 'ul.attachments' ).before( this.renderError( response ) );
        }

    },

    /**
    * Adds the given target to the selection
    *
    * @param    object  target  Selected Element
    * @param    string  id      Unique Identifier (i.e. third party API item's UID)
    */
    addToSelection: function( target, id ) {

        // Trigger the loading event
        this.trigger( 'loading' );

        // Iterate through the current collection of models until we find the model
        // that has a path matching the given path
        this.collection.each( function( model ) {
            // If this model matches the model the user selected, add it to the selection
            if ( model.get( 'id' ) == id ) {
                this.selection.add( model );
            }
        }, this );

        // Mark the item as selected in the media view
        target.addClass( 'selected details' );

        // If the selection is not empty, enable the Insert button
        if ( this.selection.length > 0 ) {
            this.$el.find( 'button.media-button-insert' ).attr( 'disabled', false );
        }

        // Trigger the loaded event
        this.trigger( 'loaded' );

    },

    /**
    * Removes the given target from the selection
    *
    * @param    object  target  Deselected Element
    * @param    string  id      Unique Identifier (i.e. third party API item's UID)
    */
    removeFromSelection: function( target, id ) {

        // Trigger the loading event
        this.trigger( 'loading' );

        // Iterate through the current collection of selected models until we find the model
        // that has a path matching the given path
        this.selection.each( function( model ) {
            // remove this model from the collection of selected models
            this.selection.remove([{ cid: model.cid }]);
        }, this );

        // Mark the item as deselected in the media view
        target.removeClass( 'selected details' );

        // If the selection is empty, disable the Insert button
        if ( this.selection.length == 0 ) {
            this.$el.find( 'button.media-button-insert' ).attr( 'disabled', 'disabled' );
        }

        // Trigger the loaded event
        this.trigger( 'loaded' );

    },

    /**
    * Clears all selected items
    */
    clearSelection: function() {

        // Iterate through each item, removing the selected state from the UI
        this.selection.each( function( model ) {
            this.$el.find( 'div[data-id="' + model.get( 'id' ) + '"]' ).parent().removeClass( 'selected details' );
        }, this );

        // Disable the Insert button
        this.$el.find( 'button.media-button-insert' ).attr( 'disabled', 'disabled' );

        // Clear the selected models
        this.selection.reset();

    },

    /**
    * Inserts one or more Galleries or Albums into the Editor
    */
    insert: function() {

        // Tell the View we're loading
        this.trigger( 'loading' );

        // For each selected item, insert a shortcode into the editor
        this.selection.forEach( function( item ) {
            wp.media.editor.insert( '[envira-' + this.action + ' id="' + item.id + '"]' );
        }, this );

        // Trigger the loaded event
        this.trigger( 'loaded' );

        // Close the modal
        EnviraGalleryModalWindow.close();

    }

} );

jQuery( document ).ready( function( $ ) {

    // Open the "Add Gallery" / "Add Album" modal
    $( document ).on( 'click', 'a.envira-gallery-choose-gallery, a.envira-albums-choose-album', function( e ) {

        // Prevent default action
        e.preventDefault();

        // Get the action
        var action = $( this ).data( 'action' );

        // Define the modal's view
        EnviraGalleryModalWindow.content( new EnviraGallerySelectionView( {
            action: action
        } ) );

        // Open the modal window
        EnviraGalleryModalWindow.open();

    } );

} );