function create_lead_contact_action() {
    var phone_number = $('#phone1').val();
    
    var lead_source = $('#lead_source').val();

    if(lead_source.trim() == '-1'){
        alertMessage(this, 'red', '', 'Please select a lead source.');
        return false;
    }

    var data = {};
    data['first_name'] = $('#first_name').val();
    data['last_name'] = $('#last_name').val();
    data['lead_source'] = $('#lead_source').val();
    data['address1'] = $('#address1').val();
    data['address2'] = $('#address2').val();
    data['phone1'] = phone_number;
    data['phone2'] = $('#phone2').val();
    data['email'] = $('#email').val();
    data['status'] = $('#status').val();
    data['final_status'] = $('#final_status').val();
    data['do_area'] = $('#do_area').val();
    data['next_call_date'] = $('#next_call_date').val();
    data['feedback'] = $('#feedback').val();
    /*  var filter = /^[0-9-+]+$/;
     if (filter.test(phone_number)) {

     } else if(phone_number.length > 9 && phone_number.length < 12){
     alertMessage(this, 'red', '', 'Contact Number is not valid.');
     return false;
     }*/
    var mendatory_field = false;
    /*
    if (phone_number.length > 3) {
        if (phone_number.length > 7 && phone_number.length < 14) {
            mendatory_field = true;
        } else {
            alertMessage(this, 'red', '', 'Contact Number is not valid.');
            return false;
        }
    } */

    var p_regex = /^1\d{9}$/;
    var is_10_digit_number = p_regex.test(phone_number);

    if( !is_10_digit_number ){
        alertMessage(this, 'red', '', 'Contact Number is not valid.');
        return false;
    }
    
    
    if (data['email'].length > 6) {
        if (validateEmail(data['email'])) {
            mendatory_field = true;

        } else {
            alertMessage(this, 'red', '', 'Email is not valid.');
            return false;
        }
    }
    if (data['first_name'].length >= 2 || data['last_name'].length >= 2) {
        if (data['first_name'].length >= 4 || data['last_name'].length >= 4) {
            mendatory_field = true;
        } else {

            alertMessage(this, 'red', '', 'Please enter name. Name must be four character or more.');
            return false;
        }
    }
    if (mendatory_field == false) {
        alertMessage(this, 'red', '', 'Please enter name or phone no or email id.');
        return false;
    }


    var notifyme = $('#notifyme:checked').val();
    if (notifyme == 'on') {
        data['notifyme'] = 'yes';
    }
    else {
        data['notifyme'] = 'no';
    }
    var response = connectServer(cms_url['create_new_contact'], data, false);

    response = JSON.parse(response);
    if (response.status == 0 || response.status === '0') {
        alertMessage(this, 'green', 'Success', 'Successfully Registered');
        showUserMenu('contacts');
    }
    //else if (response == 1 || response.trim() === '1'){
    //    alertMessage(this, 'red', '', 'Contact Number already exists.');
    //}
    else {
        alertMessage(this, 'red', 'Failure', response.msg);
    }

}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}