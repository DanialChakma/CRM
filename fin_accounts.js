/**
 * Created by L440-User on 8/12/2016.
 */
// this function initiate or update fintelligent bill
function request_for_bill() {
    // Customer Info
    var customer_id = $("#contact_id").val();
    var first_name = $("#first_name").val();
    var last_name = $("#last_name").val();
    var customer_name = first_name + ' ' + last_name;
    var phone_number = $("#phone1").val();
    var email = $("#email").val();
    var collection_address = $("#address2").val();
    var agent_name = $("#conversion_agent").val();
    //var bill_inserted = "";

    // Doze Connection Information
    var package = $("#package").val();
    var connection_address = $("#address1").val();
    var collection_date = $("#conversion_collection_date").val();
    var collection_time = $("#conversion_collection_time").val();
    var assignment_date = $("#assignment_date").val();
    var collect_do = $("#do_area").val();
    var remarks = $("#remarks").val();
    var receipt_number = $("#receipt_number").val();

    var installation_cost = $("#install_cost").val();
    var monthly_cost = $("#monthly_cost").val();
    var number_of_month = $("#month_number").val();
    var real_ip_cost = $("#real_ip_charge").val();
    var additional_cost = $("#additional_charge").val();

    var float_installation_cost = parseFloat(parseFloat(installation_cost).toFixed(4));
    var float_monthly_cost = parseFloat(parseFloat(monthly_cost).toFixed(4));
    var float_number_of_month = parseFloat(parseFloat(number_of_month).toFixed(4));
    var float_real_ip_cost = parseFloat(parseFloat(real_ip_cost).toFixed(4));
    var float_additional_cost = parseFloat(parseFloat(additional_cost).toFixed(4));

    float_installation_cost = isNaN(float_installation_cost) ? 0 : float_installation_cost;
    float_monthly_cost = isNaN(float_monthly_cost) ? 0 : float_monthly_cost;
    float_number_of_month = isNaN(float_number_of_month) ? 0 : float_number_of_month;
    float_real_ip_cost = isNaN(float_real_ip_cost) ? 0 : float_real_ip_cost;
    float_additional_cost = isNaN(float_additional_cost) ? 0 : float_additional_cost;

    var total_cost_calculation = float_installation_cost + (float_monthly_cost * float_number_of_month) + float_real_ip_cost + float_additional_cost;

    var total_cost = parseFloat(parseFloat($("#total_cost").val()).toFixed(4));
    total_cost = isNaN(total_cost) ? 0 : total_cost;

    if ((total_cost_calculation > 0.0 && total_cost > 0.0) && (total_cost_calculation === total_cost)) {

    } else {
        alertMessage(this, "red", "Message", "Computed total cost and total cost field mismatch");
        return;
    }

    var data = {};
    data['customer_id'] = customer_id;
    data['customer_name'] = customer_name;
    data['phone_number'] = phone_number;
    data['email'] = email;
    data['collection_address'] = collection_address;
    data['agent_name'] = agent_name;
    data['package'] = package;
    data['assignment_date'] = assignment_date;
    data['connection_address'] = connection_address;
    data['collection_date'] = collection_date;
    data['collection_time'] = collection_time;
    data['collect_do'] = collect_do;
    data['remarks'] = remarks;
    data['receipt_number'] = receipt_number;
    data['installation_cost'] = float_installation_cost;
    data['monthly_cost'] = float_monthly_cost;
    data['number_of_month'] = float_number_of_month;
    data['real_ip_cost'] = float_real_ip_cost;
    data['additional_cost'] = float_additional_cost;
    data['total_cost'] = total_cost;

    var response = connectServer(cms_url['initiate_fin_bill'], data, false);

    response = JSON.parse(response);
    if (parseInt(response.code) === 1) {
        alertMessage(this, "green", "Operation Status", response.msg);
    } else {
        alertMessage(this, "red", "Operation Status", response.msg);
    }
}



function table_init_accounts_task() {
    $('#tbl_accounts_task').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="accounts_task" width="85%"  ><tr><td  align="center">&nbsp;</td></tr></table>');
}

function table_data_accounts_task(dataSet) {
    $('#accounts_task').dataTable({
        "data": dataSet,
        "columns": [{
            "title": "Customer Name",
            "class": "center"
        }, {
            "title": "Contact No",
            "class": "center"
        },
            // {"title":"Conversion Date","class":"center"},
            {
                "title": "Collection Date",
                "class": "center"
            }, {
                "title": "BillType",
                "class": "center"
            }, {
                "title": "Status",
                "class": "center"
            }
        ],
        "bFilter": false,
        "bLengthChange": false,
        "order": [
            [0, "asc"]
        ],
        dom: 'T<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "doze_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                "copy", "csv", {
                    "sExtends": "xls",
                    "sFileName": "*.xls"
                }
            ],
            "filter": "applied"
        }
    });
}


function view_task_details(customer_id) {
    // $("#tbl_accounts_task").hide();
    var datainfo = {};
    datainfo['customer_id'] = customer_id.trim();
    var res_html = connectServer(cms_url['get_task_details'], datainfo, false);
    $("#task_view_modal").modal();
    $("#task_detail_content").html(res_html);
}

