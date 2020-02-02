/**
 * Created by Nazibul on 8/27/2015.
 */

function workflowPluginLoad() {
    setTimeout(function () {
        var icons = {
            header: "ui-icon-circle-arrow-e",
            activeHeader: "ui-icon-circle-arrow-s"
        };
        $("#accordion").accordion({
            // icons: icons
        });
        //$( "#toggle" ).button().click(function() {
        //    if ( $( "#accordion" ).accordion( "option", "icons" ) ) {
        //        $( "#accordion" ).accordion( "option", "icons", null );
        //    } else {
        //        $( "#accordion" ).accordion( "option", "icons", icons );
        //    }
        //});
    }, 0);
}

function work_flow_name_op() {

    $('#work_flow_name_new_button').hide();
    //$('#work_flow_name_select').attr('disabled', true);
    $('#work_flow_name_select').hide();
    $('#work_flow_name_select_button').show();
    $('#work_flow_name').show();
    $('#work_flow_name').attr('disabled', false);
    $('#work_flow_def_submit').val('Save');
    $('#workflow_def_form input[name="action"]').val('insert');
    $('#work_flow_name_select').html('<option value="0">--Select--</option>');

    $('#work_flow_name').val('');
    $('#description').val('');
    $('#require_days').val('');
    $('#follow_up_before').val('');
    $('#follow_up_mail').val('');

}

function work_flow_select_op() {

    //$('#work_flow_name').attr('disabled', true);
    $('#work_flow_name').hide();
    $('#work_flow_name_select_button').hide();
    $('#work_flow_name_new_button').show();
    $('#work_flow_name_select').show();
    $('#work_flow_name_select').attr('disabled', false);
    fetchDropDownOption('#work_flow_name_select', cms_url['load_workflow_list'], '');
    $('#work_flow_def_submit').val('Update');
    $('#workflow_def_form input[name="action"]').val('update');
    //$('#action_key').val('id');
    //$('#action_value').val('update');
}

function load_workflow_def_details() {

    var dataSet = [[]];
    var dataInfo = {};

    dataInfo['id'] = $('#work_flow_name_select').val();

    var response = connectServer(cms_url['load_workflow_def_details'], dataInfo);

    dataSet = JSON.parse(response);

    $('#workflow_def_form input[name="action_key"]').val('id');
    $('#workflow_def_form input[name="action_value"]').val(dataSet.id);
    $('#work_flow_name').val(dataSet.work_flow_name);
    $('#description').val(dataSet.description);
    $("#status option[value='" + dataSet.status + "']").attr('selected', true);
    $('#require_days').val(dataSet.require_days);
    $('#follow_up_before').val(dataSet.follow_up_before);
    $('#follow_up_mail').val(dataSet.follow_up_mail);
    $("#notify_over_SMS option[value='" + dataSet.notify_over_SMS + "']").attr('selected', true);
    $("#email_text option[value='" + dataSet.email_text + "']").attr('selected', true);
    $("#sms_text option[value='" + dataSet.sms_text + "']").attr('selected', true);

}

function worf_flow_def_save() {
    //alert('1');
    $('#work_flow_name_select').attr('disabled', true);
    var response = connectServerWithForm(cms_url['database_editor_with_form'], 'workflow_def_form');
    response = JSON.parse(response);
    $('#work_flow_name_select').attr('disabled', false);

    if (response.status) {
        showUserMenu('workflow');
        alertMessage(this, 'green', '', 'Successful');
    } else {
        alertMessage(this, 'red', '', 'Failed');
    }

}

function worf_flow_def_del() {
    $('#workflow_def_form input[name="action"]').val('delete');
    var response = connectServerWithForm(cms_url['worf_flow_def_save'], 'workflow_def_form');
    response = JSON.parse(response);

    if (response.status) {
        var dataStr = delete_database_row("work_flow_details", "work_flow_id", $('#action_value').val());
        showUserMenu('workflow');
        alertMessage(this, 'green', '', 'Successful');

    }
}

function work_flow_detail_op() {
    var action = $('#node_name_button').val();
    if (action == 'Select') {

        var dataInfo = {};
        dataInfo['id'] = $('#work_flow_id').val();
        fetchDropDownOption('#node_name_select', cms_url['load_workflow_detail_list'], dataInfo);

        $('#node_name_button').val('New');
        $('#work_flow_detail_submit').val('Update');
        $('#workflow_detail_form input[name="action"]').val('update');
        $('#node_name_select').show();
        $('#node_name').hide();
        $('#node_name').attr('disabled', true);
    } else {
        $('#node_name_select').html('<option value="0">--Select--</option>');
        $('#node_name_button').val('Select');
        $('#work_flow_detail_submit').val('Save');
        $('#workflow_detail_form input[name="action"]').val('insert');
        $('#node_name_select').hide();
        $('#node_name').attr('disabled', false);
        $('#node_name').show();
    }
}

