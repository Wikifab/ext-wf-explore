( function ( $, mw ) {

	//form fulltext elements
	$("[id^='wf-expl-'][id$='-fulltext']").selectize({
	    create: true,
	    sortField: 'text'
	});

})(jQuery, mediaWiki);