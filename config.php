<?php
 //TEST
    //Inicializamos la variable que contendra el codigo a crear/editar
    $code = "";
    $code2 ="";
 
    if(array_key_exists("Codigo", $_POST)){
        //si existe la variable en el request, entonces la guardamos como opcion
        $code = $_POST["Codigo"];
        $code_test = get_option("js_includer_code");
        //si existe la opcion la actualizamos
        if($code_test){
            update_option("js_includer_code", $code);
        } else {
            //si no existe la agregamos
            add_option("js_includer_code", $code);
        }
 
    } else {
        //si no hay request chequeamos si ya hay un valor guardado previamente
        //si hay opcion guardada esta funcion regresa la opcion
        //si no regresa lo que le coloquemos en el segundo parametro, en este caso una cadena vacia
        $code2 = get_option("js_includer_code");
    }
 
?>
 
<h1>gulf-SGC</h1>
<code>
<i>
<?php
    //para referencia mostramos el valor actual del plugin
    if($code2){
        echo htmlspecialchars( stripcslashes ($code2) );
    }   else {
        echo htmlspecialchars( stripcslashes ($code ) );
    }
 
?><br/><br/>
</i>
</code>
 
<b>Escribir el codigo que desea incluir en el encabezado:</b><br/><br/>
 
<form method="POST" action="<?php echo  $_SERVER['PHP_SELF']."?page=js-includer-plugin" ?>">
    <label>Código:</label><br/>
    <textarea name="Codigo" cols="140" rows="20"> </textarea><br/>
    <input type="submit" value="Guardar">
</form>


<div class="wrap">
<?php screen_icon(); ?>
<h2>
<?php
echo esc_html( $title );
if ( current_user_can( 'upload_files' ) ) { ?>
	<a href="media-new.php" class="add-new-h2"><?php echo esc_html_x('Add New', 'file'); ?></a><?php
}
if ( ! empty( $_REQUEST['s'] ) )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', get_search_query() ); ?>
</h2>
