function user_add_action()
{
   
   
   var data = {};
    data['user_id'] = $('#user_id').val();
    data['user_name'] = $('#user_name').val();
      data['working_schedule'] = $('#working_schedule').val();  
      data['user_password'] = $('#user_password').val();  
      data['user_address'] = $('#user_address').val();  
      data['retype_user_password'] = $('#retype_user_password').val();  
      data['user_phone'] = $('#user_phone').val();  
      data['first_name'] = $('#first_name').val();  
      data['user_alt_phone'] = $('#user_alt_phone').val();  
      data['last_name'] = $('#last_name').val();  
      data['user_role'] = $('#user_role').val();
      data['user_email'] = $('#user_email').val();
	
	
    var retype_password = $('#retype_user_password').val().trim();
	var password = $('#user_password').val().trim();
    if(password==retype_password){
         var res = connectServer(cms_url['add_user'], data,false);
		 res = JSON.parse(res);
		 if(res.status == "yes"){
		    alertMessage(this, 'green', 'Success', 'Successful');
            showUserMenu('agent_table');
		 }else{
			alertMessage(this, 'red', 'Failure', 'Failed!!'); 
		 }
    }
    else{ 
        alertMessage(this, 'red', 'Failure', 'User Password and Retype User Password Fields do not  match');
    }
}