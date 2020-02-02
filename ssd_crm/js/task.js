/**
 * Created by Nazibul on 8/31/2015.
 */

function add_task_with_contact_list() {
    //alert(1);
    var data_input = {};
    var idListString = "";
    var i = 0;
    var searchIDs = new Array();
    var searchIDs = $("input:checkbox:checked").map(function () {
        return $(this).val();
    }).get();

    var len = searchIDs.length;
    var separator = "";
    if (len != 0) {
        $.each(searchIDs, function (index, value) {
            //data_input[i++] = value;
            idListString += separator + value;
            separator = "|";
        });
    } else {
        return;
    }

    showUserMenu('add_task');

    $('#contact_id_list').val(idListString);

}

function add_to_predefined_task() {
    //alert(1);
    var data_input = {};
    var idListString = "";
    var i = 0;
    var searchIDs = new Array();
    var searchIDs = $("input:checkbox:checked").map(function () {
        return $(this).val();
    }).get();

    var len = searchIDs.length;
    var separator = "";
    if (len != 0) {
        $.each(searchIDs, function (index, value) {
            //data_input[i++] = value;
            idListString += separator + value;
            separator = "|";
        });
    } else {
        return;
    }

    showUserMenu('add_defined_task');

    $('#contact_id_list').val(idListString);

}

function insert_contact_to_task() {
    var dataSet = [[]];
    var dataInfo = {};

    dataInfo['task_id_select'] = $('#task_id_select').val();
    dataInfo['contact_id_list'] = $('#contact_id_list').val();

    var response = connectServer(cms_url['add_contact_to_task'], dataInfo);

    dataSet = JSON.parse(response);

    if (dataSet.status) {
        alertMessage(this, 'green', '', dataSet.msg);
        task_detail($('#task_id_select').val());
    } else {
        alertMessage(this, 'red', '', dataSet.msg);
    }
}

function load_task_detail() {
    var id = $('#task_id_select').val();

    var dataSet = [[]];
    var dataInfo = {};

    dataInfo['id'] = id;

    var response = connectServer(cms_url['task_detail'], dataInfo);

    dataSet = JSON.parse(response);

    $('#task_title').html(dataSet.task_title);
    $('#assignTo').html(dataSet.assign_to);
    $('#user_group_id').html(dataSet.user_group_id);
    $('#catagory').html(dataSet.node_name);
    $('#priority').html(dataSet.task_title);
    $('#status').html(dataSet.task_status);
    $('#description').html(dataSet.task_description);
    $('#due_date').html(dataSet.due_date);

    $('#task_title').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "task_title", value, "id", dataSet.id);
            return (value);
        }
        , {
            type: 'text',
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
        }
    );

    $('#assignTo').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "assign_to", value, "id", dataSet.id);
            return ($(this).find('option:selected').text());
        }
        , {
            type: 'select',
            //data: " {'E':'Letter E','F':'Letter F','G':'Letter G', 'selected':'" + dataSet.assign_to + "'}",
            loadurl: cms_url['doze_crm_sales_representative_list'] + "?for_inline=1",
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
        }
    );

    $('#user_group_id').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "user_group_id", value, "id", dataSet.id);
            return ($(this).find('option:selected').text());
        }
        , {
            type: 'select',
            //data: " {'E':'Letter E','F':'Letter F','G':'Letter G', 'selected':'" + dataSet.assign_to + "'}",
            loadurl: cms_url['get_group_list'] + "?for_inline=1",
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
        }
    );

    $('#catagory').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "work_flow_id", value, "id", dataSet.id);
            return ($(this).find('option:selected').text());
        }
        , {
            type: 'select',
            //data: " {'E':'Letter E','F':'Letter F','G':'Letter G', 'selected':'F'}",
            loadurl: cms_url['load_workflow_list'] + "?for_inline=1",
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
        }
    );

    $('#due_date').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "due_date", value, "id", dataSet.id);
            return (value);
        }
        , {
            type: 'datetime',
            //cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            //submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
            // cssclass: 'datePickerInline'
        }
    );

    $('#progress').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "progress_report", value, "id", dataSet.id);
            return ($(this).find('option:selected').text());
        }
        , {
            type: 'select',
            data: " {'25':'25%','50':'50%','75':'75%','100':'100%','selected':'25%'}",
            //loadurl : 'http://www.example.com/json.php',
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
        }
    );

    $('#priority').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "task_title", value, "id", dataSet.id);
            return ($(this).find('option:selected').text());
        }
        , {
            type: 'select',
            data: " {'1':'1','2':'2','3':'3', 'selected':'1'}",
            //loadurl : 'http://www.example.com/json.php',
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
        }
    );

    $('#status').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "task_status", value, "id", dataSet.id);
            return ($(this).find('option:selected').text());
        }
        , {
            type: 'select',
            data: " {'new':'New','processing':'Processing','done':'Done', 'selected':'new'}",
            //loadurl : 'http://www.example.com/json.php',
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
        }
    );

    $('#description').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "task_description", value, "id", dataSet.id);
            return (value);
        }
        , {
            type: 'textarea',
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            //indicator : '<img src="../img/save-512.png">',
            tooltip: 'Click to edit...',
            cssclass: 'description'
        }
    );
}

