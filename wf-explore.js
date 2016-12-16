


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
	    if (!results) return null;
	    if (!results[2]) return '';
	    explorePageNumber = parseInt(results[2]);
	    exploreMinPageNumber = parseInt(results[2]);
	}
	initPageParam();
	
	/* clique sur une label recherché : 
	 * deselection du label, et ressoumission du formulaire
	 */
	 function setHandlerOnRemoveTags() {
		$( ".wfexplore-selectedLabels .tag .remove" ).click(function (item) {
			

			var dataRole =  $( this ) . attr('data-role');
			inputID = $( this ) . attr('data-inputId');
			
			switch(dataRole) {
				case 'remove':
					$('#Label' + inputID).button('toggle');
					break;
				case 'textRemove':
					var valueToRemove = $( this ) . attr('data-textValue');
					var values = $('#' + inputID).val().split(',');
					index = values.indexOf(valueToRemove);
					if (index > -1) {
						values.splice(index, 1);
					}
					values = values.join();
					$('#' + inputID).val(values);
					break;
			}
			
			$( this ).parent().hide();

			$("#wfExplore").submit();

		});
	}
	

	/* submit form on each change on filters */
	$("#wfExplore input[type=checkbox]").change(function () {

		$("#wfExplore").submit();
    });
	
	function updateUriFromForm(form) {
        var uri = window.location.pathname + "?" + form.serialize();
		window.history.pushState(null, null, uri );
	}
	
	/* manage tags buttons */

	/* function added to add tag with input */
	function addTag(value) {
		value = value.trim();
		console.log(value);
		if( ! value) {
			return;
		}
		// add tag value in field
		var fieldValue = $("#wf-expl-Tags").val();
		if(fieldValue) {
			fieldValue += "," + value;
		} else {
			fieldValue = value;
		}
		$("#wf-expl-Tags").val(fieldValue);
		// subit form to apply filters
		$("#wfExplore").submit();
	}

	function proposedTagsBind() {
		$(".proposedTag").click(function () {
			// add tag value in field
			addTag($(this).attr('data-value'));
			event.preventDefault();
	    });
		
		$("#wf-expl-addTagButton").click(function () {
			// add tag value in field
			addTag($("#wf-expl-TagsInput").val());
			$("#wf-expl-TagsInput").val('');
	    });
		
		$('#wf-expl-TagsInput').keypress(function (e) {
			 var key = e.which;
			 if(key == 13) { // the enter key code
			    $('#wf-expl-addTagButton').click();
			    return false;  
			 }
		});
	}
	
	proposedTagsBind();
	

	/* soumission du formulaire en ajax */
    $('#wfExplore').on('submit', function(e) {
        e.preventDefault(); // J'empêche le comportement par défaut du navigateur, c-à-d de soumettre le formulaire
        requestRunning ++;
        var form = $(this); // L'objet jQuery du formulaire

        explorePageNumber = 1;
    	exploreMinPageNumber = 1;
        $('#wfExplore input[name=page]').val(explorePageNumber);
 
        $('.loader').show();
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
				$('.searchresults').empty();  
				$('.searchresults').append(wfExplore);

                // idem for get .wfexplore-selectedLabels div content 
				wfExplore = $data.find('.wfexplore-selectedLabels').contents();
				// replace .wfexplore-selectedLabels div content in dom
				$('.wfexplore-selectedLabels').empty();  
				$('.wfexplore-selectedLabels').append(wfExplore);
				
				// refresh tags proposals
				proposedTags = $data.find('.wfexplore-proposedTags').contents();
				$('.wfexplore-proposedTags').empty();  
				$('.wfexplore-proposedTags').append(proposedTags);
				proposedTagsBind();
				

				setHandlerOnRemoveTags();
        		$('.loader').hide();
        		$('.load-more').on('click', loadMoreClick);
        		
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
        if (uri.indexOf("?") < 0)
        	uri += "?" + paramName + "=" + paramValue;
        else
        	uri += "&" + paramName + "=" + paramValue;
        }
        
		window.history.pushState(null, null, uri + hash);
    }

	

	/* Load More Button */
	function exploreLoadMore(direction) {
		
		requestRunning ++;
		
    	var $form = $('#wfExplore');
    	var pagenumber;
    	var loadMorePreviousButton = null;
 
    	if (direction == 'up') {
    		$('.load-more-previous').html($('.loader').html());
    		exploreMinPageNumber = exploreMinPageNumber -1;
    		pagenumber = exploreMinPageNumber;
    		// incerment page number
    	} else {
        	$('.load-more').html($('.loader').html());
        	explorePageNumber = explorePageNumber +1;
    		pagenumber = explorePageNumber;
		}
        $('.loader').show();
        
    	$('#wfExplore input[name=page]').val(pagenumber);

        


        // Envoi de la requête HTTP en mode asynchrone
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: $form.serialize(),
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
        		$('.loader').hide();

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
        		changePageParameter('page', pagenumber);
        		requestRunning --;

            }
        });
    }

	function autoLoadOnScrollDown() {
		if (autoScrollDownEnable) return;
		
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
		if (autoScrollUpEnable) return;
		
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

	setHandlerOnRemoveTags();

});
