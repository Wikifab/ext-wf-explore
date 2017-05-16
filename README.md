

## parsers usage :

### explore Query :

'exploreQuery' function just display a list of tutorials, accordint to a semantic query

1st param is  the query, the second param (optional) is the number limite of results to display.

Ex : 
  {{#exploreQuery:  [[area::Électronique]] | 8}}
  
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