function load_workflow_details() {
    var node_id = $('#node_name_select').val();

    var dataSet = [[]];
    var dataInfo = {};

    dataInfo['id'] = node_id;

    var response = connectServer(cms_url['workflow_detail_details'], dataInfo);

    dataSet = JSON.parse(response);

    $('#workflow_detail_form input[name="action_key"]').val('id');
    $('#workflow_detail_form input[name="action_value"]').val(dataSet.id);

    $('#node_name').val(dataSet.node_name);
    $('#node_id_nxt').val(dataSet.node_id_nxt);
    $("#list_id option[value='" + dataSet.list_id + "']").attr('selected', true);
    $("#approval_type option[value='" + dataSet.approval_type + "']").attr('selected', true);
    $("#task_id option[value='" + dataSet.task_id + "']").attr('selected', true);
    $('#approval_node_id').val(dataSet.approval_node_id);
    $('#rejected_node_id').val(dataSet.rejected_node_id);
}

function work_flow_detail_save() {

    var response = connectServerWithForm(cms_url['database_editor_with_form'], 'workflow_detail_form');

    response = JSON.parse(response);

    if (response.status) {
        showUserMenu('workflow');
        alertMessage(this, 'green', '', 'Successful');
    } else {
        alertMessage(this, 'red', '', 'Failed');
    }
}

function work_flow_detail_del() {
    $('#workflow_detail_form input[name="action"]').val('delete');
    var response = connectServerWithForm(cms_url['database_editor_with_form'], 'workflow_detail_form');

    response = JSON.parse(response);

    if (response.status) {
        showUserMenu('workflow');
        alertMessage(this, 'green', '', 'Successful');
    } else {
        alertMessage(this, 'red', '', 'Failed');
    }
}

function work_flow_user_list_op() {
    var action = $('#node_list_user_button').val();
    if (action == 'Select') {

        var dataInfo = {};
        dataInfo['id'] = $('#work_flow_id').val();

        $('#user_list_id').show();
        $('#node_list_user_button').val('New');
        $('#work_flow_list_submit').val('Update');
        $('#select_user_div').hide();
        $('#group_name_input').hide();
        fetchDropDownOption('#user_list_id', cms_url['get_group_list'], '');
        work_flow_list_node();
    } else {
        $('#user_list_id').html('<option value="0">--Select--</option>');
        $('#node_list_user_button').val('Select');
        $('#work_flow_list_submit').val('Save');
        $('#select_user_div').show();
        $('#user_list_id').hide();
        $('#group_name_input').show();

        var innerHtml = connectServer(cms_url['workflow_user_list'], '');
        $('#select_user_div_list').html(innerHtml);
    }
}

function work_flow_list_list() {
    $('#node_name_list').html('<option value="0">--Select--</option>');
    var dataInfo = {};
    dataInfo['id'] = $('#work_flow_id_list').val();
    fetchDropDownOption('#node_name_list', cms_url['load_workflow_detail_list'], dataInfo);
}

function work_flow_list_node() {
    var node_id = $('#node_name_list').val();

    var dataSet = [[]];
    var dataInfo = {};

    dataInfo['id'] = node_id;
    var response = connectServer(cms_url['workflow_detail_details'], dataInfo);

    dataSet = JSON.parse(response);
    if (dataSet.list_id == null || dataSet.list_id == 'null' || dataSet.list_id == '') {
        dataSet.list_id = 0;
    }

    $('#user_list_id option').prop('selected', false).filter('[value="' + dataSet.list_id + '"]').prop('selected', true);
}

function work_flow_list_user_save() {

    var dataSet = [[]];
    var dataInfo = {};

    var action = $('#node_list_user_button').val();

    dataInfo['action'] = action;
    dataInfo['work_flow_id_list'] = $('#work_flow_id_list').val();
    dataInfo['node_name_list'] = $('#node_name_list').val();
    dataInfo['user_list_id'] = $('#user_list_id').val();

    if (action == 'Select') {
        var i = 0;
        var sThisVal = {};
        $('input.subjectid[type=checkbox]').each(function () {
            if (this.checked) {
                sThisVal[i] = $(this).val();
                i++;
            }
        });
        dataInfo['checked'] = sThisVal;
        dataInfo['name'] = $('#group_name_input').val();
    }

    //console.log(dataInfo);
    dataSet = JSON.parse(connectServer(cms_url['save_workflow_user_list'], dataInfo));
    if (dataSet.status) {
        showUserMenu('workflow');
        alertMessage(this, 'green', '', dataSet.message);
    } else {
        alertMessage(this, 'red', '', dataSet.message);
    }
}