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

var explores = [];

function Explore(container) {

	this.$container = $(container);
	this.exploreMinPageNumber = 1;
	this.explorePageNumber = 1;
	this.autoScrollDownEnable = false;
	this.autoScrollUpEnable = false;
	this.requestRunning = 0;

	this.onInit();
};

Explore.prototype.onInit = function () {

	var explore = this;

	// load more buttons and events
	explore.$container.find('.load-more').on('click', function(evt) {
		explore.loadMoreClick(evt);
	});
    explore.$container.find('.load-more-previous').on('click', function(evt) {
		explore.loadPreviousClick(evt);
	});

    /* soumission du formulaire en ajax */
    // TODO : better selecter to be sure to select form related to this explore
    // (in case of multiple explore on the same page)
    $('form.wfExplore').on('submit', function(e) {
        e.preventDefault(); // J'empêche le comportement par défaut du navigateur, c-à-d de soumettre le formulaire
        explore.requestRunning ++;
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
				explore.proposedTagsBind();


				explore.setHandlerOnRemoveTags();
		        $('.exploreLoader').hide();
        		resultDiv.find('.load-more').on('click', function(evt) {
					explore.loadMoreClick(evt);
				});

        		explore.updateUriFromForm(form) ;

            	explore.requestRunning--;
            }
        });
    });


	explore.initPageParam();

	explore.proposedTagsBind();

	explore.setHandlerOnRemoveTags();
};

Explore.prototype.updateUriFromForm = function(form) {

    var uri = window.location.pathname + "?" + form.serialize();
	window.history.pushState(null, null, uri );
};

/**
 * initialise la variable page si elle est passé dan l'url
 * @returns
 */
Explore.prototype.initPageParam = function() {

    var url = window.location.href;
    var regex = new RegExp("[?&]page(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) {
		return null;
	}
    if (!results[2]) {
		return '';
	}
    this.explorePageNumber = parseInt(results[2]);
    this.exploreMinPageNumber = parseInt(results[2]);
};

/* function added to add tag with input */
Explore.prototype.addTag = function(form, value) {

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
};

