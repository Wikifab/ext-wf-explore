

## parsers usage :

### explore Page :

Navigate to the page 'Spécial:WfExplore'  to see the full explore page

### explore Query :

'exploreQuery' function just display a list of tutorials, accordint to a semantic query

1st param is  the query, the second param (optional) is the number limite of results to display.

Ex : 
  {{#exploreQuery:  [[area::Électronique]] | 8}}
  
### displayExplore function

to display explorer in a page, insert the function displayExplore :
  {{#displayExplore: params}}

  
## configuration :

2 vars enable to configure filters params :


  $wfexploreCategories = [ 
  		$categoriesName => [
  			CategorisValueName => CategorieValueLabel,
  			...
  		],
  		...
  ]

  $wfexploreCategoriesNames = [
  		'Type' => wfMessage( 'wfexplore-type' )->text() ,
  		'area' =>  wfMessage( 'wfexplore-category' )->text(),
  		'Difficulty' => wfMessage( 'wfexplore-difficulty' )->text() ,
  		'Cost' => wfMessage( 'wfexplore-cost' )->text() ,
  		'Complete' => 'Complete',
  ]

### configure layout to display results : 

it is possible to change the layout of results, to do it, set the available layouts in LocalSettings.php : 
  $wgExploreResultsLayouts = [
		'event' => __DIR__ . '/views/layout-event.html'
  ];
  
then, when calling the explore with parser function in a page, set the 'layout' params :
  {{#displayExplore: layout=event}}