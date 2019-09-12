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
        <div class="<?php echo htmlspecialchars($bootstrapClass); ?>" >
            <ul class="nav nav-pills" role="tablist">
                <li>
                    <input type="text" id="<?php echo htmlspecialchars($inputId); ?>" name="<?php echo htmlspecialchars($inputName); ?>" placeholder="<?php echo $categoryDetails['name'] ?>">
                </li>
            </ul>
        </div>
	</ul>
</div>