function view_account_task_details(bill_id) {
    var bill_type = $("#" + bill_id + "bill_type").val();
    var datainfo = {};
    datainfo['transaction_id'] = bill_id;
    if (bill_type == "MDB") {
        var res_html = connectServer(cms_url['get_task_details_of_mbill_id'], datainfo, false);
    } else {
        var res_html = connectServer(cms_url['get_task_details_of_bill_id'], datainfo, false);
    }

    $("#task_view_modal").modal();
    $("#transaction_id").val(bill_id);
    $("#bill_type").val(bill_type);
    $("#task_detail_content").html(res_html);
}


function view_accounts_tasks() {
    var datainfo = {};
    datainfo['agent'] = "";
    var dataset = connectServer(cms_url['agent_wise_accounts_task'], datainfo, false);
    dataset = JSON.parse(dataset);
    table_init_accounts_task();
    table_data_accounts_task(dataset);
}

function hold_task() {
    var customer_id = $("#customer_id").val();
    var mb_id = $("#mb_primary_id").val();
    var bill_id = $("#transaction_id").val();
    var bill_type = $("#bill_type").val();
    var remarks = $("#remarks").val();
    var nid = $("#nid").val();
    var photo = $("#photo").val();
    var sap = $("#sap").val();
    var collected_amount = $("#collected_amount").val();
    var cheque_book_id = $("#cheque_book").val();
    var cheque_book_page = $("#cheque_book_pages").val();
    var datainfo = {},
        data = {};

    datainfo['bill_id'] = bill_id;
    datainfo['customer_id'] = customer_id;
    datainfo['remarks'] = remarks;
    datainfo['state'] = "Hold";
    datainfo['cheque_book_id'] = cheque_book_id;
    datainfo['cheque_book_page'] = cheque_book_page;
    datainfo['collected_amount'] = collected_amount;
    datainfo['nid'] = nid;
    datainfo['photo'] = photo;
    datainfo['sap'] = sap;

    data['bill_id'] = bill_id;
    data['mb_id'] = mb_id;
    data['remarks'] = remarks;
    data['state'] = "Hold";
    data['collected_amount'] = collected_amount;
    data['cheque_book_id'] = cheque_book_id;
    data['cheque_book_page'] = cheque_book_page;
    if (bill_type == "MDB") {
        var res = connectServer(cms_url['hold_accounts_task_mbill'], data, false);
    } else {
        var res = connectServer(cms_url['hold_accounts_task'], datainfo, false);
    }

    res = JSON.parse(res);
    if (parseInt(res.code) === 1) {
        alertMessage(this, "blue", "Status", res.msg);
    } else {
        alertMessage(this, "red", "Status", res.msg);
    }
}


function approve_task() {
    var customer_id = $("#customer_id").val();
    var mb_id = $("#mb_primary_id").val();
    var bill_type = $("#bill_type").val();
    var bill_id = $("#transaction_id").val();
    var collected_amount = $("#collected_amount").val();
    var cheque_book_id = $("#cheque_book").val();
    var cheque_book_page = $("#cheque_book_pages").val();
    var remarks = $("#remarks").val();
    var nid = $("#nid").val();
    var photo = $("#photo").val();
    var sap = $("#sap").val();

    var float_collected_amount = parseFloat(parseFloat(collected_amount).toFixed(4));

    var datainfo = {},
        data = {};

    datainfo['bill_id'] = bill_id;
    datainfo['customer_id'] = customer_id;
    datainfo['cheque_book_id'] = cheque_book_id;
    datainfo['cheque_book_page'] = cheque_book_page;
    datainfo['collected_amount'] = float_collected_amount;
    datainfo['remarks'] = remarks;
    datainfo['nid'] = nid;
    datainfo['photo'] = photo;
    datainfo['sap'] = sap;

    data['bill_id'] = bill_id;
    data['mb_id'] = mb_id;
    data['customer_id'] = customer_id;
    data['cheque_book_id'] = cheque_book_id;
    data['cheque_book_page'] = cheque_book_page;
    data['collected_amount'] = float_collected_amount;
    data['remarks'] = remarks;

    if (bill_type == "DB") {

        if (!float_collected_amount || float_collected_amount < 0 || float_collected_amount == "") {
            $("#errors").html("Please Check Collected Amount");
            // alertMessage(this,"red","Information","Please Check Collected Amount");
            return;
        }
        if (!cheque_book_id || cheque_book_id == "" || cheque_book_id == "undefined") {
            $("#errors").html("Please,Select Receipt Book");
            //alertMessage(this,"red","Information","Please Check Receipt Number");
            return;
        }
        if (!cheque_book_page || cheque_book_page == "" || cheque_book_page == "undefined") {
            $("#errors").html("Please,Select Receipt Page");
            //alertMessage(this,"red","Information","Please Check Receipt Number");
            return;
        }
        if (!nid || nid == "" || nid == "undefined") {
            $("#errors").html("Please Select NID");
            // alertMessage(this,"red","Information","Please Select NID");
            return;
        }
        if (!photo || photo == "" || photo == "undefined") {
            $("#errors").html("Please Select Photo");
            //alertMessage(this,"red","Information","Please Select Photo");
            return;
        }
        if (!sap || sap == "" || sap == "undefined") {
            $("#errors").html("Please Select SAP");
            //alertMessage(this,"red","Information","Please Select SAP");
            return;
        }
    } else {
        // check for monthly bill
        if ( !float_collected_amount || float_collected_amount < 0 || float_collected_amount == "" ) {
            $("#errors").html("Please,Check Collected Amount");
            return;
        }
        if ( !cheque_book_id || cheque_book_id == "" || cheque_book_id == "undefined" ) {
            $("#errors").html("Please,Select Receipt Book");
            return;
        }
        if ( !cheque_book_page || cheque_book_page == "" || cheque_book_page == "undefined" ) {
            $("#errors").html("Please,Select Receipt Page");
            return;
        }
    }


    if ( bill_type == "MDB" ) {
        var res = connectServer(cms_url['approve_accounts_task_mbill'], data, false);
    } else {
        var res = connectServer(cms_url['approve_accounts_task'], datainfo, false);
    }

    res = JSON.parse(res);

    if (parseInt(res.code) === 1) {
        alertMessage(this, "blue", "Status", res.msg);
    } else {
        alertMessage(this, "red", "Status", res.msg);
    }
}


