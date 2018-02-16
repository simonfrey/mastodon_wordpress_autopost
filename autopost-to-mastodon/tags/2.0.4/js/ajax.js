jQuery(document).ready(function($) {       //wrapper
    $("#testConnectionButton").click(function() {        //event
        $("input").css("border", "");
        $("#testConnectionButton").css("animation", "glowing 2000ms infinite");
        $('#mAuthMessage').fadeOut('slow');


        console.log("Testconnection start request.");

       $.post(my_ajax_obj.ajax_url, {     //POST request
          _ajax_nonce:  my_ajax_obj.nonce, //nonce
            action: "test_connection"        //action
        }, function(data) {  
            console.log("Testconnection Serverresponse: "+data);
            $("#testConnectionButton").css("animation", "");

            var jsonResponse = JSON.parse(data);

            if(jsonResponse.status == "0"){
                $('#testConnectionMessage').html(notifications_obj.ok + "<br><a href='"+jsonResponse.serverURL+"' target='_blank'>"+jsonResponse.serverURL+"</a>");
            }else{
                $('#testConnectionMessage').html(notifications_obj.uncaught + ". 'mastodonautopost@l1am0.eu' <br>"+notifications_obj.errorDataMsg+"<br>"+notifications_obj.errorData+" <input value='"+data+"' onclick='this.select();'>" );
            }

        });
        
    });

    $("#userAuthButton").click(function() {        //event
        var serverURL = $("input[name=mastodon_instance_url]").val(); 
        $("input").css("border", "");
        $("#userAuthButton").css("animation", "glowing 2000ms infinite");
        
        $("#tokenEnterForm").fadeOut('slow');
        $('#testConnectionButton').attr('disabled', true);  
        $("#testConnectionMessage").fadeOut('slow');
        $('#testConnectionMessage').html("");  
        $('#mAuthMessage').fadeOut('slow');

          console.log("User Auth start request. ServerURL:"+serverURL);

       $.post(my_ajax_obj.ajax_url, {     //POST request
          _ajax_nonce: my_ajax_obj.nonce, //nonce
            action: "user_auth",        //action
            serverURL: serverURL           //data
        }, function(data) {  
            $("#userAuthButton").css("animation", "");
            console.log("User auth response: "+data);
            var jsonResponse = JSON.parse(data);

            if(jsonResponse.status == "0"){
                $('#tokenPopupURL').attr('href',jsonResponse.authUrl);
                $('#tokenPopupURL').html(jsonResponse.authUrl);
                $("#tokenEnterForm").fadeIn('slow');
                $("input[name='mastodon_server_token']").attr('value','');

                window.open(data, '_blank');
            }else{
                $("input[name=mastodon_server_url]").css("border", "2px solid red")
                $('#mAuthMessage').html(notifications_obj.notFound); 
                $("#mAuthMessage").fadeIn('slow');
            }
        });
        
    });


    $("#getBearerButton").click(function() {        //event
        var token = $("input[name=mastodon_server_token]").val(); 
         $("input").css("border", "");
        $("#getBearerButton").css("animation", "glowing 2000ms infinite");

        console.log("GetBearer start request.");

       $.post(my_ajax_obj.ajax_url, {     //POST request
          _ajax_nonce: my_ajax_obj.nonce, //nonce            
          action: "get_bearer",        //action
            token: token        //action
        }, function(data) {  
            $("#getBearerButton").css("animation", "");
            console.log("GetBearer response: "+data);

            var jsonResponse = JSON.parse(data);

            if(jsonResponse.status == "0"){
                $("#tokenEnterForm").fadeOut('slow');
                $('#testConnectionButton').attr('disabled', false);                
                $('#testConnectionMessage').html(notifications_obj.testmsg);
                $("#testConnectionMessage").fadeIn('slow');
                $('#mAuthMessage').fadeOut('slow');


            }else{                    
                $('#mAuthMessage').html(notifications_obj.uncaught + ". 'mastodonautopost@l1am0.eu' <br>"+notifications_obj.errorDataMsg+"<br>"+notifications_obj.errorData+" <input value='"+data+"' onclick='this.select();'>" );
                $('#mAuthMessage').fadeIn('slow');
                $('#testConnectionButton').attr('disabled', true);
                $("#testConnectionMessage").fadeOut('slow');    
                $("#tokenEnterForm").fadeOut('slow');    
                $('#testConnectionMessage').html("");    
            }
        });
    });
});

const getBearer = (token) =>{
        $("input").css("border", "");
        $("#userAuthButton").css("animation", "glowing 2000ms infinite");

       $.post(my_ajax_obj.ajax_url, {     //POST request
          _ajax_nonce: my_ajax_obj.nonce, //nonce            
          action: "get_bearer",        //action
            token: token        //action
        }, function(data) {  
            $("#userAuthButton").css("animation", "");
            if(data == 0){
                $('#testConnectionButton').attr('disabled', false);                
                $('#testConnectionMessage').html(notifications_obj.testmsg);    

            }else{                    
                $('#mAuthMessage').html(notifications_obj.uncaught);
                $('#testConnectionButton').attr('disabled', true);    
                $('#testConnectionMessage').html("");    
            }
        });
        
}

const isValidUrl = (string) => {
  try {
    new URL(string);
    return true;
  } catch (_) {
    return false;  
  }
}