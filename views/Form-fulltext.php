<?php 

$bootstrapClass = isset($bootstrapClass)?  $bootstrapClass : '';
$inputName = "wf-expl-$category-fulltext" ;
$pattern = '/[^0-9a-zA-Z\-_]/i';
$replacement = '-';
$inputId = preg_replace($pattern, $replacement, $inputName);
$active = isset($selectedOptions[$category]['value']) ? 'active' : '';
$valueSearch = isset($selectedOptions[$category]['value']) ? $selectedOptions[$category]['value'] : '';


?>

<div class="WFfilter-property">
	<ul class="nav nav-pills" role="tablist">
		<li class="dropdown mega-dropdown" id="myForm">
			<a id="drop5" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
			  <?php echo $categoryDetails['name'] ?>
			  <span class="caret"></span>
			</a>
			<ul class="dropdown-menu mega-dropdown-menu dropdown-menu-1cols">
				<div class="<?php echo htmlspecialchars($bootstrapClass); ?>" >
					<ul class="nav nav-pills" role="tablist">
						<li>
							<select id="<?php echo htmlspecialchars($inputId); ?>" name="<?php echo htmlspecialchars($inputName); ?>">
								<?php if(isset($categoryDetails['suggestions'])): ?>
									<?php foreach($categoryDetails['suggestions'] as $suggestion): ?>
										<option value="<?php echo $suggestion; ?>"> <?php echo $suggestion; ?> </option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</li>
					</ul>
			 	</div>
			</ul>
		</li>
	</ul>
</div>