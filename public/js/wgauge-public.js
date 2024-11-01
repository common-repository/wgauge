(function( $ ) {
	'use strict';

	$(function () {
		if (navigator.userAgent.indexOf('Safari') != -1 && 
		navigator.userAgent.indexOf('Chrome') == -1) {
			document.body.className += " safari";
		}
		var oneMonthFromNow = new Date((+new Date) + 2678400000);
		console.log(oneMonthFromNow);
		// If the "wGauge box" is on this page, let's setup the event handler
		if(1 === $('#wg-gauge-box').length) {
			console.log('wGauge Active');
		} // end if
		$('#wg_feedback').hide();
		$('#wg-thank-you').hide();

		$('#wg-gauge-attention').on('click', function(){
			$('#wg-gauge-box').slideToggle();
		});

		$('#wg-gauge-close').on('click', function(){
			$('#wg-gauge-box').slideToggle();
		});

		$("#wgauge-submit-feedback").on('click', function(){
			var wg_rating = $("#wgRangeInput").val();
			var wg_feedback = $("#wg_feedback").val();
			var wg_user = $("#wg_user").val();
			$.ajax({
				type: 'POST',   // Adding Post method
				url: wgAjax.ajax_url, // Including ajax file
				data: { // Sending data dname to post_word_count function.
					"action": "wg_submit_data", 
					"wg_rating":wg_rating,
					"wg_feedback":wg_feedback,
					"wg_user":wg_user,
				}, 
				success: function(data){ // Show returned data using the function.
					console.log(data);
					$('#wg-feedback-form').slideUp();
					$('#wg-thank-you').fadeIn();
					$('#wg-gauge-attention').hide();
					setCookie();
					
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			});
		});
		
		function setCookie() {
			var oneMonthFromNow = new Date((+new Date) + 2678400000);
			//document.cookie = "wgcompelte=true; expires=Thu, 01 Jan 1970 00:00:00 GMT";
			document.cookie = "wgcomplete=1; expires=" + oneMonthFromNow;
		}
		$('#wgauge-reveal-feedback').on('click',function(){
			$('#wg_feedback').slideDown();
			$(this).css({
				'margin-left': '0',
				'margin-right': '0',
				width: '0'
			}).on('transitionend', function(){
				$(this).remove();
			});
			
		});
		
		
	});


})( jQuery );
