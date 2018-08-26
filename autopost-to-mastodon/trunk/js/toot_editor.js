jQuery(document).ready(function($) {           //wrapper
    $("#viewToot").click(function() {             //event
    	console.log("Start call to id "+$("#post_ID").val() );
        var this2 = this;                      //use in callback
        $.post(ajax_obj.ajax_url, {         //POST request
           _ajax_nonce: ajax_obj.nonce,     //nonce
            action: "get_toot_preview",
            post_ID: $("#post_ID").val()              //data
        }, function(data) {                    //callback
            console.log(data);              //insert server response
        });
    });
});