function reject_task() {
    var customer_id = $("#customer_id").val();
    var cheque_book_id = $("#cheque_book").val();
    var cheque_book_page = $("#cheque_book_pages").val();
    var mb_id = $("#mb_primary_id").val();
    var bill_id = $("#transaction_id").val();
    var bill_type = $("#bill_type").val();
    var remarks = $("#remarks").val();
    var datainfo = {},data={};
    datainfo['bill_id'] = bill_id;
    datainfo['customer_id'] = customer_id;
    datainfo['cheque_book_id'] = cheque_book_id;
    datainfo['cheque_book_page'] = cheque_book_page;
    datainfo['remarks'] = remarks;
    datainfo['state'] = "Reject";

    data['bill_id'] = bill_id;
    data['mb_id'] = mb_id;
    data['cheque_book_id'] = cheque_book_id;
    data['cheque_book_page'] = cheque_book_page;
    data['remarks'] = remarks;
    data['state'] = "Reject";

    if ( bill_type == "MDB" ) {
        var res = connectServer(cms_url['reject_accounts_task_mbill'], data, false);
    } else {
        var res = connectServer(cms_url['reject_accounts_task'], datainfo, false);
    }

    res = JSON.parse(res);
    if (parseInt(res.code) === 1) {
        alertMessage(this, "blue", "Status", res.msg);
    } else {
        alertMessage(this, "red", "Status", res.msg);
    }
}


function table_init_manager_task() {
    $('#tbl_accounts_task').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="accounts_task" width="85%"  ><tr><td  align="center">&nbsp;</td></tr></table>');

}

function table_data_manager_task(dataset) {
    $('#accounts_task').dataTable({
        "pageLength": 50,
        "data": dataset,
        select: {
            style: 'os',
            selector: 'td:first-child'
        },
        "columns": [
                {
                    "title": "",
                    "class": "center"
                },
                {
                    "title": "Customer Name",
                    "class": "center"
                },
                {
                    "title": "Email",
                    "class": "center"
                },
                {
                    "title": "Phone",
                    "class": "center"
                },
                {
                    "title": "Address",
                    "class": "center"
                },
                {
                    "title": "Due Amount(TK)",
                    "class": "center"
                },
                {
                    "title": "CollectionStatus",
                    "class": "center"
                },
                {
                    "title": "Agent",
                    "class": "center"
                }
        ],
        "bFilter": true,
        "bLengthChange": false,
        "order": [
            [0, "asc"]
        ],
        dom: 'T<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "doze_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                "copy", "csv", {
                    "sExtends": "xls",
                    "sFileName": "*.xls"
                }
            ],
            "filter": "applied"
        }
    });
}

function get_account_manager_tasks() {
    var datainfo = {};

    var start_date = $("#start_date").val();
    var end_date = $("#end_date").val();
    datainfo['manager'] = "";
    datainfo['start_date'] = start_date;
    datainfo['end_date'] = end_date;
    var dataset = connectServer(cms_url['get_account_manager_task'], datainfo, false);
    console.log("Log::");
    dataset = JSON.parse(dataset);
    table_init_manager_task();
    table_data_manager_task(dataset);
}


function table_init_task_reassign() {
    $('#tbl_accounts_task').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="accounts_task" width="85%"  ><tr><td  align="center">&nbsp;</td></tr></table>');
}

function table_data_task_reassign(dataset) {
    $('#accounts_task').DataTable({
        select: false,
        "data": dataset,
        "columns": [{
            "title": "id",
            "class": "center"
        }, {
            "title": "Customer ID",
            "class": "center"
        },
            // {"title":"Conversion Date","class":"center"},
            {
                "title": "Details",
                "class": "center"
            }, {
                "title": "Reassign",
                "class": "center"
            }
        ],
        "bFilter": false,
        "bLengthChange": false,
        "order": [
            [0, "asc"]
        ],
        dom: 'T<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "doze_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                "copy", "csv", {
                    "sExtends": "xls",
                    "sFileName": "*.xls"
                }
            ],
            "filter": "applied"
        }
    });
}


