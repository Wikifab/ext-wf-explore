

## parsers usage :

### explore Page :

Navigate to the page 'Spécial:WfExplore'  to see the full explore page

### explore Query :

'exploreQuery' function just display a list of tutorials, accordint to a semantic query

1st param is  the query, the second param (optional) is the number limite of results to display.
other named parameters can be set : 
- sort : field to use to sort results
- limit : number of result to display
- layout : layout to use (see layout config)

Ex : 
  {{#exploreQuery:  [[area::Électronique]] | 8}}
  {{#exploreQuery:  [[area::Électronique]] | sort=editdate|limit=8}}
  {{#exploreQuery:  [[area::Électronique]] | sort=editdate|limit=8| layout=event}}
  
### displayExplore function

to display explorer in a page, insert the function displayExplore :
  {{#displayExplore: params}}
  
### Input Box function

to add an input box to search pages using Special:WfExplore :
  {{#exploreinputbox:}}

  
## configuration using global vars :

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
  
  
### configure filters for layout

it is possible te define other filter when a layout is given:

  $wfexploreCategoriesByLayout = [
  	  'event' => [ 
  		  $categoriesName => [
  			  CategorisValueName => CategorieValueLabel,
    			...
    		],
    		...
    ]
  ];
  
### Wikifab Groups config
To configure explore for groups like wikifab, here is the config : (it set no filters for groups)

$wgExploreResultsLayouts = [
		'group' => __DIR__ . '/extensions/WfextStyle/templates/layout-group-search-result.html'
];

$wfexploreCategoriesByLayout = [
		'group' => []
];

  
### default sort order

Default sort order can be configured using the $wfeSortField var in LocalSettings.php. For instance, to sort by date of last change : 
  $wfeSortField = 'Modification date';
  
  
## configuration using hooks :

Explore::OnGetFilters
it is possible to change categories using hooks, this is recommended for internationalized wiki.

Record hooks in LocalSettings, and  set a function, for example :

  $wgHooks['Explore::getFilters'][] = 'onExploreGetFilters';
  function onExploreGetFilters(& $filters, $layout) {
  	$filters["Type"] = [
  		['Oukou'] = 'Oukoukou',
  		['Ouka'] = 'Oukaka'
  	];
 	return true;
 }

