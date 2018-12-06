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
							<input type="text" id="<?php echo htmlspecialchars($inputId); ?>" name="<?php echo htmlspecialchars($inputName); ?>">
						</li>
					</ul>
			 	</div>
			</ul>
		</li>
	</ul>
</div>