$('#task_view_modal').on('shown.bs.modal', function() {
    $('.chosen-select', this).chosen();
});

function view_manager_task_details(bill_id) {

    $('#task_view_modal').on('shown.bs.modal', function() {
        $('.chosen-select', this).chosen();
    });

    var datainfo = {};
    datainfo['mbid'] = bill_id;
    var res_html;
    res_html = connectServer(cms_url['get_manager_task_details_for_monthly_bill'], datainfo, false);
    res_html = JSON.parse(res_html);
    $("#task_view_modal").modal();
    $("#task_detail_content").html(res_html.content);

    $("#cancel_manager_task").hide();
    $("#approve_manager_task").hide();
    $("#execute_manager_task").hide();

    if( res_html.collection_status == "confirm" ){
    }else if( res_html.collection_status == "yes" ){
        $("#cancel_manager_task").show();
        $("#approve_manager_task").hide();
        $("#execute_manager_task").show();
    }else if( res_html.collection_status == "cancel" ){
        $("#cancel_manager_task").show();
        $("#approve_manager_task").show();
    }else{
        $("#cancel_manager_task").show();
        $("#approve_manager_task").show();
    }
    console.log(res_html.collection_status);
}

function get_chequebook_pages() {
    $(".cheque_pages").show();
    var data = {};
    var book_id = $("#cheque_book").val();
    if (!book_id || book_id == "" || book_id == "undefined") {
        alertMessage(this, "red", "Attention", "Please,select cheque book");
    }
    data['cheque_book_id'] = book_id;
    var available_page_options = connectServer(cms_url['get_available_cheque_book_pages'], data, false);
    $("#cheque_book_pages").html(available_page_options);
    //   $(".cheque_pages").show();
    $('#cheque_book_pages').chosen('destroy').chosen();

}


function approve_manager_task() {

    var mb_id = $("#mb_primary_id").val();
    var collected_amount = $("#collected_amount").val();
    var delivery_cost = $("#delivery_cost").val();
    var receipt_number = $("#receipt_number").val();
    var data = {};
    data['mb_id'] = mb_id;
    data['collected_amount'] = collected_amount;
    data['delivery_cost'] = delivery_cost;
    data['receipt_number'] = receipt_number;
    var res;
    res = connectServer(cms_url['approve_manager_task_mbill'], data, false);


    res = JSON.parse(res);
    if (parseInt(res.code) === 1) {
        alertMessage(this, "blue", "Operation Status", res.msg);
        get_account_manager_tasks();
    } else {
        alertMessage(this, "red", "Operation Status", res.msg);
    }
}

function execute_manager_task() {

    var mb_id = $("#mb_primary_id").val();
   // var collected_amount = $("#collected_amount").val();
  //  var receipt_number = $("#receipt_number").val();

    var data = {};
    data['mb_id'] = mb_id;
    var res;
    res = connectServer(cms_url['execute_manager_task_mbill'], data, false);
    res = JSON.parse(res);
    if (parseInt(res.code) === 1) {
        alertMessage(this, "blue", "Operation Status", res.msg);
        get_account_manager_tasks();
    } else {
        alertMessage(this, "red", "Operation Status", res.msg);
    }
}


function hold_manager_task() {

    var bill_type = $("#bill_type").val();
    var mb_id = $("#mb_primary_id").val();
    var customer_id = $("#customer_id").val();
    var transaction_id = $("#transaction_id").val();
    var collected_amount = $("#collected_amount").val();
    var receipt_number = $("#receipt_number").val();
    var remarks = $("#remarks").val();
    var nid = $("#nid").val();
    var photo = $("#photo").val();
    var sap = $("#sap").val();
    var cheque_book_id = $("#cheque_book").val();
    var cheque_book_page = $("#cheque_book_pages").val();
    var datainfo = {},
        data = {};

    datainfo['bill_id'] = transaction_id;
    datainfo['customer_id'] = customer_id;
    datainfo['collected_price'] = collected_amount;
    datainfo['cheque_book_id'] = cheque_book_id;
    datainfo['cheque_book_page'] = cheque_book_page;
    datainfo['remarks'] = remarks;
    datainfo['nid'] = nid;
    datainfo['photo'] = photo;
    datainfo['sap'] = sap;

    data['bill_id'] = transaction_id;
    data['mb_id'] = mb_id;
    data['customer_id'] = customer_id;
    data['collected_amount'] = collected_amount;
    data['cheque_book_id'] = cheque_book_id;
    data['cheque_book_page'] = cheque_book_page;
    data['remarks'] = remarks;
    var res;
    if (bill_type == "MDB") {
        res = connectServer(cms_url['hold_manager_task_mbill'], data, false);
    } else {
        res = connectServer(cms_url['hold_manager_task'], datainfo, false);
    }

    res = JSON.parse(res);
    if (parseInt(res.code) === 1) {
        alertMessage(this, "blue", "Operation Status", res.msg);
        get_account_manager_tasks();
    } else {
        alertMessage(this, "red", "Operation Status", res.msg);
    }
}

