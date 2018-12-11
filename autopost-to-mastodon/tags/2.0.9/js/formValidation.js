jQuery(document).ready(function($) { 
	//Enable login button
	$("input[name=mastodon_instance_url]").keyup(function(){
        if($(this).val().length !=0){
            $('#userAuthButton').attr('disabled', false);
        }
        else
        {
            $('#userAuthButton').attr('disabled', true);        
        }
    });
});