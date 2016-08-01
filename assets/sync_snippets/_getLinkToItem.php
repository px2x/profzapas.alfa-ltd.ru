<?php


if (is_numeric($parent) && is_numeric($id) && $title != '' ) {
    
    return '<a href="'. $modx->makeUrl( $parent ) .'i/'. $id .'/ ">'.$title.'</a>';
}

?>