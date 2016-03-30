


$( document ).ready(function () {

	var $explorePageNumber = 1;
	
	/* clique sur une label recherché : 
	 * deselection du label, et ressoumission du formulaire
	 */
	 function setHandlerOnRemoveTags() {
		$( ".wfexplore-selectedLabels .tag .remove" ).click(function (item) {
			

			inputID = $( this ) . attr('data-inputId');

			$('#Label' + inputID).button('toggle');
			$( this ).parent().hide();

			$("#wfExplore").submit();

		});
	}

	/* soumission du formulaire à chaque changement de filtre */
	$("#wfExplore input[type=checkbox]").change(function () {

		$("#wfExplore").submit();
    });

	/* soumission du formulaire en ajax */
    $('#wfExplore').on('submit', function(e) {
        e.preventDefault(); // J'empêche le comportement par défaut du navigateur, c-à-d de soumettre le formulaire
 
        var $this = $(this); // L'objet jQuery du formulaire

        $explorePageNumber = 1;
        $('#wfExplore input[name=page]').val($explorePageNumber);
 
        $('.loader').show();
        // Envoi de la requête HTTP en mode asynchrone
        $.ajax({
            url: $this.attr('action'),
            type: $this.attr('method'),
            data: $this.serialize(),
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

				setHandlerOnRemoveTags();
        		$('.loader').hide();
        		$('.load-more').on('click', exploreLoadMore);

            }
        });
    });

	

	/* Load More Button */
	function exploreLoadMore(e) {

    	var $form = $('#wfExplore');
 
        $('.load-more').html($('.loader').html());
        $('.loader').show();

		// incerment page number
        $explorePageNumber = $explorePageNumber +1;
        $('#wfExplore input[name=page]').val($explorePageNumber);


        // Envoi de la requête HTTP en mode asynchrone
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: $form.serialize(),
            success: function(html) {
                var $data = $(html);

                // get .searchresults div content from result
				var wfExplore = $data.find('.searchresults').contents();
				// remove old button
				$('.load-more').remove();
				// append to .searchresults div content in dom
				$('.searchresults').append(wfExplore);

                // idem for get .wfexplore-selectedLabels div content 
				wfExplore = $data.find('.wfexplore-selectedLabels').contents();
				// replace .wfexplore-selectedLabels div content in dom
				$('.wfexplore-selectedLabels').empty();  
				$('.wfexplore-selectedLabels').append(wfExplore);

				setHandlerOnRemoveTags();
        		$('.loader').hide();
        		$('.load-more').on('click', exploreLoadMore);

            }
        });
    }

    $('.load-more').on('click', exploreLoadMore);

	setHandlerOnRemoveTags();

});