function reject_manager_task() {
    var mb_id = $("#mb_primary_id").val();
    var data = {};
    data['mb_id'] = mb_id;
    var res;
    res = connectServer(cms_url['reject_manager_task_mbill'], data, false);
    res = JSON.parse(res);
    if (parseInt(res.code) === 1) {
        alertMessage(this, "blue", "Operation Status", res.msg);
        get_account_manager_tasks();
    } else {
        alertMessage(this, "red", "Operation Status", res.msg);
    }
}



function get_tasks_to_reassign() {
    var datainfo = {};
    datainfo['manager'] = "";
    var dataset = connectServer(cms_url['get_task_for_reassign'], datainfo, false);
    // console.log("Log::",dataset);
    dataset = JSON.parse(dataset);
    table_init_task_reassign();
    table_data_task_reassign(dataset);
}


function assign_task_to() {
    var datainfo = {};
    var ids = Array();
    var assign_id = document.getElementById('agent_select').value;
    var reassign_date = document.getElementById('reassign_date').value;
    var table = document.getElementById('accounts_task');

    var len = table.rows.length;
    var i;
    for (i = 1; i < len; i++) {
        var bill_id = table.rows[i].cells[0].children[0].value;
        if (table.rows[i].cells[0].children[0].checked) {
            bill_id = bill_id.trim()
            ids.push(bill_id);
        }
    }

    datainfo['reassign_date'] = reassign_date;
    datainfo['assign_id'] = assign_id;
    datainfo['ids'] = ids;
    var res = connectServer(cms_url['assign_task_to_agent'], datainfo, false);
    res = JSON.parse(res);
    var len = res.length;
    var msg_str = "";
    for (i = 0; i < len; i++) {
        if (msg_str == "") {
            msg_str = res[i].bill_id + "," + res[i].msg;
        } else {
            msg_str += "<br/>" + res[i].bill_id + "," + res[i].msg;
        }
    }

    alertMessage(this, "blue", "Operation Status", msg_str);

}


function generate_call_list_page() {
    displayContent("62", "#cmsData", "#contentListLayout", "ContentID");
    $('#renew_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: 1,
        todayHighlight: 1
    });
}


function table_init_call_list() {
    $('#tbl_call_list').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="tbl_call_list_data" width="100%"  ><tr><td  align="center">&nbsp;</td></tr></table>');
}

function table_data_call_list(dataset) {
    $('#tbl_call_list_data').DataTable({
        select: false,
        lengthChange: false,
        "pageLength": dataset.length,
        "data": dataset,
        dom: "<'row'<'col-sm-6'B><'col-sm-3 text-center'l><'col-sm-3'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            {
                text: '<button type="button" class="btn btn-primary btn-sm">Select All</button>',
                action: function ( e, dt, node, config ) {
                   var table = document.getElementById("tbl_call_list_data");
                   var rows = table.rows.length;
                   for(var i=1;i<rows;i++){
                       var chk_box = table.rows[i].cells[0];
                       //console.log(chk_box);
                       if($(chk_box).find('input[type="checkbox"]').is(":checked")){

                       }else{
                           $(chk_box).find('input[type="checkbox"]').prop("checked",true);
                       }
                   }
                   console.log(table.rows.length);
                }
            },
            {
                text: '<button type="button" class="btn btn-primary btn-sm">Save</button>',
                action: function ( e, dt, node, config ) {
                    var table = document.getElementById("tbl_call_list_data");
                    var rows = table.rows.length;
                    var ids = [];
                    for(var i=1;i<rows;i++){
                        var chk_box = table.rows[i].cells[0];
                        var id_colum = table.rows[i].cells[1];
                        //console.log(chk_box);
                        if($(chk_box).find('input[type="checkbox"]').is(":checked")){
                              ids.push (parseInt($(id_colum).text()));
                        }
                    }

                    var datainfo = {};
                    var renew_date = $("#renew_date").val();
                    datainfo['renew_date'] = renew_date;
                    datainfo['ids'] = ids;
                    var res = connectServer(cms_url['save_cgw_call_list'], datainfo, false);
                    res = JSON.parse(res);
                    if( res.status == 0 ){
                        alertMessage(this, "green", "Operation Status", res.msg);
                    }else{
                        alertMessage(this, "red", "Operation Status", res.msg);
                    }

                }
            }
        ],
        "columns": [{
            "title": "Selection",
            "class": "center"
        },{
            "title": "id",
            "class": "center"
        }, {
            "title": "Customer ID",
            "class": "center"
        },
            // {"title":"Conversion Date","class":"center"},
            {
                "title": "Email",
                "class": "center"
            }, {
                "title": "Charging Due Date",
                "class": "center"
            },
            {
                "title": "Renewal Date",
                "class": "center"
            },
            {
                "title": "Package Price",
                "class": "center"
            },
            {
                "title": "Due Amount",
                "class": "center"
            },
            {
                "title": "Status",
                "class": "center"
            }
        ],
        "bFilter": false,
        "bLengthChange": false,
        "order": [
            [0, "asc"]
        ],
       // dom: 'B<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "doze_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
           /* "aButtons": [
                "copy", "csv", {
                    "sExtends": "xls",
                    "sFileName": "*.xls"
                }
            ], */
            "filter": "applied"
        }
    });
}

