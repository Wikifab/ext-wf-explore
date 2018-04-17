<?php 

$bootstrapClass = isset($bootstrapClass)?  $bootstrapClass : '';
$inputName = "wf-expl-$category-fulltext" ;
$pattern = '/[^0-9a-zA-Z\-_]/i';
$replacement = '-';
$inputId = preg_replace($pattern, $replacement, $inputName);
$active = isset($selectedOptions[$category]['value']) ? 'active' : '';
$valueSearch = isset($selectedOptions[$category]['value']) ? $selectedOptions[$category]['value'] : '';

$searchFilters .=   '<div class="'.$bootstrapClass.'">
						<ul class="nav nav-pills" role="tablist">
							<li>
								<label id="Label'.$inputId.'" class="fulltext-search-label '.$active.'">
								'.$categoryDetails['name'].'
								</label>
								<input id="'.$inputId.'"
								name="'.$inputName.'"
								type="text"
								class="fulltext-search"
								value="'.$valueSearch.'"
								autocomplete="off">
							</li>
						</ul>
				 	</div>';
?>