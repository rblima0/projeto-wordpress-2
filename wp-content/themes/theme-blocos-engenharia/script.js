var $j = jQuery.noConflict(),
    url = 'http://localhost/blocos-engenharia/',
    url_ajax = url + 'wp-admin/admin-ajax.php';

$j(document).ready(function() {
    var loading = false;
    $j( '#get-site-name' ).click( function( e ){
		if ( !loading ) {
			loading = true;
			$j.ajax({
				type: 'POST',
				url: url_ajax,
				data: {
					action: 'get_site_name'
				},
				success: function( site_name ) {
                    alert( 'O nome do site é: ' +  site_name );
                    loading = false;
				}
			});
		}
        e.preventDefault();
    });
});