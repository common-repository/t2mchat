<?php

function t2m_react_route(){
    if ( !current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    ?>
    <div id="react-root"></div>
    <?php
}
?>