function create_task() {
    var dataSet = [[]];
    var dataInfo = {};

    dataInfo['task_name'] = $('#task_name').val();
    dataInfo['task_catagory'] = $('#task_catagory').val();
    dataInfo['assignto'] = $('#assignto').val();
    dataInfo['user_group_id'] = $('#user_group_id').val();
    dataInfo['due_date'] = $('#due_date').val();
    dataInfo['TaskAddDetails'] = $('#TaskAddDetails').val();
    dataInfo['contact_id_list'] = $('#contact_id_list').val();

    var response = connectServer(cms_url['add_new_task'], dataInfo);

    dataSet = JSON.parse(response);

    if (dataSet.status) {
        alertMessage(this, 'green', '', dataSet.msg);
        showUserMenu('task_list');
    } else {
        alertMessage(this, 'red', '', dataSet.msg);
    }

}

function create_auto_today_task() {
    var dataSet = [[]];
    var dataInfo = {};

    //dataInfo['task_name'] = $('#task_name').val();
    //dataInfo['task_catagory'] = $('#task_catagory').val();
    //dataInfo['assignto'] = $('#assignto').val();
    //dataInfo['user_group_id'] = $('#user_group_id').val();
    //dataInfo['due_date'] = $('#due_date').val();
    //dataInfo['TaskAddDetails'] = $('#TaskAddDetails').val();
    //dataInfo['contact_id_list'] = $('#contact_id_list').val();

    var response = connectServer(cms_url['add_auto_today_task'], dataInfo);
    try {
        dataSet = JSON.parse(response);
        if (dataSet.status) {
            localStorage.add_auto_today_task='task_created';
        //    alertMessage(this, 'green', '', dataSet.msg);
        //    showUserMenu('task_list');
         } //else {
        //    alertMessage(this, 'red', '', dataSet.msg);
        //}
    } catch (ex) {

    }


}

function table_init_task_list(div_id) {
    var div_name = "#" + div_id;
    var table_id = "task_list_table";
    $(div_name).html('<table class="table table-striped table-bordered table-hover responsive" id="' + table_id + '" width="100%">' +
        '<tr><td  align="center"></tr></table>');

    var cms_auth = checkSession('cms_auth');
    cms_auth = JSON.parse(cms_auth);

    var dataInfo = {};
    dataInfo['role'] = cms_auth.user_role;
    dataInfo['user_id'] = cms_auth.user_id;

    table_data_task_list(dataInfo, table_id);

}

