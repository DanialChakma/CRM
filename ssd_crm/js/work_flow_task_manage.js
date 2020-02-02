/**
 * Created by Nazibul on 9/9/2015.
 */


function work_flow_task_page(customer_id, node_id, task_id) {

    if (node_id == 1 || node_id == '1') {

        var auth_session_data = checkSession('cms_auth');
        var auth_data = JSON.parse(auth_session_data);
        if ((auth_data.user_role).toLowerCase() == 'retail') {
            alertMessage(this, "red", "Access Denied !");
            return false;
        }

        showUserMenu('payment_collection');

        var dataSet = [[]];
        var dataInfo = {};
        dataInfo['id'] = customer_id;

        var response = connectServer(cms_url['payment_collection_record'], dataInfo);

        dataSet = JSON.parse(response);

        if (dataSet.id == null || dataSet.id == '') {
            $('#action').val('insert');
            $('#action_table').val('payments');
        } else {
            $('#action_key').val('id');
            $('#action_value').val(dataSet.id);
            $('#action').val('update');
            $('#action_table').val('payments');
        }

        $('#contact_id').val(customer_id);
        $('#doze_id').html(dataSet.doze_id);
        $('#receipt_number').val(dataSet.receipt_number);
        $('#collection_date').val(dataSet.collection_date);
        $('#collected_by').val(dataSet.collected_by);
        //$("#collected_by option[value='" + dataSet.collected_by + "']").attr('selected', true);
        $("#payment_mode option[value='" + dataSet.payment_mode + "']").attr('selected', true);
        $("#collection_status option[value='" + dataSet.collection_status + "']").attr('selected', true);

        $('#contact').html(dataSet.contact_id);
        $('#contact_name').html(dataSet.name);
        $('#contact_phone').html(dataSet.phone1);
        $('#contact_email').html(dataSet.email);
        $('#contact_address').html(dataSet.address1);

        if (task_id == undefined) {

        } else {
            $('#back_button').attr("onClick", "task_detail(" + task_id + ");");
        }
    } else if (node_id == 2 || node_id == '2') {

        var auth_session_data = checkSession('cms_auth');
        var auth_data = JSON.parse(auth_session_data);
        if ((auth_data.user_role).toLowerCase() == 'retail') {
            alertMessage(this, "red", "Access Denied !");
            return false;
        }

        showUserMenu('connection_install');

        var dataSet = [[]];
        var dataInfo = {};
        dataInfo['id'] = customer_id;

        var response = connectServer(cms_url['connection_install'], dataInfo);

        dataSet = JSON.parse(response);

        if (dataSet.id == null || dataSet.id == '') {
            $('#action').val('insert');
            $('#action_table').val('payments');
        } else {
            $('#action_key').val('id');
            $('#action_value').val(dataSet.id);
            $('#action').val('update');
            $('#action_table').val('otrs_ticket');
        }

        $('#connection_due_date').val(dataSet.connection_due_date);
        $('#raise_date').html(dataSet.raise_date);
        $('#ticket_number').html(dataSet.ticket_number);
        $('#ticket_agent_name').html(dataSet.ticket_agent);
        $("#status option[value='" + dataSet.status + "']").attr('selected', true);

        $('#contact_id').val(customer_id);
        $('#doze_id').html(dataSet.doze_id);
        $('#receipt_number').html(dataSet.receipt_number);
        $('#collection_date').html(dataSet.collection_date);
        $('#collected_by').html(dataSet.collected_by);
        $('#payment_mode').html(dataSet.payment_mode);
        $('#collection_status').html(dataSet.collection_status);

        $('#contact').html(dataSet.contact_id);
        $('#contact_name').html(dataSet.name);
        $('#contact_phone').html(dataSet.phone1);
        $('#contact_email').html(dataSet.email);
        $('#contact_address').html(dataSet.address1);

        var cms_auth = checkSession('cms_auth');
        cms_auth = JSON.parse(cms_auth);

        if (dataSet.ticket_agent == null || (dataSet.ticket_agent).trim() == '') {
            $('#ticket_agent').val(cms_auth.first_name + " " + cms_auth.last_name);
        }

        if (task_id == undefined) {

        } else {
            $('#back_button').attr("onClick", "task_detail(" + task_id + ");");
        }
    } else if (node_id == 3 || node_id == '3') {
        show_detail_lead(customer_id);
        $('#back_button').attr("onClick", "task_detail(" + task_id + ");");
        //$('.discard').hide();
        //$('#action_menu_contact_detail').append('<button id="back_button" title="Back" class="btn long" onclick="task_detail('+task_id+')"><img height="15px;" src="ssd_crm/img/undo11.png"></button>');
    }
}

function save_connection_install() {
    var response = connectServerWithForm(cms_url['database_editor_with_form'], 'otrs_collection_form');

    response = JSON.parse(response);

    if (response.status) {
        alertMessage(this, 'green', '', "success");
    } else {
        alertMessage(this, 'red', '', "error :" + response.message);
    }
}


function save_payment_collection() {
    var repeipt_no = $("#receipt_number").val();
    var contact_id = $("#contact").html();

    if (repeipt_no.trim() == "") {
        alertMessage(this, 'red', '', "Receipt Number Required");
        return false;
    }

    update_database_row("contacts", "customer_type", "customer", "id", contact_id);
    var response = connectServerWithForm(cms_url['database_editor_with_form'], 'payment_collection_form');

    response = JSON.parse(response);

    if (response.status) {
        alertMessage(this, 'green', '', "success");
    } else {
        alertMessage(this, 'red', '', "error :" + response.message);
    }
}