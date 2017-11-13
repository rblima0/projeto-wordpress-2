$j = jQuery.noConflict();
$j(document).ready( function($) {
	var vote = true;
	$( '#post-rating span' ).each( function( index ) {
		$(this).data( 'id', index+1 );
		$(this).addClass( 'active' );
	});

	$( '#post-rating span' ).mouseover( function() {
		if ( vote ) {
			var id = $(this).data( 'id' );
			for ( var i=1; i<=id; i++ )
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
					rating: $(this).data( 'id' ),
					post_id: $(this).parent().attr( 'class' ).replace( 'ref-', '' )
				},
				success: function( results ) {
					if ( !results ) {
						$( '#post-rating' ).append( '<strong>Seu voto já está computado!</strong>' );
					} else {
						$( '#post-rating span' ).removeClass( 'on' );
						for ( var i=1; i<=results; i++ )
							$( '#post-rating span#star-' + i ).addClass( 'on' );

						$( '#post-rating' ).append( '<strong>Seu voto foi computado com sucesso!</strong>' );
					}
					setTimeout( function(){ $( '#post-rating strong' ).fadeOut( 500 ); }, 3000 );
				}
			});
			e.preventDefault();
		}
	});

});

var url_ajax = 'http://localhost/projeto-wordpress-2/wp-admin/admin-ajax.php';