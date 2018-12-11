jQuery(document).ready(function($) {       //wrapper
    $("#testConnectionButton").click(function() {        //event
        var serverURL = $("input[name=mastodon_server_url]").val(); 


        $("input").css("border", "");
        $("#testConnectionButton").css("animation", "glowing 2000ms infinite");

       $.post(my_ajax_obj.ajax_url, {     //POST request
          _ajax_nonce:  my_ajax_obj.nonce, //nonce
            action: "test_connection"        //action
        }, function(data) {  
            console.log(data);              //callback
            $("#testConnectionButton").css("animation", "");
            if(isValidUrl(data)){
                $('#testConnectionMessage').html(notifications_obj.ok + "<br><a href='"+data+"' target='_blank'>"+data+"</a>");
            }else{
                $('#testConnectionMessage').html(notifications_obj.uncaught + " 'mastodonautopost@l1am0.eu'");
            }

        });
        
    });

    $("#userAuthButton").click(function() {        //event
        var serverURL = $("input[name=mastodon_instance_url]").val(); 
        $("input").css("border", "");
        $("#userAuthButton").css("animation", "glowing 2000ms infinite");

       $.post(my_ajax_obj.ajax_url, {     //POST request
          _ajax_nonce: my_ajax_obj.nonce, //nonce
            action: "user_auth",        //action
            serverURL: serverURL           //data
        }, function(data) {  
            $("#userAuthButton").css("animation", "");

            if(isValidUrl(data)){
                window.open(data, '_blank');
                var token = prompt(notifications_obj.popup+": ", "");
                if(token != null){
                    getBearer(token);
                    $('#mAuthMessage').html("");                
                }else{
                    $('#mAuthMessage').html(notifications_obj.typeinpopup);                
                    $('#testConnectionButton').attr('disabled', true);                  
                    $('#testConnectionMessage').html("");    
                } 

            }else{
                $("input[name=mastodon_server_url]").css("border", "2px solid red")
                $('#testConnectionMessage').html(notifications_obj.notFound);    
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