function get_cgw_call_list() {
    var datainfo = {};

    var renew_date = $("#renew_date").val();
    datainfo['renew_date'] = renew_date;
    var res = connectServer(cms_url['get_cgw_call_list'], datainfo, false);
   
    var data = JSON.parse(res);
    if ( data.status && data.status == 0 ) {
        alertMessage(this, "red", "Operation Status", data.msg);
    } else {
        table_init_call_list();
        table_data_call_list(data);
    }

}

function generate_call_list() {
    var datainfo = {};

    var renew_date = $("#renew_date").val();
    datainfo['renew_date'] = renew_date;
    var res = connectServer(cms_url['generate_call_list'], datainfo, false);
    res = JSON.parse(res);
    if (res.status == 1) {
        alertMessage(this, "blue", "Operation Status", res.msg);
    } else {
        alertMessage(this, "red", "Operation Status", res.msg);
    }

}


/*
 function table_init_call_assign(){
 $('#tbl_monthly_call').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="tbl_monthly_call_assign" width="85%"  ><tr><td  align="center">&nbsp;</td></tr></table>');
 }*/

function table_init_call_assign() {
    $('#tbl_monthly_call').html('<table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" id="tbl_monthly_call_assign" width="65%" ><tr><td  align="center">&nbsp;</td></tr></table>');
}

function table_data_call_assign(dataset) {
    $('#tbl_monthly_call_assign').DataTable({
        responsive: true,
        select: false,
        "data": dataset,
        "columns": [{
            "title": "",
            "class": "center"
        }, {
            "title": "Email",
            "class": "center"
        }, {
            "title": "Renew Date",
            "class": "center"
        }, {
            "title": "Assign To",
            "class": "center"
        }],
        "bFilter": false,
        "bLengthChange": false,
        "order": [
            [0, "asc"]
        ],
        dom: 'T<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "doze_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                "copy", "csv", {
                    "sExtends": "xls",
                    "sFileName": "*.xls"
                }
            ],
            "filter": "applied"
        }
    });
}


function get_call_list() {
    var datainfo = {};
    datainfo['renew_date'] = $("#renew_date").val();
    var dataset = connectServer(cms_url['get_call_list'], datainfo, false);
    dataset = JSON.parse(dataset);
    table_init_call_assign();
    table_data_call_assign(dataset);
}

function assign_call_page() {
    displayContent("63", "#cmsData", "#contentListLayout", "ContentID");
    calenderDatePickerOnlyDate(1, "get_call_list");
    fetchDropDownOptionHtml("#assign_to", cms_url['get_accounts_agent'],{"agent_type":"account_caller"});
    var datainfo = {};
    datainfo['renew_date'] = $("#renew_date").val();
    var dataset = connectServer(cms_url['get_call_list'], datainfo, false);
    // console.log("Log::",dataset);
    dataset = JSON.parse(dataset);
    table_init_call_assign();
    table_data_call_assign(dataset);
}

function assign_call_to_agent() {

    var ids = Array();
    var assign_id = document.getElementById('assign_to').value;
    var table = document.getElementById('tbl_monthly_call_assign');

    var len = table.rows.length;
    var i;
    for (i = 1; i < len; i++) {
        var bill_id = table.rows[i].cells[0].children[0].value;
        if (table.rows[i].cells[0].children[0].checked) {
            bill_id = bill_id.trim()
            ids.push(bill_id);
        }
    }

    var datainfo = {};
    datainfo['assign_id'] = assign_id;
    datainfo['ids'] = ids;

    var res = connectServer(cms_url['assign_call_to_agent'], datainfo, false);
    res = JSON.parse(res);
    if (res.status == 1) {
        alertMessage(this, "blue", "Operation Status", res.msg);
        get_call_list();
    } else {
        alertMessage(this, "red", "Operation Status", res.msg);
    }

}

function table_init_monthly_call() {
    $('#tbl_monthly_call').html('<table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" id="tbl_monthly_call_list" width="65%" ><tr><td  align="center">&nbsp;</td></tr></table>');
}

function table_data_monthly_call(dataset) {
    $('#tbl_monthly_call_list').DataTable({
        "info":false,
        responsive: true,
        "select": false,
        "data": dataset,
        "columns": [{
            "title": "Customer Name",
            "class": "center"
        }, {
            "title": "Contacts",
            "class": "center"
        }, {
            "title": "Package Price",
            "class": "center"
        }, {
            "title": "Due Amount(TK)",
            "class": "center"
        },{
            "title": "Call History",
            "class": "center"
        }],
        "bFilter": true,
        "bLengthChange": false,
        "order": [
            [0, "asc"]
        ],
        dom: 'T<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "doze_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                "copy", "csv", {
                    "sExtends": "xls",
                    "sFileName": "*.xls"
                }
            ],
            "filter": "applied"
        }
    });
}


