/**
 * Created by Nazibul on 9/8/2015.
 */

function table_init_otrs_list() {
    var div_id = 'otrs_reportLoader';
    var div_name = "#" + div_id;
    var table_id = "task_list_table";
    $(div_name).html('<table class="table table-striped table-bordered table-hover responsive" id="' + table_id + '" width="100%">' +
        '<tr><td  align="center"></tr></table>');

    var cms_auth = checkSession('cms_auth');
    cms_auth = JSON.parse(cms_auth);

    var dataInfo = {};
    dataInfo['role'] = cms_auth.user_role;
    dataInfo['user_id'] = cms_auth.user_id;

    table_data_otrs_list(dataInfo, table_id);

}

function table_data_otrs_list(dataSet, table_id) {
    var table_name = "#" + table_id;
    $(table_name).dataTable({

        "processing": true,
        "serverSide": true,
        "ajax": {
            url: cms_url['otrs_list'],
            type: 'POST',
            data: {'info': dataSet}
        },
        "columns": [
            {"title": 'Filter', "data": "filter", "class": ""},
            {"title": 'Detail', "data": "contact_id", "class": ""},
            {"title": 'Ticket', "data": "ticket_number", "class": ""},
            {"title": 'Raise', "data": "raise_date", "class": ""},
            {"title": 'Connection', "data": "connection_due_date", "class": ""},
        ],
        "order": [[0, "asc"]],
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        'iDisplayLength': 30,
        "bSort": false,
        dom: 'rtip',
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "select",
                    "sButtonText": "Excel",
                    "fnClick": function (nButton, oConfig, oFlash) {
                        // window.open(cms_url['export_contacts_excel'] + "?type=closed", "_blank");
                    }
                },
                "csv"
            ],
            "filter": "applied"
        }
    });
}


function generate_all_otrs_ticket() {
    var datainfo = {};
    var data_input = {};
    var i = 0;
    var searchIDs = new Array();
    var searchIDs = $("input:checkbox:checked").map(function () {
        return $(this).val();
    }).get();

    var len = searchIDs.length;

    if (len != 0) {
        $.each(searchIDs, function (index, value) {
            data_input[i++] = value;
        });
    } else {
        return;
    }

    datainfo['id'] = {};
    datainfo['id'] = data_input;

    var auth_session_data = checkSession('cms_auth');
    var auth_data = JSON.parse(auth_session_data);

    datainfo['name'] = auth_data.first_name + " " + auth_data.last_name;
    datainfo['cur_time'] = return_local_time();
    var response = connectServer(cms_url['generate_all_ticket'], datainfo);
    response = JSON.parse(response);
    if (response.status) {
        alertMessage(this, 'green', '', 'Ticket Generated');
    } else {
        alertMessage(this, 'red', '', 'Failed.');
    }
}

function search_in_otrs() {

    var div_id = 'otrs_reportLoader';
    var div_name = "#" + div_id;
    var table_id = "task_list_table";
    $(div_name).html('<table class="table table-striped table-bordered table-hover responsive" id="' + table_id + '" width="100%">' +
        '<tr><td  align="center"></tr></table>');

    //var cms_auth = checkSession('cms_auth');
    //cms_auth = JSON.parse(cms_auth);

    var dataInfo = {};
    dataInfo['search_key'] = $('#otrsSearch').val();
    table_data_otrs_list(dataInfo, table_id);
}

function generate_ticket() {
    var a = $('#due_date').val();
    // a = a.replace(" ", "");
    if (a.length < 2 || a == null) {
        alertMessage(this, 'red', '', 'Please give Connection Due Date !!');
        return false;
    }
    try {
        conversion_history_controller();
    }
    catch (ex) {

    }
    var dataInfo = {};
    dataInfo['contact_id'] = $('#contact_id').val();
    try {
        var response = connectServer(cms_url['generate_ticket'], dataInfo);

        response = JSON.parse(response);

        if (response.status) {

            //   $('#raise_date').html(response.raise_date);
            $('#ticket_number').val(response.ticket_number);
            $('#ticket_agent').html(response.ticket_agent);

            alertMessage(this, 'green', '', "Ticket Generated");
        } else {
            alertMessage(this, 'red', '', "error :" + response.message);
        }
    }
    catch (ex) {
        alertMessage(this, 'red', '', "error :" + ex);
    }

}

function show_generated_orts_report() {
    var div_id = 'otrs_reportLoader';
    var div_name = "#" + div_id;
    var table_id = "task_list_table";
    $(div_name).html('<table class="table table-striped table-bordered table-hover responsive" id="' + table_id + '" width="100%">' +
        '<tr><td  align="center"></tr></table>');

    var cms_auth = checkSession('cms_auth');
    cms_auth = JSON.parse(cms_auth);

    var dataInfo = {};
    dataInfo['role'] = cms_auth.user_role;
    dataInfo['user_id'] = cms_auth.user_id;
    dataInfo['condtion_ticket'] = "yes";

    table_data_otrs_list(dataInfo, table_id);
}

function show_not_generated_orts_report() {
    var div_id = 'otrs_reportLoader';
    var div_name = "#" + div_id;
    var table_id = "task_list_table";
    $(div_name).html('<table class="table table-striped table-bordered table-hover responsive" id="' + table_id + '" width="100%">' +
        '<tr><td  align="center"></tr></table>');

    var cms_auth = checkSession('cms_auth');
    cms_auth = JSON.parse(cms_auth);

    var dataInfo = {};
    dataInfo['role'] = cms_auth.user_role;
    dataInfo['user_id'] = cms_auth.user_id;
    dataInfo['condtion_ticket'] = "not";

    table_data_otrs_list(dataInfo, table_id);
}

function show_date_filter_orts_report() {
    var div_id = 'otrs_reportLoader';
    var div_name = "#" + div_id;
    var table_id = "task_list_table";
    $(div_name).html('<table class="table table-striped table-bordered table-hover responsive" id="' + table_id + '" width="100%">' +
        '<tr><td  align="center"></tr></table>');

    var cms_auth = checkSession('cms_auth');
    cms_auth = JSON.parse(cms_auth);

    var dataInfo = {};
    dataInfo['role'] = cms_auth.user_role;
    dataInfo['user_id'] = cms_auth.user_id;
    dataInfo['date_form'] = $('#orts_start').val();
    dataInfo['date_to'] = $('#orts_end').val();

    table_data_otrs_list(dataInfo, table_id);
}