function table_data_task_list(dataSet, table_id) {
    var table_name = "#" + table_id;
    $(table_name).dataTable({

        "processing": true,
        "serverSide": true,
        "ajax": {
            url: cms_url['task_list'],
            type: 'POST',
            data: {'info': dataSet}
        },
        "columns": [
            {"data": "filter", "class": "filter"},
            //{"data": "catagory", "class": "catagory"},
            //{"data": "title", "class": "title"},
            //{"data": "action", "class": "action"},
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

function task_detail(id) {

    var dataSet = [[]];
    var dataInfo = {};

    dataInfo['id'] = id;

    var response = connectServer(cms_url['task_detail'], dataInfo);

    dataSet = JSON.parse(response);

    showUserMenu('task_detail');
    //alert(dataSet.task_title);
    $('#task_id').html(dataSet.id);
    $('#task_title').html(dataSet.task_title);
    $('#assignTo').html(dataSet.assign_to);
    $('#user_group_id').html(dataSet.user_group_id);
    $('#catagory').html(dataSet.work_flow_name);
    $('#node_name').html(dataSet.node_name);
    $('#progress').html(dataSet.progress_report);
    $('#priority').html(dataSet.task_title);
    $('#status').html(dataSet.task_status);
    $('#description').html(dataSet.task_description);
    $('#last_updated').html(dataSet.update_date);
    $('#created_by').html(dataSet.assign_by);
    $('#due_date').html(dataSet.due_date);

    $('#task_title').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "task_title", value, "id", dataSet.id);
            return (value);
        }
        , {
            type: 'text',
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
        }
    );

    $('#assignTo').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "assign_to", value, "id", dataSet.id);
            return ($(this).find('option:selected').text());
        }
        , {
            type: 'select',
            //data: " {'E':'Letter E','F':'Letter F','G':'Letter G', 'selected':'" + dataSet.assign_to + "'}",
            loadurl: cms_url['doze_crm_sales_representative_list'] + "?for_inline=1",
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
        }
    );

    $('#user_group_id').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "user_group_id", value, "id", dataSet.id);
            return ($(this).find('option:selected').text());
        }
        , {
            type: 'select',
            //data: " {'E':'Letter E','F':'Letter F','G':'Letter G', 'selected':'" + dataSet.assign_to + "'}",
            loadurl: cms_url['get_group_list'] + "?for_inline=1",
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
        }
    );

    $('#catagory').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "work_flow_id", value, "id", dataSet.id);
            return ($(this).find('option:selected').text());
        }
        , {
            type: 'select',
            //data: " {'E':'Letter E','F':'Letter F','G':'Letter G', 'selected':'F'}",
            loadurl: cms_url['load_workflow_list'] + "?for_inline=1",
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
        }
    );

    $('#due_date').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "due_date", value, "id", dataSet.id);
            return (value);
        }
        , {
            type: 'datetime',
            //cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            //submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
            // cssclass: 'datePickerInline'
        }
    );

    $('#progress').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "progress_report", value, "id", dataSet.id);
            return ($(this).find('option:selected').text());
        }
        , {
            type: 'select',
            data: " {'25':'25%','50':'50%','75':'75%','100':'100%','selected':'25%'}",
            //loadurl : 'http://www.example.com/json.php',
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
        }
    );

    $('#priority').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "task_title", value, "id", dataSet.id);
            return ($(this).find('option:selected').text());
        }
        , {
            type: 'select',
            data: " {'1':'1','2':'2','3':'3', 'selected':'1'}",
            //loadurl : 'http://www.example.com/json.php',
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
        }
    );

    $('#status').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "task_status", value, "id", dataSet.id);
            return ($(this).find('option:selected').text());
        }
        , {
            type: 'select',
            data: " {'new':'New','processing':'Processing','done':'Done', 'selected':'new'}",
            //loadurl : 'http://www.example.com/json.php',
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            tooltip: 'Click to edit...'
        }
    );

    $('#description').editable(function (value, settings) {
            var dataStr = update_database_row("work_task", "task_description", value, "id", dataSet.id);
            return (value);
        }
        , {
            type: 'textarea',
            cancel: '<button type="cancel" class="btn btn-danger" style="margin-left: 5px;"><span class="glyphicon glyphicon-remove"></span></button>',
            submit: '<button type="cancel" class="btn btn-primary" style="margin-left: 5px;"><span class="glyphicon glyphicon-ok"></span></button>',
            //indicator : '<img src="../img/save-512.png">',
            tooltip: 'Click to edit...',
            cssclass: 'description'
        }
    );

    show_tast_details_contact(dataSet.table_data);
}