/* clique sur une label recherché :
* deselection du label, et ressoumission du formulaire
*/
Explore.prototype.setHandlerOnRemoveTags = function() {

	$( ".wfexplore-selectedLabels .tag .remove" ).click(function (item) {

		var form = $(this).parents('form:first');
		var dataRole =  $( this ) . attr('data-role');
		inputID = $( this ) . attr('data-inputId');

		switch(dataRole) {

			case 'remove':
				form.find('#Label' + inputID).button('toggle');
				break;

			case 'categoryRemove':
				var input = form.find('#' + inputID);
				input.attr('checked', false);
				input.parents('.dynatree-selected').removeClass('dynatree-selected');
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
};

Explore.prototype.proposedTagsBind = function () {

	var explore = this;

	$(".proposedTag").click(function (event) {
		var form = $(this).parents('form:first');
		// add tag value in field
		explore.addTag(form, $(this).attr('data-value'));
		event.preventDefault();
    });

	$("#wf-expl-addTagButton").click(function () {
		var form = $(this).parents('form:first');
		// add tag value in field
		explore.addTag(form, $("#wf-expl-TagsInput").val());
		$("#wf-expl-TagsInput").val('');
    });

	$('#wf-expl-TagsInput').keypress(function (e) {
		 var key = e.which;
		 if(key == 13) { // the enter key code
			 $(this).parents('form:first').find('#wf-expl-addTagButton').click();
		    return false;
		 }
	});
};

Explore.prototype.changePageParameter = function(paramName, paramValue) {

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
};

/* Load More Button */

Explore.prototype.exploreLoadMore = function (direction, e) {

	if($(e.target).parent().prev().children().length > 0){
		var loadMore = $(e.target);
		var loadSpinner = loadMore.parent().prev().children('.loader');
		loadMore.html(loadSpinner.html());
		loadSpinner.show();
	}

	var explore = this;

	explore.requestRunning++;

	// TODO : improve selector to be sure to get the good form
	var $form = $('form.wfExplore:first');
	var pagenumber;

	if (direction == 'up') {
		$('.load-more-previous').html($('.exploreLoader').html());
		explore.exploreMinPageNumber = explore.exploreMinPageNumber -1;
		pagenumber = explore.exploreMinPageNumber;
		// incerment page number
	} else {
    	$('.load-more').html($('.exploreLoader').html());
    	explore.explorePageNumber = explore.explorePageNumber +1;
		pagenumber = explore.explorePageNumber;
	}
    $('.exploreLoader').show();

    $form.find('input[name=page]').val(pagenumber);

	var requestUrl = $form.attr('action');
	var requestType = $form.attr('method') ? $form.attr('method') : 'GET';
	var data = $form.serialize();


	// destination page can have 2 values :
	//  'explore' : it point to the spécial:WfExplore Page, and it must have all query params in the get params
	//  'self' : it call the actual page, with juste a 'page' param. Cannot works when using form filters
	var destPageType = 'explore';

	if($form.length == 0) {
		destPageType = 'self';
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
			var wfExplore = $data.find('#' + explore.$container.attr('id')).contents();
			if (destPageType == 'explore') {
				wfExplore = $data.find('.searchresults').contents();
			}

            // remove previous button
			wfExplore.find('.load-more-previous').remove();

			// remove old button
			if (direction == 'up') {
				explore.$container.find('.load-more-previous').remove();
				// append to .searchresults div content in dom
				explore.$container.prepend(wfExplore);
			} else {
				explore.$container.find('.load-more').remove();
				// append to .searchresults div content in dom
				explore.$container.append(wfExplore);
			}

            // idem for get .wfexplore-selectedLabels div content
			wfExplore = $data.find('.wfexplore-selectedLabels').contents();
			// replace .wfexplore-selectedLabels div content in dom
			$('.wfexplore-selectedLabels').empty();
			$('.wfexplore-selectedLabels').append(wfExplore);

			explore.setHandlerOnRemoveTags();
			loadSpinner.hide();
    		$('.exploreLoader').hide();

			if (direction == 'up') {
				// remove .load.more added
				explore.$container.find('.load-more').first().remove();
				// add load-more previous :
				if(pagenumber > 1) {
					//$('.searchresults').prepend('<div class="load-more-previous">' + mw.msg( 'wfexplore-load-more-tutorials-previous' ) + '</div>');

					$container.find('.load-more-previous').on('click', function(evt) {
						explore.loadPreviousClick(evt);
					});
				}
			} else {
				explore.$container.find('.load-more-previous').last().remove();
				explore.$container.find('.load-more').on('click', function(evt) {
					explore.loadMoreClick(evt);
				});
			}

    		// this second line replace the previous to use a slow effect, but do not change the uri
    		//$('html,body').animate({scrollTop: $('#explore-page' + pagenumber).offset().top}, 'slow');
    		//window.location.hash = '#page' + explorePageNumber;

			//this set all form params un uri :
			//updateUriFromForm($form);
			// this change only page, usefull for page query without filters :
    		explore.changePageParameter('page', pagenumber);
    		explore.requestRunning--;

        }
    });
};

Explore.prototype.autoLoadOnScrollDown = function() {

	var explore = this;

	if (explore.autoScrollDownEnable) {
		return;
	}

	explore.autoScrollDownEnable = true;

	$(window).scroll(function() {

	    if(explore.requestRunning == 0 && explore.$container.find('.load-more').length > 0 && $(window).scrollTop() + $(window).height() > $('.footer-main').offset().top ) {
	    	if (explore.requestRunning == 0) {
	    		explore.requestRunning = explore.requestRunning +1 ;

	    		explore.exploreLoadMore('down');
	    		explore.requestRunning --;
	    	}
	    }
	});
};

Explore.prototype.autoLoadOnScrollUp = function(container) {

	var explore = this;

	if (explore.autoScrollUpEnable) {
		return;
	}

	explore.autoScrollUpEnable = true;

	$(window).scroll(function() {

	    if(explore.requestRunning == 0 && explore.$container('.load-more-previous').length > 0 && $(window).scrollTop() < 10 ) {
	    	if (explore.requestRunning == 0) {
	    		explore.requestRunning = explore.requestRunning +1 ;

	    		explore.exploreLoadMore('up');
	    		explore.requestRunning --;
	    	}
	    }
	});
};

Explore.prototype.loadMoreClick = function(e) {

	var explore = this;

	if ( ! $(e.target).hasClass('no-autoload') ) {
		if (explore.autoScrollDownEnable) {
			explore.autoScrollDownEnable = false;
		} else {
			explore.autoLoadOnScrollDown();
		}
	}

	explore.exploreLoadMore('down', e);
};

Explore.prototype.loadPreviousClick = function(e) {

	var explore = this;

	if (explore.autoScrollUpEnable) {
		explore.autoScrollUpEnable = false;
	} else {
		explore.autoLoadOnScrollDown();
	}

	explore.exploreLoadMore('up');
};

$( document ).ready(function () {

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

    $('.searchresults').each(function() {
		explores.push(new Explore(this));
	});

	$('.ExploreTreeInput').each(function () {
		$(this).applyDynatree();
	});
});
