jQuery(document).ready(function() {	
	UPD8FeedbackAddRow()
});

function UPD8FeedbackAddRow() {
	var question = jQuery('.upd8-feedback-form-editor .questions .row:last-child').clone(),
		textarea = jQuery('textarea', question).val('');
		
		
	jQuery('.upd8-feedback-form-editor .color-picker').wpColorPicker();
	
	jQuery('label .nr', question).html( parseInt(jQuery('label .nr', question).html())+1 );

	jQuery('.upd8-feedback-form-editor .questions').append(question);

	textarea.keyup(function() {
		var next = jQuery(this).parent().next('.row').children('textarea');
		if( jQuery(this).val() && jQuery(this).val() !== '' && next.length == 0 ) {
			UPD8FeedbackAddRow();
		}
	});
}