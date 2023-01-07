<?php 

require_once( dirname( __FILE__ ) . '/config.php' );
require_once( dirname( __FILE__ ) . '/utility_functions.php');

if ( ! session_id() ) {
    session_start();
}

$errors = [];

$_SESSION['_values'] = $_POST['PetitionSignature'];

$_SESSION['_values']['message'] = substr( $_SESSION['_values']['message'], 0, 1024 );

if ( $_SESSION['_csrf']!=$_POST['_csrf'] ) {
    $errors['token'] = $envolve_config['messages']['invalid_token'];
}
else {
    if ( $_SESSION['envolve_captcha']!=strtoupper( $_POST['captcha_challenge'] ) ) {
        $errors['captcha'] = $envolve_config['messages']['invalid_captcha'];
    }
}

if ( sizeof($errors) == 0 ) {
    $values = isset($_SESSION['_values'])  ? _b64_serialize( $_SESSION['_values'] ) : '';
    $slug = $_SESSION['_values']['petition_slug'];
    $url = $envolve_config['provider_api_entrypoint'] . $envolve_config['actions']['form'] . '?key=' . $envolve_config['key'] . '&slug='. $slug . '&values=' . $values;
    $content = postToExternalSource( $url );
    $result = unserialize( $content );
    if ( $result['status']=='ok' ) {
        unset($_SESSION['_msg_fail']);
        unset($_SESSION['_errors']);
        $_SESSION['_msg_ok'] = $envolve_config['messages']['signature_ok'];
        header("Location: " . $_SESSION['_form_url']);
    }
    else {
        $errors += $result['errors'];
    }
}

$_SESSION['_msg_fail'] = $envolve_config['messages']['signature_error'];
$_SESSION['_errors'] = $errors;

// There are errors of some kind...
header("Location: " . $_SESSION['_form_url']. '#petition-form');
