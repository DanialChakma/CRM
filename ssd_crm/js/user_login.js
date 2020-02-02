/**
 * Created by Mazhar on 3/10/2015.
 */

function check_user_session() {

    var returnValue = connectServer(cms_url['user_session']);

    if (returnValue) {
        try {
            returnValue = JSON.parse(returnValue);

            if (returnValue.status) {
                var auth_session_data = JSON.stringify(returnValue.read);
                setSession(auth_session_data, 'cms_auth');
            }
        } catch (e) {
            $.each(sessionStorage, function (ind, val) {
                destroySession(ind);
            });
        }
    }
}

function user_login_action() {

    var return_value = connectServerWithForm(cms_url['user_login'], 'user_login_form');
    try {
        return_value = JSON.parse(return_value);
        if (return_value.status) {
            var auth_session_data = JSON.stringify(return_value.read);
            setSession(auth_session_data, 'cms_auth');
            //alert(auth_session_data);
            // alert(site_host);
            //defaultViewController();
            create_auto_today_task();
            redirect_to(site_host);
        } else {
            alertMessage(this, 'red', 'Failure', 'Login Failed.');
        }
    } catch (exception) {
        alertMessage(this, 'red', 'Failure', 'Login Failed!');
    }
    clear_droupdown_data();
}

// ####################### Check php Session #########
function check_user_session() {

    var return_value = connectServer(cms_url['user_session']);

    if (return_value) {
        try {
            return_value = JSON.parse(return_value);

            if (return_value.status) {
                var auth_session_data = JSON.stringify(return_value.read);
                setSession(auth_session_data, 'cms_auth');
            }
        } catch (e) {
            $.each(sessionStorage, function (ind, val) {
                destroySession(ind);
            });
        }
    }
}
// ####################### UserLogoutAction #########
function user_logout_action() {
    connectServer(cms_url['user_log_out']);
    redirect_to(site_host);
    return false;
}
