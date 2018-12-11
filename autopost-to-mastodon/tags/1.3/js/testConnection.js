jQuery(document).ready(function($) {       //wrapper
    $("#testConnectionButton").click(function() {        //event
        var serverURL = $("input[name=mastodon_server_url]").val();
        var email = $("input[name=mastodon_email]").val();
        var password = $("input[name=mastodon_password]").val();  
        console.log('Send');
        $("input").css("border", "");
        $("#testConnectionButton").css("animation", "glowing 2000ms infinite");

       $.post(my_ajax_obj.ajax_url, {     //POST request
           _ajax_nonce: my_ajax_obj.nonce, //nonce
            action: "test_connection",        //action
            serverURL: serverURL,              //data
            email: email,             //data
            password: password              //data
        }, function(data) {  
            console.log(data);              //callback
            $("#testConnectionButton").css("animation", "");

            switch (data) {
                case '404':
                    $("input[name=mastodon_server_url]").css("border", "2px solid red")
                    $('#testConnectionMessage').html(notifications_obj.notFound);    
                    break;
                case '401':
                    $("input[name=mastodon_email]").css("border", "2px solid red")
                    $("input[name=mastodon_password]").css("border", "2px solid red")
                    $('#testConnectionMessage').html(notifications_obj.noAccess);                    
                    break;
                case '999':
                    $("input").css("border", "2px solid red")
                    $('#testConnectionMessage').html(notifications_obj.uncaught);                    break;
                case '200':
                    $("#testConnectionButton").css("border", "2px solid green")
                    $('#testConnectionMessage').html(notifications_obj.ok);    
                break;
            }
        });
        
    });
});