/**
 * Created by L440-User on 8/12/2016.
 */
// this function initiate or update fintelligent bill
function request_for_bill() {
    // Customer Info
    var customer_id = $("#contact_id").val();
    var payment_method = $("#payment_mode_conversion").val();

    if( payment_method != "Ecourier" ){
        alertMessage(this, "red", "Message", "You can request bill for only E-Courier Payment Method.");
        return;
    }

    var data = {};
    data['customer_id'] = customer_id;
    data['payment_method'] = payment_method;


    var response = connectServer(cms_url['initiate_fin_bill'], data, false);

    response = JSON.parse(response);
    if ( parseInt(response.code) === 1 ) {
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
    datainfo['mb_id'] = bill_id;
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
    var delivery_cost  = $("#delivery_cost").val();
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
    data['delivery_cost'] = delivery_cost;
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
    var delivery_cost = $("#delivery_cost").val();

    var datainfo = {},
        data = {};

    datainfo['bill_id'] = bill_id;
    datainfo['customer_id'] = customer_id;
    datainfo['cheque_book_id'] = cheque_book_id;
    datainfo['cheque_book_page'] = cheque_book_page;
    datainfo['collected_amount'] = collected_amount;
    datainfo['remarks'] = remarks;
    datainfo['nid'] = nid;
    datainfo['photo'] = photo;
    datainfo['sap'] = sap;

    data['bill_id'] = bill_id;
    data['mb_id'] = mb_id;
    data['customer_id'] = customer_id;
    data['cheque_book_id'] = cheque_book_id;
    data['cheque_book_page'] = cheque_book_page;
    data['collected_amount'] = collected_amount;
    data['delivery_cost'] = delivery_cost;
    data['remarks'] = remarks;

    var numRegex = /^\d+\.?\d*$/;
    if (bill_type == "DB") {

        if( !numRegex.test(collected_amount) ){
            $("#errors").html("Collected Amount: Field must be numaric");
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
        if( !numRegex.test(collected_amount) ){
            $("#errors").html("Collected Amount: Field must be numaric");
            return;
        }

        if( !numRegex.test(delivery_cost) ){
            $("#errors").html("Delivery Cost: Field must be numaric");
            return;
        }
     /*   if ( !cheque_book_id || cheque_book_id == "" || cheque_book_id == "undefined" ) {
            $("#errors").html("Please,Select Receipt Book");
            return;
        }
        if ( !cheque_book_page || cheque_book_page == "" || cheque_book_page == "undefined" ) {
            $("#errors").html("Please,Select Receipt Page");
            return;
        } */
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
        "columns": [{
            "title": "",
            "class": "center"
        },

            {
                "title": "Customer Name",
                "class": "center"
            },
            // {"title":"Conversion Date","class":"center"},
            {
                "title": "Email",
                "class": "center"
            }, {
                "title": "Phone",
                "class": "center"
            }, {
                "title": "Address",
                "class": "center"
            }, {
                "title": "Due Amount(TK)",
                "class": "center"
            }, {
                "title": "CollectionStatus",
                "class": "center"
            },{
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
    // console.log("Log::",dataset);
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
   // console.log(res_html.collection_status);
}

/*
function view_manager_task_details(bill_id) {

    $('#task_view_modal').on('shown.bs.modal', function() {
        $('.chosen-select', this).chosen();
    });

    var status = $("#" + bill_id.trim()).val();
    var bill_type = $("#" + bill_id.trim() + "bill_type").val();
    var datainfo = {};
    datainfo['transaction_id'] = bill_id;
    var res_html;
    if (bill_type == "MDB") {
        res_html = connectServer(cms_url['get_manager_task_details_for_monthly_bill'], datainfo, false);
    } else {
        res_html = connectServer(cms_url['get_manager_task_details_of_bill_id'], datainfo, false);
    }

    $("#task_view_modal").modal();
    $("#approve_manager_task").show();
    $("#execute_manager_task").show();
    if (status == "Approve") {
        $("#approve_manager_task").hide();
        $("#execute_manager_task").show();
    } else {
        $("#execute_manager_task").hide();
    }

    if( status == "Confirmed" ){
        $('#reject_manager_task,#hold_manager_task,#approve_manager_task,#execute_manager_task').hide();
    }
    $("#transaction_id").val(bill_id);
    if (bill_type == "MDB") {
        $("#bill_type").val("MDB");
    } else {
        $("#bill_type").val("DB");
    }

    $("#task_detail_content").html(res_html);

    var invoiceparam = {};
    invoiceparam['bill_type'] = bill_type;
    invoiceparam['bill_id'] = bill_id;
    var invoiceRes = connectServer(cms_url['get_invoice_data'], invoiceparam, false);
    invoiceRes = JSON.parse(invoiceRes);
    $("#cheque_book").val(invoiceRes.bookid);

    if ( invoiceRes.invoiceno && invoiceRes.invoiceno != "" ) {
        var data = {};
        data['cheque_book_id'] = invoiceRes.bookid;
        var available_page_options = connectServer(cms_url['get_available_cheque_book_pages'], data, false);
        $("#cheque_book_pages").html(available_page_options);
        $(".cheque_pages").show();
        $("#cheque_book_pages").val(invoiceRes.invoiceno);
    }
} */

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

    var bill_type = $("#bill_type").val();
    var mb_id = $("#mb_primary_id").val();
    var customer_id = $("#customer_id").val();
    var transaction_id = $("#transaction_id").val();
    var collected_amount = $("#collected_amount").val();
    var delivery_cost = $("#delivery_cost").val();
    var install_cost = $("#installation_cost").val();
    var receipt_number = $("#receipt_number").val();
    var remarks = $("#remarks").val();
    var nid = $("#nid").val();
    var photo = $("#photo").val();
    var sap = $("#sap").val();
    var cheque_book_id = $("#cheque_book").val();
    var cheque_book_page = $("#cheque_book_pages").val();
    var datainfo = {},
        data = {};
    datainfo['customer_id'] = customer_id;
    datainfo['bill_id'] = transaction_id;
    datainfo['mb_id'] = mb_id;
    datainfo['delivery_cost'] = delivery_cost
    datainfo['collected_amount'] = collected_amount;
    datainfo['receipt_number'] = receipt_number;
    datainfo['installation_cost'] = install_cost;
    //datainfo['cheque_book_id'] = cheque_book_id;
  //  datainfo['cheque_book_page'] = cheque_book_page;
    datainfo['remarks'] = remarks;
    //datainfo['nid'] = nid;
   // datainfo['photo'] = photo;
   // datainfo['sap'] = sap;

    data['customer_id'] = customer_id;
    data['bill_id'] = transaction_id;
    data['mb_id'] = mb_id;
    data['receipt_number'] = receipt_number;
    data['remarks'] = remarks;
    data['collected_amount'] = collected_amount;
    data['delivery_cost']    = delivery_cost;
    // data['cheque_book_id'] = cheque_book_id;
   // data['cheque_book_page'] = cheque_book_page;
  //  alert("KKKK");
    var numRegex = /^\d+\.?\d*$/;
    if ( bill_type == "DB" ) {
        if( !numRegex.test(collected_amount) ){
            $("#errors").html("Collected Amount: Field must be numaric");
            return;
        }
        if( !numRegex.test(delivery_cost) ){
            $("#errors").html("Delivery Cost: Field must be numaric");
            return;
        }
        if( !numRegex.test(install_cost) ){
            $("#errors").html("Installation Cost: Field must be numaric");
            return;
        }
     /*   if (!cheque_book_id || cheque_book_id == "" || cheque_book_id == "undefined") {
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
        } */
    }else{

        if( !numRegex.test(collected_amount) ){
            $("#errors").html("Collected Amount: Field must be numaric");
            return;
        }

        if( !numRegex.test(delivery_cost) ){
            $("#errors").html("Delivery Cost: Field must be numaric");
            return;
        }
        if( !numRegex.test(receipt_number) ){
            $("#errors").html("Receipt Number: Field must be numaric");
            return;
        }
        /*
        if ( !cheque_book_id || cheque_book_id == "" || cheque_book_id == "undefined" ) {

            $("#errors").html("Please,Select Receipt Book");
            return;
        }
        if ( !cheque_book_page || cheque_book_page == "" || cheque_book_page == "undefined" ) {
            $("#errors").html("Please,Select Receipt Page");
            return;
        } */
    }


    var res;
    if ( bill_type == "MDB" ) {
        res = connectServer(cms_url['approve_manager_task_mbill'], data, false);
    } else {
        res = connectServer(cms_url['approve_manager_task'], datainfo, false);
    }

    res = JSON.parse(res);
    if (parseInt(res.code) === 1) {
        alertMessage(this, "blue", "Operation Status", res.msg);
        get_account_manager_tasks();
    } else {
        alertMessage(this, "red", "Operation Status", res.msg);
    }
}

function execute_manager_task() {
    var bill_type = $("#bill_type").val();
    var customer_id = $("#customer_id").val();
    var transaction_id = $("#transaction_id").val();
    var mb_id = $("#mb_primary_id").val();
    var collected_amount = $("#collected_amount").val();
    var install_cost = $("#installation_cost").val();
    var delivery_cost = $("#delivery_cost").val();
    var receipt_number = $("#receipt_number").val();
    var remarks = $("#remarks").val();
   // var nid = $("#nid").val();
  //  var photo = $("#photo").val();
  //  var sap = $("#sap").val();
 //   var cheque_book_id = $("#cheque_book").val();
 //   var cheque_book_page = $('#cheque_book_pages option:selected').val();
    var datainfo = {},
        data = {};
    datainfo['bill_id'] = transaction_id;
    datainfo['customer_id'] = customer_id;
    datainfo['mb_id'] = mb_id;
    datainfo['collected_amount'] = collected_amount;
    datainfo['receipt_number'] = receipt_number;
    datainfo['delivery_cost'] = delivery_cost;
    datainfo['installation_cost'] = install_cost;
 //   datainfo['cheque_book_id'] = cheque_book_id;
  //  datainfo['cheque_book_page'] = cheque_book_page;
    datainfo['remarks'] = remarks;
  //  datainfo['nid'] = nid;
 //   datainfo['photo'] = photo;
//    datainfo['sap'] = sap;

    data['bill_id'] = transaction_id;
    data['mb_id'] = mb_id;
    data['receipt_number'] = receipt_number;
    data['remarks'] = remarks;
    data['collected_amount'] = collected_amount;
    data['delivery_cost'] = delivery_cost;
  //  data['cheque_book_id'] = cheque_book_id;
  //  data['cheque_book_page'] = cheque_book_page;

    var numRegex = /^\d+\.?\d*$/;
    if ( bill_type == "DB" ) {
        if( !numRegex.test(collected_amount) ){
            $("#errors").html("Collected Amount: Field must be numaric");
            return;
        }
        if( !numRegex.test(delivery_cost) ){
            $("#errors").html("Delivery Cost: Field must be numaric");
            return;
        }
        if( !numRegex.test(install_cost) ){
            $("#errors").html("Installation Cost: Field must be numaric");
            return;
        }
    /*    if (!cheque_book_id || cheque_book_id == "" || cheque_book_id == "undefined") {
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
        } */
    }else{

        if( !numRegex.test(collected_amount) ){
            $("#errors").html("Collected Amount: Field must be numaric");
            return;
        }

        if( !numRegex.test(delivery_cost) ){
            $("#errors").html("Delivery Cost: Field must be numaric");
            return;
        }
        if( !numRegex.test(receipt_number) ){
            $("#errors").html("Receipt Number: Field must be numaric");
            return;
        }
        /*
        if ( !cheque_book_id || cheque_book_id == "" || cheque_book_id == "undefined" ) {
            $("#errors").html("Please,Select Receipt Book");
            return;
        }
        if ( !cheque_book_page || cheque_book_page == "" || cheque_book_page == "undefined" ) {
            $("#errors").html("Please,Select Receipt Page");
            return;
        } */
    }


    var res;
    if (bill_type == "MDB") {
        res = connectServer(cms_url['execute_manager_task_mbill'], data, false);
    } else {
        res = connectServer(cms_url['execute_manager_task'], datainfo, false);
    }

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
    var delivery_cost = $("#delivery_cost").val();
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
    data['delivery_cost'] = delivery_cost;
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
    var bill_type = $("#bill_type").val();
    var mb_id = $("#mb_primary_id").val();
    var customer_id = $("#customer_id").val();
    var transaction_id = $("#transaction_id").val();
    var collected_amount = $("#collected_amount").val();
    var cheque_book_id = $("#cheque_book").val();
    var cheque_book_page = $("#cheque_book_pages").val();
    var remarks = $("#remarks").val();
    var nid = $("#nid").val();
    var photo = $("#photo").val();
    var sap = $("#sap").val();
    var datainfo = {},
        data = {};
    datainfo['bill_id'] = transaction_id;
    datainfo['customer_id'] = customer_id;
    datainfo['cheque_book_id'] = cheque_book_id;
    datainfo['cheque_book_page'] = cheque_book_page;
    datainfo['remarks'] = remarks;
    datainfo['nid'] = nid;
    datainfo['photo'] = photo;
    datainfo['sap'] = sap;

    data['bill_id'] = transaction_id;
    data['mb_id'] = mb_id;
    data['remarks'] = remarks;
    data['cheque_book_id'] = cheque_book_id;
    data['cheque_book_page'] = cheque_book_page;
    var res;
    if ( bill_type == "MDB" ) {
        res = connectServer(cms_url['reject_manager_task_mbill'], data, false);
    } else {
        res = connectServer(cms_url['reject_manager_task'], datainfo, false);
    }

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
    setTimeout(function(){
        display_call_list_history();
    },500);

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
       /* dom: "<'row'<'col-sm-6'B><'col-sm-3 text-center'l><'col-sm-3'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5'i><'col-sm-7'p>>", */
        "columns": [{
            "title": "ID",
            "class": "center"
        },{
            "title": "Mark",
            "class": "center"
        }, {
            "title": "Renew Date",
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


function display_call_list_history(){
    var datainfo = {};
    var res = connectServer(cms_url['get_call_list_history'], datainfo, false);
    var data = JSON.parse(res);
    table_init_call_list();
    table_data_call_list(data);
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
        alertMessage(this, "green", "Operation Status", data.msg);
        display_call_list_history();
    }

}

function generate_call_list() {

    var datainfo = {};
    var renew_date = $("#renew_date").val();
    datainfo['renew_date'] = renew_date;
    var res = connectServer(cms_url['generate_call_list'], datainfo, false);
    res = JSON.parse(res);
    if ( res.status == 1 ) {
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
        "info":true,
        responsive: true,
        "select": false,
        "data": dataset,
        "columns": [{
            "title": "Connected ?",
            "class": "center"
        },{
            "title": "Customer Name",
            "class": "center"
        },{
            "title": "Email",
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
            "title": "Email",
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
    if( dataset.status == 0 ){
        alertMessage(this,"red","Status",dataset.msg);
    }else{
        table_init_monthly_call_history();
        table_data_monthly_call_history(dataset);
    }

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

/*
    $('#task_view_modal').on('shown.bs.modal', function () {
        console.log("Modal Shown");
        $(this).ajaxStart(function(){
        console.log("KDKKD");
        $(".processing_img").show();
    });
    $(this).ajaxComplete(function(){
        $(".processing_img").hide();
        console.log("KDKKD");
    });
}).ajaxSend(function() {
    console.log("Shown!");
    // $(".processing_img").show();
}).ajaxComplete(function(){
    console.log("Hidding!");
    // $(".processing_img").hide();
});*/

}

function generate_collection_tasks(){
    var res = connectServerWithForm(cms_url['save_collection_task'],'user_details');
    var res = JSON.parse(res);
    if( res.status == "success" ){
        alertMessage(this,'blue',"Operation Status",res.msg);
    }else{
        alertMessage(this,'red',"Operation Status",res.msg);
    }
}


function SearchForUsers(){
    var datainfo = {};
    datainfo['user_id'] = $("#user_id").val();
    var res_html = connectServer(cms_url['serach_for_radius_user'], datainfo, false);
    $("#radius_search_result").modal();
    $("#radius_user_details").html(res_html);
    $("#payment_date").datepicker({ autoclose: 1,format: 'yyyy-mm-dd'});
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
    var z_index = $("#task_view_modal").zIndex();
    var res = connectServer(cms_url['save_monthly_call_status'], datainfo, false);
    res = JSON.parse(res);
    if ( res.status === 1 ) {
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