<?php if (count($filtersData) > 0):?>
<div class="search-section" id="search-<?php echo $exploreId; ?>">
<div class="container">

<?php if (isset($params['complete']) && $params['complete']):?>
	<input type="hidden" id="wf-expl-complete-1" name="wf-expl-complete-1" value='1'/>
<?php endif; ?>
<?php if (isset($params['layout']) && $params['layout']):?>
	<input type="hidden" id="layout" name="layout" value='<?php echo $params['layout'] ?>'/>
<?php endif; ?>
<div class="WFfilter">

	<?php foreach ($filtersData as $category => $categoryDetails) : ?>
		<?php
		// for categories with only 1 value, display a switch button instead of a dropdown
		if(isset($wgExploreCategoriesUsingSwitchButtons[$category])):
			foreach ($categoryDetails['values'] as $value) :?>

			  <div class="switch-btn">
				<?php

				$inputName = "wf-expl-$category-" . $value['id'];
				$pattern = '/[^0-9a-zA-Z\-_]/i';
				$replacement = '-';
				$inputId = preg_replace($pattern, $replacement, $inputName);

				$label = $categoryDetails['name'] . ' : ' . $value['name'];
				if (isset($wgExploreSwitchButtons["$category-" . $value['id']])) {
					$label = wfMessage($wgExploreSwitchButtons["$category-" . $value['id']]);
				}
				?>

				<p class="switch-p-container">
					<label class="switch-label" for="<?php echo $inputId;?>"><?php echo $label; ?></label>
					<label class="switch">
					  <input id='<?php echo $inputId; ?>' name="<?php echo $inputName; ?>"
								    		type="checkbox"
								    		<?php echo isset($selectedOptions[$category][$value['id']]) ? 'checked="checked"' : ''; ?>
								    		autocomplete="off">
					<span class="slider round"></span>
					</label>
				</p>
			<?php endforeach;?>
	 	  </div>
		<?php else:?>
		<div class="WFfilter-property">
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
		<?php endif;?>
	<?php endforeach; ?>

</div>
</div>
</div>
<?php endif;?>
<input id='wf-expl-Tags' name='wf-expl-Tags' type='hidden' value="<?php echo isset($selectedOptions['Tags']) ? $selectedOptions['Tags']['value'] : ''; ?>">
<div class="search-filters-section wfexplore-proposedTags">
<div class="container">
	<?php foreach ($tags as $tag) {

		// add a pseudo random class to enable style customisations :
		$class =  'tagpattern-' . (hexdec(substr(md5(strtolower($tag)), 0, 6)) % 33);

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
		if (isset($wgExploreCategoriesUsingSwitchButtons[$category])) {
			// if this is from a switch button, do not display label
			continue;
		}
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

		// exception : for 'Language' property, if this is default language selected, we do not display it :
		if ($category == 'Language') {
			if(count($values) == 1 && isset($values[$currentLanguage])) {
				continue;
			}
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
