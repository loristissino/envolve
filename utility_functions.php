<?php

function getFromArray( $key, $array, $default='' ) {
    return isset($array[$key]) ? $array[$key] : $default;
}

function retrieveFromExternalSource( $url, $transient_code='', $transient_time=-1 ) {
    global $envolve_config;
    if ( $transient_time == -1 ) {
        $transient_time = $envolve_config['transient_time'];
    }
        
    if ( $transient_code ) {
        $content = get_transient( $transient_code );
        if ( !$content ) {
            $content = file_get_contents( $url );
            set_transient( $transient_code, $content, $transient_time );
        }
    }
    else {
        $content = file_get_contents( $url );
    }
    return $content;
}

function postToExternalSource( $url ) {
    global $envolve_config;
    $context = stream_context_create( ['http'=>['method'=>'POST']] );
    $content = file_get_contents( $url, false, $context );
    return $content;
}

function _b64_serialize( $values ) {
    return str_replace( '/', '_', base64_encode( json_encode( $values ) ) );
}
