<div class="search-section">
<div class="container">
<div class="WFfilter">
<div class="col-md-1 col-sm-0 col-xs-0"></div>
<div class="col-md-10 col-sm-12 col-xs-12">

	<?php foreach ($filtersData as $category => $categoryDetails) : ?>
		<div class="col-md-3 col-sm-3 col-xs-6">
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
<div class="search-filters-section wfexplore-selectedLabels">
<div class="container">
	<?php foreach ($selectedOptions as $category => $values) {
		if (! isset($filtersData[$category])) {
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
