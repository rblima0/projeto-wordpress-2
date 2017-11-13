<?php

add_action( 'after_setup_theme', 'custom_setup' );

function custom_setup()
{
    add_action( 'customize_register', 'customizer' );
}

function customizer( $c ) // $wp_customize
{
    $c->add_section(
        'ct-section',
        array(
            'title'     => 'Seção personalizada',
            'priority'  => 30,
        )
    );

    $c->add_setting(
        'ct-option',
        array(
            'default'   => 'Valor padrão',
            'type'      => 'option'
        )
    );

    $c->add_setting(
        'ct-opt[a]',
        array(
            'type' => 'option'
        )
    );
    $c->add_setting(
        'ct-opt[b]',
        array(
            'type' => 'option'
        )
    );

    $c->add_setting(
        'ct-text',
        array(
            'type' => 'theme_mod'
        )
    );

    $c->add_setting( 'ct-file' );

    $c->add_control(
        new WP_Customize_Control(
            $c,
            'some-var',
            array(
                'label'     => 'Variável',
                'section'   => 'ct-section',
                'settings'  => 'ct-option',
            )
        )
    );

    $c->add_control(
        new WP_Customize_Color_Control(
            $c,
            'ct-option-a',
            array(
                'label'     => 'Alguma cor',
                'section'   => 'ct-section',
                'settings'  => 'ct-opt[a]',
            )
        )
    );
    $c->add_control(
        new WP_Customize_Image_Control(
            $c,
            'ct-option-b',
            array(
                'label'     => 'Imagem',
                'section'   => 'ct-section',
                'settings'  => 'ct-opt[b]',
            )
        )
    );

    $c->add_control(
        new WP_Customize_Control(
            $c,
            'ct-text-c',
            array(
                'label'     => 'Texto',
                'section'   => 'ct-section',
                'settings'  => 'ct-text',
            )
        )
    );
    
    $c->add_control(
        new WP_Customize_Upload_Control(
            $c,
            'ct-file-c',
            array(
                'label'     => 'Arquivo',
                'section'   => 'ct-section',
                'settings'  => 'ct-file',
            )
        )
    );
    
}