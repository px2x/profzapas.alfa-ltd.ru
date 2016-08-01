<?php

// не раь\ботает ?? РЕКУРСИЯ??
	if(is_array($cats) and isset($cats[$parent_id])){
        $tree = '<ul>';
        if($only_parent==false){
            foreach($cats[$parent_id] as $cat){
                $tree .= '<li>'.$cat['pagetitle'].' #'.$cat['id'];
				//$tree .=  build_tree($cats,$cat['id']);
				 
                $tree .= $modx->runSnippet( 'buildTree', array( 'cats' => $cats,  'cat' => $cat['id']));
                $tree .= '</li>';
            }
        }elseif(is_numeric($only_parent)){
            $cat = $cats[$parent_id][$only_parent];
            $tree .= '<li>'.$cat['pagetitle'].' #'.$cat['id'];
            //$tree .=  build_tree($cats,$cat['id']);
			$tree .= $modx->runSnippet( 'buildTree', array( 'cats' => $cats,  'cat' => $cat['id']));
            $tree .= '</li>';
        }
        $tree .= '</ul>';
    }
    else return null;
    return $tree;





?>