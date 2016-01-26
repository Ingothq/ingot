/**
 * This script is always loaded in post editor, use for controlling the modal that opens with Ingot button.
 *
 * @since 1.1.0
 */
jQuery( document ).ready( function ( $ ) {
    $( document ).on( 'click', '#ingot-insert-shortcode-button', function(e){
        e.preventDefault();
        var chosen = $( 'input[name="ingot-group"]:checked' );
        if( ! chosen.length){
            return;
        }

        var code = '[ingot id="' + chosen.val() + '"]';

        chosen.prop('checked', false);
        window.send_to_editor(code);
        $('.caldera-modal-closer').trigger('click');
    } );

    $( document ).on( 'change', 'input[name="ingot-group"]', function(){
        var chosen = $( 'input[name="ingot-group"]:checked' );
        console.log( chosen.length  );
        if( 1 == chosen.length ){
            $( '#ingot-insert-shortcode-button' ).css( 'visibility', 'visible' ).attr( 'aria-hidden', 'false' );

        }else{
            $( '#ingot-insert-shortcode-button' ).css( 'visibility', 'hidden' ).attr( 'aria-hidden', 'true' );
        }
    });

} );
