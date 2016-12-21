<?php if (count($filtersData) > 0):?>
<div class="search-section">
<div class="container">
<div class="WFfilter">
<div class="col-md-1 col-sm-0 col-xs-0"></div>
<div class="col-md-10 col-sm-12 col-xs-12">

<?php

switch(count($filtersData)) {
	case 1 :
		$bootstrapClass = "col-md-12 col-sm-12 col-xs-12";
		break;
	case 2 :
		$bootstrapClass = "col-md-6 col-sm-6 col-xs-6";
		break;
	case 3 :
		$bootstrapClass = "col-md-4 col-sm-4 col-xs-6";
		break;
	case 4 :
		$bootstrapClass = "col-md-3 col-sm-3 col-xs-6";
		break;
	case 5 :
	default :
		$bootstrapClass = "col-md-2 col-sm-3 col-xs-6";
		break;
}
?>
	<?php foreach ($filtersData as $category => $categoryDetails) : ?>
		<div class="<?php echo $bootstrapClass ?>">
	    <ul class="nav nav-pills" role="tablist">
	      <li class="dropdown mega-dropdown" id="myForm">
	        <a id="drop5" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
	          <?php echo $categoryDetails['name'] ?>
	          <span class="caret"></span>
	        </a>
			<?php

			$nbcolone = ceil(count($categoryDetails['values']) / 7.0);
			$nbPerColone = ceil(count($categoryDetails['values']) / $nbcolone);
			$nbBootstrap = ceil(12 / $nbcolone);
			$colCount = 0;
			?>
			<ul class="dropdown-menu mega-dropdown-menu dropdown-menu-<?php echo $nbcolone ?>cols">

			<?php if (isset($params['complete']) && $params['complete']):?>
				<input type="hidden" id="wf-expl-complete-1" name="wf-expl-complete-1" value='1'/>
			<?php endif; ?>

			<div class="row">
				<?php
				foreach ($categoryDetails['values'] as $key => $value) {
					$inputName = "wf-expl-$category-" . $value['id'];
					$pattern = '/[^0-9a-zA-Z\-_]/i';
					$replacement = '-';
					$inputId = preg_replace($pattern, $replacement, $inputName);

					$colCount++;
					if($colCount > $nbcolone) {
						echo '</div><div class="row">';
						$colCount = 1;
					}
					?>
					<div class="col-sm-<?php echo $nbBootstrap; ?> col-xs-12">
						<div class="btn-group" data-toggle="buttons">
						  <label id='Label<?php echo $inputId; ?>' class="btn btn-primary <?php echo isset($selectedOptions[$category][$value['id']]) ? 'active' : ''; ?>">
						    <input id='<?php echo $inputId; ?>' name="<?php echo $inputName; ?>"
						    		type="checkbox"
						    		<?php echo isset($selectedOptions[$category][$value['id']]) ? 'checked="checked"' : ''; ?>
						    		autocomplete="off">
						     <?php echo $value['name'] ?>
						  </label>
						</div>
					</div>

				<?php } ?>
			</div>
			</ul>
	      </li>
		 </ul>
	 	</div>
	<?php endforeach; ?>

</div>
<div class="col-md-1 col-sm-0 col-xs-0"></div>
</div>
</div>
</div>
<?php endif;?>
<input id='wf-expl-Tags' name='wf-expl-Tags' type='hidden' value="<?php echo isset($selectedOptions['Tags']) ? $selectedOptions['Tags']['value'] : ''; ?>">
<div class="search-filters-section wfexplore-proposedTags">
<div class="container">
	<?php foreach ($tags as $tag) {

		// add a pseudo random class to enable style customisations :
		$class =  'tagpattern-' . (hexdec(substr(md5(strtolower($tag)), 0, 6)) % 30);

		echo ' <a class="proposedTag '.$class.'" data-value="' . $tag . '" ><span class="tag label label-default">'
				. $tag
				. ' </span> </a>';
	}?>
	<!--
	Tag : <input id='wf-expl-TagsInput' name="wf-expl-TagsInput" />
	<button id="wf-expl-addTagButton" name="addTag" type="button">+</button>
	-->
<span></span>
</div>
</div>

<div class="search-filters-section wfexplore-selectedLabels">
<div class="container">
	<?php foreach ($selectedOptions as $category => $values) {
		if (! isset($filtersData[$category]) ) {

			if(isset($values['type']) && $values['type'] =='text' && $values['value']) {
				// case of text values
				echo ' <span class="category-filter-title">' . $category . ' : </span>';
				$textValues = explode(',',$values['value']);
				foreach ($textValues as $textValue) {
					$inputId = "wf-expl-$category";
					$pattern = '/[^0-9a-zA-Z\-_]/i';
					$replacement = '-';
					$inputId = preg_replace($pattern, $replacement, $inputId);
					echo ' <span class="tag label label-default">'
							. $textValue
							. ' <span class="remove tagRemove" data-role="textRemove" data-textValue="'.$textValue.'" data-inputId="' . $inputId . '"> '
									. 'x</span></span> ';
				}
			}
			continue;
		}
		echo ' <span class="category-filter-title">' . $filtersData[$category]['name'] . ' : </span>';
		foreach ($values as $id => $value) {
			$inputId = "wf-expl-$category-" . $id;
			$pattern = '/[^0-9a-zA-Z\-_]/i';
			$replacement = '-';
			$inputId = preg_replace($pattern, $replacement, $inputId);
			echo ' <span class="tag label label-default">'
				. $value['valueName']
				. ' <span class="remove" data-role="remove" data-inputId="' . $inputId . '"> '
				. 'x</span></span> ';
		}
	}?>
<span></span>
</div>
</div>
