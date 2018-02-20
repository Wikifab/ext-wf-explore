<div class="<?php echo isset($bootstrapClass)?  $bootstrapClass : '' ?>">
		<ul class="nav nav-pills" role="tablist">
			<?php
			$inputName = "wf-expl-$category-fulltext" ;
			$pattern = '/[^0-9a-zA-Z\-_]/i';
			$replacement = '-';
			$inputId = preg_replace($pattern, $replacement, $inputName);
			?>
			<label id='Label<?php echo $inputId; ?>' class="fulltext-search-label <?php echo isset($selectedOptions[$category]['value']) ? 'active' : ''; ?>">
				<?php echo $categoryDetails['name'] ?>
			</label>
			<input id='<?php echo $inputId; ?>'
				name="<?php echo $inputName; ?>"
				type="text"
				class='fulltext-search'
				value="<?php echo isset($selectedOptions[$category]['value']) ? $selectedOptions[$category]['value'] : ''; ?>"
				autocomplete="off">
			</ul>
		</li>
		 </ul>
	 	</div>