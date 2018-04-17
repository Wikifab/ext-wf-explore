<?php

$name = $categoryDetails['name'];
$inputName = "wf-expl-$category-sort" ;
$pattern = '/[^0-9a-zA-Z\-_]/i';
$replacement = '-';

/*default value if (!isset($params['sort']) && $category == 'Modification_date') $params['sort'] = $category;*/

$checked = isset($params['sort']) && $params['sort'] == $category ? 'checked="checked"' : '';
$active = isset($params['sort']) && $params['sort'] == $category ? 'active' : '';
$inputId = preg_replace($pattern, $replacement, $inputName);
$sortFilters .= '
				<div class="btn-group" data-toggle="buttons">
			  <label id="Label'.$inputId.'" class="btn btn-primary '.$active.'">
			    <input id="'.$inputId.'" name="'.$inputName.'"
			    		type="checkbox" '.$checked.' 
			    		autocomplete="off">'.
			     $name.'
			  </label></div>';

?>