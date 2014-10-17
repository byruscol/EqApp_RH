<?php
add_action('wp_head', 'include_js'); 
function include_js(){
    echo stripcslashes( get_option("js_includer_code") );
}
?>
 