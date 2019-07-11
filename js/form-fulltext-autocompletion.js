( function ( $, mw ) {

	$("[id^='wf-expl-'][id$='-fulltext']").not('[type=hidden]').each(function( index ) {

		var regex = RegExp('wf-expl-(.*?)-fulltext');

		var propname = regex.exec($(this)[0].id)[1];

		$(this).selectize({
		    valueField: 'value',
		    labelField: 'value',
		    searchField: 'value',
		    options: [],
		    create: false,
		    maxItems: 1,
		    load: function(query, callback) {
		        if (!query.length) return callback();

		        // first request to get token
				$.ajax({
					type: "GET",
					url: mw.util.wikiScript('api'),
					data: { action:'query', format:'json',  meta: 'tokens', type:'csrf'},
				    dataType: 'json',
				    success: onTokenSuccess
				});

				function onTokenSuccess( jsondata ) {

					var token = jsondata.query.tokens.csrftoken;
					var data = {};
					data.action = "exploregetpropertyvalues";
					data.format = "json";
					data.propname = propname;
					data.query = query;
					data.token = token;

					$.ajax({ 
						type: "POST",
						url: mw.util.wikiScript('api'),
						data: data,
					    dataType: 'json',
						success: function(res) {

							var array = [];
							res.exploregetpropertyvalues.forEach(function(element) {
							  array.push({
							  	'value': element
							  });
							});
							callback(array);
							
						},
						error: function() {
							callback();
						}
					})
				}
		    },
		    onItemAdd: function(value, $item) {
		    	console.log(value);
		    	$item.parents('.dropdown').removeClass('open');
		    }
		});

	});

})(jQuery, mediaWiki);