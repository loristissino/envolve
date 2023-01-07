<?php

require_once( dirname( __FILE__ ) . '/config.php' );
require_once( dirname( __FILE__ ) . '/utility_functions.php');

function envolve_petition_form_shortcode( $atts, $content=null ) {
    global $envolve_config;
    $slug=getFromArray( 'slug', $atts );
    if ( !$slug ) {
        return '🔔 Note to the webmaster: slug not set.';
    }
    
    if ( isset( $_SESSION['_msg_just_ok'] ) ) {
        unset( $_SESSION['_msg_just_ok'] );
        return ""; // we don't show the form right after it was subscribed.
    }   
    
    $values = isset($_SESSION['_values'])  ? _b64_serialize($_SESSION['_values']) : '';
    
    $url = $envolve_config['provider_api_entrypoint'] . $envolve_config['actions']['form'] . '?key=' . $envolve_config['key'] . '&slug='. $slug . '&values=' . $values;
    
    $html = retrieveFromExternalSource( $url );
    
    $errors_msg = '';
    $field = false; // the first field with a detected error

    if ( isset($_SESSION['_msg_fail']) and sizeof($_SESSION['_errors'])>0 ) {
        $errors_msg = '<div class="envolve errors">' . $_SESSION['_msg_fail'] . '<ul><li>'
            . join('</li><li>', $_SESSION['_errors'])
            . '</li></ul></div>';
        unset( $_SESSION['_msg_fail'] );
    }
    $html = str_replace( '<div class="envolve errors"></div>', $errors_msg, $html );
        
    $_SESSION['_csrf'] = substr( $html, strpos($html, 'name="_csrf" value="')+20, 88 );
    
    $_SESSION['_form_url'] = $_SERVER['REQUEST_URI'];
    
    if ( sizeof($_SESSION['_errors'])>0 ) {
        $html .= "<script>";
        foreach($_SESSION['_errors'] as $key=>$value) {
            $html .= "document.querySelector('#petitionsignature-${key}').classList.add('error');";
        }
        $field = array_key_first($_SESSION['_errors']);
        $html .= "setTimeout(function() {document.querySelector('#petitionsignature-${field}').focus(); console.log('done');}, 2000);";
        $html .= "</script>";
    }

    return $html;
}

function envolve_list_petitions_shortcode( $atts, $content=null ) {
    global $envolve_config;
    $url = $envolve_config['provider_api_entrypoint'] . $envolve_config['actions']['list'] . '?key=' . $envolve_config['key'];
    return retrieveFromExternalSource( $url, 'petitions' );
}

function envolve_petition_signatures_shortcode( $atts, $content=null ) {
    global $envolve_config;
    $slug=getFromArray( 'slug', $atts );
    $limit=getFromArray( 'limit', $atts, 20 );
    if ( !$slug ) {
        return '🔔 Note to the webmaster: slug not set.';
    }

    $url = $envolve_config['provider_api_entrypoint'] . $envolve_config['actions']['signatures'] . '?key=' . $envolve_config['key'] . '&slug=' . $slug . '&limit='. $limit;
    
    return retrieveFromExternalSource( $url, 'petition_signatures_' . $slug );
}

function envolve_petition_text_shortcode( $atts, $content=null ) {
    global $envolve_config;
    $slug=getFromArray( 'slug', $atts );
    if ( !$slug ) {
        return '🔔 Note to the webmaster: slug not set.';
    }

    if (isset($_SESSION['_msg_ok'])) {
        $html = '<a name="thank-you"></a><div class="envolve thank-you">' . $_SESSION['_msg_ok'] . '</div>';
        unset($_SESSION['_msg_ok']);
        $_SESSION['_msg_just_ok']=1; // we need this for the shortcut of the form
        return $html;
    }   
    
    $url = $envolve_config['provider_api_entrypoint'] . $envolve_config['actions']['petition'] . '?key=' . $envolve_config['key'] . '&slug='. $slug;

    $html = retrieveFromExternalSource( $url, 'petition-' . $slug );
    return $html;
}

function envolve_confirm_signature_shortcode( $atts, $content=null ) {
    global $envolve_config;
    
    $slug = sanitize_text_field( getFromArray( 'slug', $_GET ) );
    $email = sanitize_text_field( getFromArray( 'email', $_GET ) );
    $code = sanitize_text_field( getFromArray( 'code', $_GET ) );
    
    if ( !$slug ) {
        return '🔔 Note to the webmaster: this page should not be linked directly.';
    }
    
    $url = $envolve_config['provider_api_entrypoint'] . $envolve_config['actions']['confirm'] . '?key=' . $envolve_config['key'] . '&slug='. $slug . '&email=' . $email . '&code=' . $code;
        
    $response = retrieveFromExternalSource( $url );
    
    switch ( $response ) {
        case '2':
            return '<div class="envolve thank-you">' . $envolve_config['messages']['confirmation_already_ok'] . '</div>';
        case '1':
            return '<div class="envolve thank-you">' . $envolve_config['messages']['confirmation_ok'] . '</div>';
        case '0':
            return '<div class="envolve errors">' . $envolve_config['messages']['confirmation_error'] . '</div>';
    }
}

function envolve_init_session() {
    if ( ! session_id() ) {
        session_start();
    }
}

// Start session on init hook.

add_action( 'init', 'envolve_init_session' );

add_shortcode('envolve_list_petitions', 'envolve_list_petitions_shortcode');

add_shortcode('envolve_petition_form', 'envolve_petition_form_shortcode');

add_shortcode('envolve_petition_text', 'envolve_petition_text_shortcode');

add_shortcode('envolve_petition_signatures', 'envolve_petition_signatures_shortcode');

add_shortcode('envolve_confirm_signature', 'envolve_confirm_signature_shortcode');
