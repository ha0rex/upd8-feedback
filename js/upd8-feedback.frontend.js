var nr_of_questions,
	width_of_module,
	step,
	last_step;
	
jQuery(document).ready(function() {
	nr_of_questions = jQuery('.upd8-feedback .inner-wrapper .questions > li').length;
	step = 0,
	last_step = jQuery('.upd8-feedback .inner-wrapper .questions > li').length + 2;
	
	
	UPD8FeedbackResize();
	
	jQuery( window ).resize(function() {
		UPD8FeedbackResize();
	});
	
	jQuery('.upd8-feedback .start-quiz').click(function() {
		jQuery('.upd8-feedback .inner-wrapper').animate({
			marginLeft: '-='+width_of_module+'px'
		}, 600);
		step++;
	});
	
	jQuery('.upd8-feedback .finish-quiz').click(function() {
		window.location.href = '/';
	});
	
	jQuery('.upd8-feedback .inner-wrapper .questions > li').each(function() {
		var stars = [],
			stars_ul = jQuery('<ul class="stars"></ul>'),
			input = jQuery('.answer', this),
			prev_btn = jQuery('.buttons_container .prev', this),
			next_btn = jQuery('.buttons_container .next', this);
		
		for(var i=1; i<6; i++) {
			stars[i] =  jQuery('<li><a data-value="'+i+'">★</a></li>');
			stars_ul.append(stars[i]);
			stars[i].click(function() {
				for( var j=0; j<=jQuery('a', this).attr('data-value'); j++ ) {
					jQuery('li:nth-child('+(j)+')', stars_ul).addClass('active');
				}
				input.val(jQuery('a', this).attr('data-value'));
				setTimeout(function() {
					jQuery('.upd8-feedback .inner-wrapper').animate({
						marginLeft: '-='+width_of_module+'px'
					}, 600);
					step++;
					if( step == last_step-1 ) {
						UPD8FeedbackSendResults();
					}
				}, 1000);
			});
			
			stars[i].mouseover(function() {
				for( var j=0; j<=jQuery('a', this).attr('data-value'); j++ ) {
					jQuery('li:nth-child('+(j)+')', stars_ul).addClass('hover');
				}
			}).mouseout(function() {
				jQuery('li', stars_ul).removeClass('hover');
			})
		}
		stars_ul.insertBefore(jQuery('.buttons_container', this));
		
		prev_btn.click(function() {
			jQuery('.upd8-feedback .inner-wrapper').animate({
				marginLeft: '+='+width_of_module+'px'
			}, 600);
			step--;		
		});	
		
		next_btn.click(function() {
			jQuery('.upd8-feedback .inner-wrapper').animate({
				marginLeft: '-='+width_of_module+'px'
			}, 600);	
			step++;	
			if( step == last_step-1 ) {
				UPD8FeedbackSendResults();
			}
		});		
	});
});

function UPD8FeedbackResize() {
	width_of_module = jQuery('.upd8-feedback').width();
	jQuery('.upd8-feedback .inner-wrapper').css('width', width_of_module*(nr_of_questions+2)+100);
	jQuery('.upd8-feedback .inner-wrapper .questions > li, .upd8-feedback .prologue, .upd8-feedback .conclusion').css('width', width_of_module);
	jQuery('.upd8-feedback .inner-wrapper').css({
		marginLeft: -(width_of_module*(step))+'px'
	});
}

function UPD8FeedbackSendResults() {
	var answers = [];
	jQuery('.upd8-feedback .inner-wrapper .questions > li input[name="answers[]"]').each(function() {
		answers.push(jQuery(this).val());
	});
	jQuery.ajax(
		{
			type: "post",
			dataType: "json",
			url: ajax_url,
			data: {
				action: 'upd8_form_fill',
				form_id: jQuery('.upd8-feedback').attr('data-form-id'),
				answers: answers
			},
			success: function(msg){
				console.log(msg);
			}
		});	
}