function show_tast_details_contact(dataSet) {
    $('#task_details_contact').html('<table class="task_details_contact_list" id="task_details_contact_table">' + dataSet + '</table>');


}

function done_this_task(id) {

    //alert('done this task ' + id);

    var dataSet = [[]];
    var dataInfo = {};
    dataInfo['id'] = id;

    var response = connectServer(cms_url['done_this_task'], dataInfo);

    dataSet = JSON.parse(response);

    if (dataSet.status) {
        //return dataSet;
        alertMessage(this, 'green', '', 'success');
        showUserMenu('task_list');
    } else {
        alertMessage(this, 'red', '', 'error');
    }

}

function single_task_action(action) {
    var id = $('#task_id').html();
    if (action == 'complete') {
        done_this_task($('#task_id').html());
    } else if (action == 'delete') {
        $('#task_delete_form input[name="action"]').val('delete');
        $('#task_delete_form input[name="action_key"]').val('id');
        $('#task_delete_form input[name="action_value"]').val(id);
        $('#task_delete_form input[name="action_table"]').val('work_task');

        var response = connectServerWithForm(cms_url['database_editor_with_form'], 'task_delete_form');

        showUserMenu('task_list');
    }
}


function customer_detail_for_this_task(customer_id, node_id, task_id) {
    if (node_id == null || node_id == '' || node_id == 0) {
        show_detail_lead(customer_id);
        $('#back_button').attr("onClick", "task_detail(" + task_id + ");");
    } else {
        work_flow_task_page(customer_id, node_id, task_id);
    }
}

function change_sale_collection() {
    var status = $('#collection_status').val();

    var form_id = 'payment_collection_form';
    var response = connectServerWithForm(cms_url['payment_status_update'], form_id);
    if (response == 0 || response.trim() === '0') {
        //alertMessage(this, 'green', '', 'Successfully Submitted.');
    } else {
        //alertMessage(this, 'red', '', 'Failed.');
    }
}

function new_task_list(div_id) {

    var div_name = "#" + div_id;
    var table_id = "task_list_table";
    $(div_name).html('<table class="table table-striped table-bordered table-hover responsive" id="' + table_id + '" width="100%">' +
        '<tr><td  align="center"></tr></table>');

    var cms_auth = checkSession('cms_auth');
    cms_auth = JSON.parse(cms_auth);

    var dataInfo = {};
    dataInfo['role'] = cms_auth.user_role;
    dataInfo['user_id'] = cms_auth.user_id;
    dataInfo['task_status'] = 'new';

    table_data_task_list(dataInfo, table_id);

}

function complete_task_list(div_id) {

    var div_name = "#" + div_id;
    var table_id = "task_list_table";
    $(div_name).html('<table class="table table-striped table-bordered table-hover responsive" id="' + table_id + '" width="100%">' +
        '<tr><td  align="center"></tr></table>');

    var cms_auth = checkSession('cms_auth');
    cms_auth = JSON.parse(cms_auth);

    var dataInfo = {};
    dataInfo['role'] = cms_auth.user_role;
    dataInfo['user_id'] = cms_auth.user_id;
    dataInfo['task_status'] = 'done';

    table_data_task_list(dataInfo, table_id);

}

function updateTaskComments() {

    var id = $('#task_id').html();
    var cmnt = $('#task_response').val();

    update_database_row("work_task", "task_response", cmnt, "id", id);

    alertMessage(this, 'green', '', 'Comment Saved');
}

function release_contact_from_task(task_id, contact_id) {
    var dataSet = [[]];
    var dataInfo = {};
    dataInfo['task_id'] = task_id;
    dataInfo['contact_id'] = contact_id;

    var response = connectServer(cms_url['remove_contact_from_task'], dataInfo);

    dataSet = JSON.parse(response);

    if (dataSet.status) {
        alertMessage(this, 'green', '', 'success');
        task_detail(task_id);
    } else {
        alertMessage(this, 'red', '', 'error');
    }
}