function table_init_monthly_call_history(){
    $('#tbl_call_history').html('<table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" id="tbl_monthly_call_history_data" width="65%" ><tr><td  align="center">&nbsp;</td></tr></table>');
}

function table_data_monthly_call_history(dataset){
    $('#tbl_monthly_call_history_data').DataTable({
        responsive: true,
        select: false,
        "data": dataset,
        "columns": [{
            "title": "CustomerID",
            "class": "center"
        }, {
            "title": "Customer Feedback",
            "class": "center"
        },{
            "title": "Call Date",
            "class": "center"
        },{
            "title": "Followup Date",
            "class": "center"
        },{
            "title": "Call Status",
            "class": "center"
        },{
            "title": "Call Outcome",
            "class": "center"
        }],
        "bFilter": true,
        "bLengthChange": false,
        "order": [
            [0, "asc"]
        ],
        dom: 'T<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "doze_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                "copy", "csv", {
                    "sExtends": "xls",
                    "sFileName": "*.xls"
                }
            ],
            "filter": "applied"
        }
    });
}
function show_call_history(mbcl_id){
    var data = {};
    data['mbcl_id'] = mbcl_id;
    var dataset = connectServer(cms_url['get_monthly_call_history'], data, false);
    dataset = JSON.parse(dataset);
    table_init_monthly_call_history();
    table_data_monthly_call_history(dataset);
}

function monthly_call_page() {
    displayContent("64", "#cmsData", "#contentListLayout", "ContentID");
    //calenderDatePickerOnlyDate(1, "get_call_list");
    //  fetchDropDownOptionHtml("#assign_to",cms_url['get_accounts_agent']);
    var datainfo = {};
    var dataset = connectServer(cms_url['get_monthly_call_list'], datainfo, false);
    // console.log("Log::",dataset);
    dataset = JSON.parse(dataset);
    table_init_monthly_call();
    table_data_monthly_call(dataset);
}

function SearchForUsers(){
    var datainfo = {};
    datainfo['user_id'] = $("#user_id").val();
  //  var res_html = connectServer(cms_url['serach_for_radius_user'], datainfo, false);
    $("#radius_search_result").modal();
}

function generate_collection_tasks(){
    //var form = $("#user_details");
   // var values = form.serialize();
    var res = connectServerWithForm(cms_url['save_collection_task'],'user_details');
   // var res = connectServer(cms_url['save_collection_task'], values, false);

}

function show_monthly_details(ids) {

    var datainfo = {};
    var id_arr = ids.split("|");
    var contact_id = id_arr[0];
    var mb_id = id_arr[1];
    datainfo['customer_id'] = contact_id;
    datainfo['mb_id'] = mb_id;
    var res_html = connectServer(cms_url['get_monthly_call_details'], datainfo, false);
    $("#task_view_modal").modal();
    $("#contact_id").val(contact_id);
    $("#task_detail_content").html(res_html);
    $('.calendarPickerDate').datepicker({
        format: "yyyy-mm-dd",
        weekStart: 2 - 1,
        todayBtn: true,
        calendarWeeks: false,
        autoclose: true,
        todayHighlight: true,
        todayHighlight: 1
    });
}

function call_status_change() {
    var call_status = $("#call_status").val();
    if ( call_status == "Connected" ) {
        $(".outcome").show();
        var outcome = parseInt($("#outcome").val());
        if ( outcome == 1 ) {
            $(".followup").show();
        }
        if ( outcome == 3 || outcome == 4 ) {
            //alert(outcome);
            $(".paymentmethod").show();
            if( outcome == 4 ){
                $(".payment_date").show();
            }else{
                $(".payment_date").hide();
            }
        }
    } else {
        $(".outcome").hide();
        $(".followup").hide();
        $(".payment_date,.paymentmethod").hide();
    }
}

function outcome_change() {

    var outcome = parseInt($("#outcome").val());
    if (  outcome == 1 ) {
        $(".followup").show();
    } else {
        $(".followup").hide();
    }

    if ( outcome == 3 || outcome == 4 ) {
        //alert(outcome);
        $(".paymentmethod").show();
        if( outcome == 4 ){
            $(".payment_date").show();
        }else{
            $(".payment_date").hide();
        }
    }else{
        $(".payment_date,.paymentmethod").hide();
    }
}

function save_monthly_call_status() {

    var datainfo = {};
    var contact_id = $("#contact_id").val();
    var call_status = $("#call_status").val();
    var outcome = $("#outcome").val();
    var mbcl_id = $("#mbcl_id").val();
    var follow_up_date = $("#follow_up_date").val();
    var paymentmethod = $("#paymentmethod").val();
    var payment_date = $("#payment_date").val();
    var feedback = $("#remarks").val();
    datainfo['contact_id'] = contact_id;
    datainfo['call_status'] = call_status;
    datainfo['outcome'] = outcome;
    datainfo['follow_up_date'] = follow_up_date;
    datainfo['feedback'] = feedback;
    datainfo['mbcl_id'] = mbcl_id;
    datainfo['paymentmethod'] = paymentmethod;
    datainfo['payment_date'] = payment_date;
    var res = connectServer(cms_url['save_monthly_call_status'], datainfo, false);
    res = JSON.parse(res);
    if (res.status == 0) {
        alertMessage(this, "blue", "Operation Status", res.msg);
    } else {
        alertMessage(this, "red", "Operation Status", res.msg);
    }
}


