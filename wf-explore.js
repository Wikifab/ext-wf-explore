//Page Creator autocompletion

(function() {

	$( document ).ready(function() {
		$("#wf-expl-Page_creator-fulltext").autocomplete({

		    source : function(requete, reponse){

			    $.ajax({

			    	type: "POST",
					url: mw.util.wikiScript('api'),
					data: {
						action:'pfautocomplete', //PageForms
						format:'json',
						namespace: 'User',
						substr: $("#wf-expl-Page_creator-fulltext").val()
					},
				    dataType: 'json',

		            success : function(data){
		            	reponse($.map(data.pfautocomplete, function(obj){
		                    return obj.title;
		                }));
		            }

			    });

		    },
		    select: function( event, ui ) {
		    	$("#wf-expl-Page_creator-fulltext").val(ui.item.value);
        		var form = $(this).parents('form:first');
        		form.submit();
      		}

		});

	});

})();


$( document ).ready(function () {

	var exploreMinPageNumber = 1;
	var explorePageNumber = 1;
	var autoScrollDownEnable = false;
	var autoScrollUpEnable = false;
	var requestRunning = 0;

	/**
	 * initialise la variable page si elle est passé dan l'url
	 * @returns
	 */
	function initPageParam() {
	    var url = window.location.href;
	    var regex = new RegExp("[?&]page(=([^&#]*)|&|#|$)"),
	        results = regex.exec(url);
	    if (!results) {
			return null;
		}
	    if (!results[2]) {
			return '';
		}
	    explorePageNumber = parseInt(results[2]);
	    exploreMinPageNumber = parseInt(results[2]);
	}
	initPageParam();

	/* clique sur une label recherché :
	 * deselection du label, et ressoumission du formulaire
	 */
	 function setHandlerOnRemoveTags() {
		$( ".wfexplore-selectedLabels .tag .remove" ).click(function (item) {


			var form = $(this).parents('form:first');
			var dataRole =  $( this ) . attr('data-role');
			inputID = $( this ) . attr('data-inputId');

			switch(dataRole) {
				case 'remove':
					form.find('#Label' + inputID).button('toggle');
					break;
				case 'dateRemove':
					form.find('#'+inputID).val('');
					break;
				case 'textRemove':
					var valueToRemove = $( this ) . attr('data-textValue');
					var values = form.find('#' + inputID).val().split(',');
					index = values.indexOf(valueToRemove);
					if (index > -1) {
						values.splice(index, 1);
					}
					values = values.join();
					form.find('#' + inputID).val(values);
					break;
			}

			$( this ).parent().hide();

			//$("#wfExplore").submit();
			form.submit();

		});
	}

	//one filter at a time
	$('#sort-filters input[type="checkbox"]').on('change', function() {
	   $('#sort-filters input[type="checkbox"]').not(this).prop('checked', false);
	   $('#sort-filters label').not($(this).parent()).removeClass('active');
	});


	/* submit form on each change on filters */
	//$("form.wfExplore input[type=checkbox]")
	$("form.wfExplore input").change(function () {
		$(this).parents('form:first').submit();
	});

	function updateUriFromForm(form) {
        var uri = window.location.pathname + "?" + form.serialize();
		window.history.pushState(null, null, uri );
	}

	/* manage tags buttons */

	/* function added to add tag with input */
	function addTag(form, value) {
		value = value.trim();
		if( ! value) {
			return;
		}
		// add tag value in field
		var fieldValue = form.find("#wf-expl-Tags").val();
		if(fieldValue) {
			fieldValue += "," + value;
		} else {
			fieldValue = value;
		}
		form.find("#wf-expl-Tags").val(fieldValue);
		// subit form to apply filters
		form.submit();
	}

	function proposedTagsBind() {
		$(".proposedTag").click(function (event) {
			var form = $(this).parents('form:first');
			// add tag value in field
			addTag(form, $(this).attr('data-value'));
			event.preventDefault();
	    });

		$("#wf-expl-addTagButton").click(function () {
			var form = $(this).parents('form:first');
			// add tag value in field
			addTag(form, $("#wf-expl-TagsInput").val());
			$("#wf-expl-TagsInput").val('');
	    });

		$('#wf-expl-TagsInput').keypress(function (e) {
			 var key = e.which;
			 if(key == 13) { // the enter key code
				 $(this).parents('form:first').find('#wf-expl-addTagButton').click();
			    return false;
			 }
		});
	}

	proposedTagsBind();

	/* soumission du formulaire en ajax */
    $('form.wfExplore').on('submit', function(e) {
        e.preventDefault(); // J'empêche le comportement par défaut du navigateur, c-à-d de soumettre le formulaire
        requestRunning ++;
        var form = $(this); // L'objet jQuery du formulaire

        var exploreId = form.attr('data-exploreId');
        explorePageNumber = 1;
    	exploreMinPageNumber = 1;
        form.find('input[name=page]').val(explorePageNumber);

        $('.exploreLoader').show();
        // Envoi de la requête HTTP en mode asynchrone
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            success: function(html) {
                var $data = $(html);

                // get .searchresults div content from result
				wfExplore = $data.find('.searchresults').contents();
				// replace .searchresults div content in dom
				var resultDiv = $('#result-' + exploreId +'.searchresults');
				resultDiv.empty();
				resultDiv.append(wfExplore);

                // idem for get .wfexplore-selectedLabels div content
				wfExplore = $data.find('.wfexplore-selectedLabels').contents();
				// replace .wfexplore-selectedLabels div content in dom
				form.find('.wfexplore-selectedLabels').empty();
				form.find('.wfexplore-selectedLabels').append(wfExplore);

				// refresh tags proposals
				proposedTags = $data.find('.wfexplore-proposedTags').contents();
				form.find('.wfexplore-proposedTags').empty();
				form.find('.wfexplore-proposedTags').append(proposedTags);
				proposedTagsBind();


				setHandlerOnRemoveTags();
		        $('.exploreLoader').hide();
        		resultDiv.find('.load-more').on('click', loadMoreClick);

        		updateUriFromForm(form) ;

            	requestRunning --;
            }
        });
    });

    function changePageParameter(paramName, paramValue)
    {
        var url = window.location.href;
        var uri = window.location.pathname + window.location.search;
        var hash = location.hash;

        if (uri.indexOf(paramName + "=") >= 0)
        {
            var prefix = uri.substring(0, uri.indexOf(paramName));
            var suffix = uri.substring(uri.indexOf(paramName));
            suffix = suffix.substring(suffix.indexOf("=") + 1);
            suffix = (suffix.indexOf("&") >= 0) ? suffix.substring(suffix.indexOf("&")) : "";
            uri = prefix + paramName + "=" + paramValue + suffix;
        }
        else
        {
        if (uri.indexOf("?") < 0){uri += "?" + paramName + "=" + paramValue;} else {uri += "&" + paramName + "=" + paramValue;}
        }

		window.history.pushState(null, null, uri + hash);
    }



	/* Load More Button */
	function exploreLoadMore(direction) {

		requestRunning ++;

		var $form = $('form.wfExplore:first');
    	var pagenumber;
    	var loadMorePreviousButton = null;

    	if (direction == 'up') {
    		$('.load-more-previous').html($('.exploreLoader').html());
    		exploreMinPageNumber = exploreMinPageNumber -1;
    		pagenumber = exploreMinPageNumber;
    		// incerment page number
    	} else {
        	$('.load-more').html($('.exploreLoader').html());
        	explorePageNumber = explorePageNumber +1;
    		pagenumber = explorePageNumber;
		}
        $('.exploreLoader').show();

        $form.find('input[name=page]').val(pagenumber);

    	var requestUrl = $form.attr('action');
    	var requestType = $form.attr('method') ? $form.attr('method') : 'GET';
    	var data = $form.serialize();

    	if($form.length == 0) {
    		requestUrl = '?';
    		data = {page:pagenumber};
    	}
    	
        // Envoi de la requête HTTP en mode asynchrone
        $.ajax({
            url: requestUrl,
            type: requestType,
            data: data,
            success: function(html) {
                var $data = $(html);

                // get .searchresults div content from result
				var wfExplore = $data.find('.searchresults').contents();
				// remove previous button
				wfExplore.find('.load-more-previous').remove();

				// remove old button
				if (direction == 'up') {
					loadMorePreviousButton = $('.load-more-previous').clone();
					$('.load-more-previous').remove();
					// append to .searchresults div content in dom
					$('.searchresults').prepend(wfExplore);
				} else {
					$('.load-more').remove();
					// append to .searchresults div content in dom
					$('.searchresults').append(wfExplore);
				}

                // idem for get .wfexplore-selectedLabels div content
				wfExplore = $data.find('.wfexplore-selectedLabels').contents();
				// replace .wfexplore-selectedLabels div content in dom
				$('.wfexplore-selectedLabels').empty();
				$('.wfexplore-selectedLabels').append(wfExplore);

				setHandlerOnRemoveTags();
        		$('.exploreLoader').hide();

				if (direction == 'up') {
					// remove .load.more added
					$('.load-more').first().remove();
					// add load-more previous :
					if(pagenumber > 1) {
						//$('.searchresults').prepend('<div class="load-more-previous">' + mw.msg( 'wfexplore-load-more-tutorials-previous' ) + '</div>');

						//$('.searchresults').prepend(loadMorePreviousButton);
						$('.load-more-previous').on('click', loadPreviousClick);
					}
				} else {
					$('.load-more-previous').last().remove();
					$('.load-more').on('click', loadMoreClick);
				}

        		// this second line replace the previous to use a slow effect, but do not change the uri
        		//$('html,body').animate({scrollTop: $('#explore-page' + pagenumber).offset().top}, 'slow');
        		//window.location.hash = '#page' + explorePageNumber;

				//this set all form params un uri :
				//updateUriFromForm($form);
				// this change only page, usefull for page query without filters :
        		changePageParameter('page', pagenumber);
        		requestRunning --;

            }
        });
    }

	function autoLoadOnScrollDown() {
		if (autoScrollDownEnable) {
			return;
		}

		autoScrollDownEnable = true;

		$(window).scroll(function() {

		    if(requestRunning == 0 && $('.load-more').length > 0 && $(window).scrollTop() + $(window).height() > $('.footer-main').offset().top ) {
		    	if (requestRunning == 0) {
		    		requestRunning = requestRunning +1 ;

		    		exploreLoadMore(null);
		    		requestRunning --;
		    	}
		    }
		});

	}
	function autoLoadOnScrollUp() {
		if (autoScrollUpEnable) {
			return;
		}

		autoScrollUpEnable = true;

		$(window).scroll(function() {

		    if(requestRunning == 0 && $('.load-more-previous').length > 0 && $(window).scrollTop() < 10 ) {
		    	if (requestRunning == 0) {
		    		requestRunning = requestRunning +1 ;

		    		exploreLoadMore('up');
		    		requestRunning --;
		    	}
		    }
		});

	}

	function loadMoreClick(e) {
		if (autoScrollDownEnable) {
			autoScrollDownEnable = false;
		} else {
			autoLoadOnScrollDown();
		}
		exploreLoadMore('down');
	}

	function loadPreviousClick(e) {
		if (autoScrollUpEnable) {
			autoScrollUpEnable = false;
		} else {
			autoLoadOnScrollDown();
		}
		exploreLoadMore('up');
	}

    $('.load-more').on('click', loadMoreClick);
    $('.load-more-previous').on('click', loadPreviousClick);

    $('.explore-hidden-field').each(function() {
    	// little hack to set value of search field,
    	// when the search field is outside of the form
    	var clonedId = $(this).attr('data-exploreClonedId');
    	var value = $(this).val();
    	if( clonedId) {
    		$('#' + clonedId).each(function() {
    			$(this).val(value);
    		});
    	}
    });

    mw.loader.using( 'jquery.ui.datepicker' ).then( function () {
    	$( ".datepicker" ).datepicker({
        	dateFormat: 'yy/mm/dd',
            showButtonPanel: true
          });
    } );

	setHandlerOnRemoveTags();
});
