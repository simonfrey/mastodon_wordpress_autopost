jQuery(document).ready(function($) {  
	//Select the right radio button for message on load
	let textareaValue = $('textarea[name=message]').val();
	$('input:radio[name=message_template]').prop("checked",false);
	$('input[value="'+textareaValue+'"]').prop("checked", true);	

	$('input:radio').parent().css({ opacity: 0.5 });;
	$('input:radio:checked').parent().css({ opacity: 1 });;
	
	$('textarea[name=message]').change(function(){
		$('input:radio[name=message_template]').prop("checked",false);
		$('input[value="'+this.value+'"]').prop("checked", true);	
	});
	
	//Show advanced config
	$("#show_advanced_configuration").click(function(){ 
			$(".not_advanced_setting").fadeOut("fast");
			$("td.advanced_setting").fadeIn("slow");
			$("tr.advanced_setting").fadeIn("slow").css("display","block");
			$("#hide_advanced_configuration").removeClass("active");
			$("#show_advanced_configuration").addClass("active");
	});

	//Hide advanced config
	$("#hide_advanced_configuration").click(function(){ 
			$(".advanced_setting").fadeOut("fast");
			$(".not_advanced_setting").fadeIn("slow");
			$("#show_advanced_configuration").removeClass("active");
			$("#hide_advanced_configuration").addClass("active");
	});
	//Set the message value on radio select
	$('input:radio[name=message_template]').change(function(){
			let value = $('input:radio[name=message_template]:checked').val();
			$('textarea[name=message]').val(value);
	});

	$('input:radio').change(function(){
			$('input:radio').parent().css({ opacity: 0.5 });;
			$('input:radio:checked').parent().css({ opacity: 1 });;
	});
});