function invoice_generation_page() {
    displayContent("65", "#cmsData", "#contentListLayout", "ContentID");
    fetchDropDownOptionHtml("#cheque_book_id", cms_url['get_cheque_book_option']);
}

function enable_multiple_check() {
    if ($("#is_multiple_check").is(":checked")) {
        $("#checkbox_number").attr("disabled", false);
    } else {
        $("#checkbox_number").attr("disabled", true);
    }
}

function generate_blank_checkbook() {
    var book_id = $("#check_book_id").val();
    var branch = $("#branch").val();
    var check_prefix = $("#check_prefix").val();
    var check_initial_number = $("#check_initial_number").val();
    var check_number_diff = $("#check_number_diff").val();
    var checkbox_number = $("#checkbox_number").val();
    var remarks = $("#remarks").val();
    var is_multiple = $("#is_multiple_check").is(":checked");
    if (!book_id || book_id == 'undefined' || book_id == '') {
        alertMessage(this, "yellow", "Requirements", "Check Book ID Can't be Empty");
        return;
    }

    if (!branch || branch == 'undefined' || branch == '') {
        alertMessage(this, "yellow", "Requirements", "You haven't selected any Branch.<br/>" + "Please,Select Branch");
        return;
    }
    if (!check_prefix || check_prefix == 'undefined' || check_prefix == '') {
        alertMessage(this, "yellow", "Requirements", "Check No: First Field(Prefix) can't be empty.");
        return;
    }
    if (!check_initial_number || check_initial_number == 'undefined' || check_initial_number == '' || check_initial_number < 0) {
        alertMessage(this, "yellow", "Requirements", "Check No: Second Field(Initial Check Number) can't be empty OR Less than Zero");
        return;
    }



    if (is_multiple) {

        if (!checkbox_number || checkbox_number == 'undefined' || checkbox_number == '' || checkbox_number <= 0) {
            alertMessage(this, "yellow", "Requirements", "For Multiple Check. Number of Check field can't be empty or Zero");
            return;
        }

        if (!check_number_diff || check_number_diff == 'undefined' || check_number_diff == '' || check_number_diff <= 0) {
            alertMessage(this, "yellow", "Requirements", "Check No: Third Field(Consecutive Check Number Interval) can't be empty OR Less than One");
            return;
        }
    }

    var data = {};
    data['book_id'] = book_id;
    data['branch'] = branch;
    data['check_prefix'] = check_prefix;
    data['check_initial_number'] = check_initial_number;
    data['check_number_diff'] = check_number_diff;
    data['checkbox_number'] = checkbox_number;
    data['remarks'] = remarks;
    data['is_multiple'] = is_multiple;
    var res = connectServer(cms_url['generate_check_book'], data, false);
    res = JSON.parse(res);
    if (res.status == 0) {
        alertMessage(this, "blue", "Operation Status", res.msg);
        fetchDropDownOptionHtml("#cheque_book_id", cms_url['get_cheque_book_option']);
    } else {
        alertMessage(this, "red", "Operation Status", "Generation Failed!!");
    }
}



function table_init_cheque_book() {
    $('#tbl_cheque_book').html('<table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" id="tbl_cheque_book_pages" width="65%" ><tr><td  align="center">&nbsp;</td></tr></table>');
}


function table_data_cheque_book(dataset) {
    $('#tbl_cheque_book_pages').DataTable({
        responsive: true,
        select: false,
        "data": dataset,
        "columns": [{
            "title": "InvoiceBookID",
            "class": "center"
        }, {
            "title": "InvoiceInitialID",
            "class": "center"
        }, {
            "title": "InvoiceNo",
            "class": "center"
        }, {
            "title": "Branch",
            "class": "center"
        }, {
            "title": "Status",
            "class": "center"
        }, {
            "title": "Remarks",
            "class": "center"
        }, {
            "title": "LastUpdate",
            "class": "center"
        }],
        "pageLength": 50,
        "bFilter": true,
        "bLengthChange": false,
        "order": [
            [0, "asc"]
        ],
        dom: 'T<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "doze_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                "copy", "csv", {
                    "sExtends": "xls",
                    "sFileName": "*.xls"
                }
            ],
            "filter": "applied"
        }
    });
}

function get_cheque_book_pages() {

    var cheque_book_id = $("#cheque_book_id").val();
    if (!cheque_book_id || cheque_book_id == "" || cheque_book_id == "undefined") {
        return;
    }

    var data = {};
    data['cheque_book_id'] = cheque_book_id;
    var res_data = connectServer(cms_url['get_cheque_book_pages'], data, false);

    var dataset = JSON.parse(res_data);
    table_init_cheque_book();
    table_data_cheque_book(dataset);

}