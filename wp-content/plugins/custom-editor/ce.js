( function() {
    tinymce.create( 'tinymce.plugins.custombutton', {
        init: function( ed, url ) {
            ed.addCommand( 'button-alert', function() {
                alert( 'Teste de botão' );
            });

            ed.addButton( 
                'custom_button',
                {
                    title: 'Alerta',
                    cmd: 'button-alert',
                    image: url + '/alert.png' 
                }
            );
        }
    });
    tinymce.PluginManager.add( 'custom_button', tinymce.plugins.custombutton );
})();