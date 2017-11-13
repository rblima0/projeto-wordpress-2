
$j = jQuery.noConflict();

$j( document ).ready(function( $ ) {
    
    var vote = true;
    $( '#post-rating span' ).each( function( index ) {
        $( this ).data( 'id', index + 1 );
        $( this ).addClass( 'active' );
    });

    $( '#post-rating span' ).mouseover( function() {
        if ( vote ) {
            var id = $( this ).data( 'id' );
            for ( var i = 1; i <= id; i++ )
                $( '#post-rating span#star-' + i ).addClass( 'hover' );
        }
    });

    $( '#post-rating span' ).mouseout( function() {
        $( '#post-rating span' ).removeClass( 'hover' );
    });

    $( '#post-rating span' ).click( function( e ) {
        if ( vote ) {
            vote = false;
            $( '#post-rating span' ).addClass( 'off' );
            $.ajax({
                type: 'POST',
                url: url_ajax,
                data: {
                    action: 'vote',
                    rating: $( this ).data( 'id' ),
                    post_id: $( this ).parent().attr( 'class' ).replace( 'ref-', '' )
                },
                success: function( r ) {
                    r = JSON && JSON.parse( r ) || $j.parseJSON( r );
                    if (!r) {
                        $( '#post-rating' ).append( ' <strong>Recurso indisponível!</strong>' );
                    } else {
                        $( '#post-rating' ).append( ' <strong>' + r.msg + '</strong>' );
                        if ( !r.error ) {
                            $( '#cpr-avg' ).text( r.avg );
                            $( '#cpr-votes' ).text( r.votes );
                            $( '#post-rating span' ).removeClass( 'on' );
                            for (var i = 1; i <= r.stars; i++)
                                $( '#post-rating span#star-' + i).addClass( 'on' );
                        }
                    }
                    setTimeout(function() {
                        $( '#post-rating strong' ).fadeOut( 500 );
                    }, 3000);
                }
            });
            e.preventDefault();
        }
    });

});

var url_ajax = 'http://localhost/projeto-wordpress-2/wp-admin/admin-ajax.php';