<?php

add_action( "wp_ajax_generate_fcm_api", 'generateRandomAPIKEY', 10 );
add_action( "wp_ajax_nopriv_generate_fcm_api", 'generateRandomAPIKEY', 10 );

function generateRandomAPIKEY(){
    return wp_ajax_generate_password();
}