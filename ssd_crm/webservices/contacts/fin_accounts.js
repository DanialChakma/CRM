/**
 * Created by L440-User on 8/12/2016.
 */

// this function initiate or update fintelligent bill
function request_for_bill(){
    // Customer Info
    var customer_id = $("#contact_id").val();
    var first_name = $("#first_name").val();
    var last_name = $("#last_name").val();
    var customer_name = first_name+' '+last_name;
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

    float_installation_cost = isNaN(float_installation_cost) ? 0:float_installation_cost;
    float_monthly_cost = isNaN(float_monthly_cost) ? 0:float_monthly_cost;
    float_number_of_month = isNaN(float_number_of_month) ? 0:float_number_of_month;
    float_real_ip_cost = isNaN(float_real_ip_cost) ? 0:float_real_ip_cost;
    float_additional_cost = isNaN(float_additional_cost) ? 0:float_additional_cost;

    var total_cost_calculation = float_installation_cost + (float_monthly_cost*float_number_of_month) + float_real_ip_cost + float_additional_cost;

    var total_cost = parseFloat(parseFloat($("#total_cost").val()).toFixed(4));
    total_cost = isNaN(total_cost)?0:total_cost;

    if( (total_cost_calculation > 0.0 && total_cost > 0.0) && (total_cost_calculation === total_cost) ){

    }else{
        alertMessage(this,"red","Message","Computed total cost and total cost field mismatch");
        return;
    }

    var data ={};
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

    var response = connectServer(cms_url['initiate_fin_bill'], data,false);
    alert("KKK");
    return;
    response = JSON.parse(response);
    if( parseInt(response.code) === 1 ){
        alertMessage(this,"green","Operation Status",response.msg);
    }else{
        alertMessage(this,"red","Operation Status",response.msg);
    }
}



function table_init_accounts_task() {
    $('#tbl_accounts_task').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="accounts_task" width="85%"  ><tr><td  align="center">&nbsp;</td></tr></table>');
}
function table_data_accounts_task(dataSet) {
    $('#accounts_task').dataTable({
        "data": dataSet,
        "columns": [
            {"title": "Customer Name", "class": "center"},
            {"title":"Contact No","class":"center"},
           // {"title":"Conversion Date","class":"center"},
            {"title":"Collection Date","class":"center" },
            {"title":"Status","class":"center"}
        ],
        "bFilter": false,
        "bLengthChange": false,
        "order": [[0, "asc"]],
        dom: 'T<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "doze_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                 "copy", "csv",
                {
                    "sExtends": "xls",
                    "sFileName": "*.xls"
                }
            ],
            "filter": "applied"
        }
    });
}


function view_task_details(customer_id){
    // $("#tbl_accounts_task").hide();
    var datainfo = {};
    datainfo['customer_id'] = customer_id.trim();
    var res_html = connectServer(cms_url['get_task_details'], datainfo,false);
    $("#task_view_modal").modal();
    $("#task_detail_content").html(res_html);
}

function view_account_task_details(bill_id){
    var datainfo = {};
    datainfo['transaction_id'] = bill_id;
    var res_html = connectServer(cms_url['get_task_details_of_bill_id'], datainfo,false);
    $("#task_view_modal").modal();
    $("#transaction_id").val(bill_id);
    $("#task_detail_content").html(res_html);
}


function view_accounts_tasks(){
    var datainfo = {};
    datainfo['agent'] = "";
    var dataset = connectServer(cms_url['agent_wise_accounts_task'], datainfo,false);
    dataset = JSON.parse(dataset);
    table_init_accounts_task();
    table_data_accounts_task(dataset);
}

function hold_task(){
    var bill_id = $("#transaction_id").val();
    var remarks = $("#remarks").val();
    var datainfo = {};
    datainfo['bill_id'] = bill_id;
    datainfo['remarks'] = remarks;
    datainfo['state'] = "Hold";

    var res = connectServer(cms_url['hold_accounts_task'], datainfo,false);
    res=JSON.parse(res);
    if( parseInt(res.code) === 1 ){
        alertMessage(this,"blue","Status",res.msg);
    }else{
        alertMessage(this,"red","Status",res.msg);
    }
}


function approve_task(){
    var bill_id = $("#transaction_id").val();
    var collected_amount = $("#collected_amount").val();
    var receipt_number = $("#receipt_number").val();
    var remarks = $("#remarks").val();
    var nid = $("#nid").val();
    var photo = $("#photo").val();
    var sap = $("#sap").val();

    var float_collected_amount = parseFloat(parseFloat(collected_amount).toFixed(4));

    var datainfo = {};
    datainfo['bill_id'] = bill_id;
    datainfo['receipt_number'] = receipt_number;
    datainfo['collected_amount'] = float_collected_amount;
    datainfo['remarks'] = remarks;
    datainfo['nid'] = nid;
    datainfo['photo'] = photo;
    datainfo['sap'] = sap;
    datainfo['state'] = "Approve";
    if( !float_collected_amount || float_collected_amount < 0 || float_collected_amount == "" ){
        $("#errors").html("Please Check Collected Amount");
       // alertMessage(this,"red","Information","Please Check Collected Amount");
        return;
    }
    if( !receipt_number || receipt_number == "" || receipt_number == "undefined" ){
        $("#errors").html("Please Check Receipt Number");
        //alertMessage(this,"red","Information","Please Check Receipt Number");
        return;
    }
    if( !nid || nid == "" || nid == "undefined" ){
        $("#errors").html("Please Select NID");
       // alertMessage(this,"red","Information","Please Select NID");
        return;
    }
    if( !photo || photo == "" || photo == "undefined" ){
        $("#errors").html("Please Select Photo");
        //alertMessage(this,"red","Information","Please Select Photo");
        return;
    }
    if( !sap || sap == "" || sap == "undefined" ){
        $("#errors").html("Please Select SAP");
        //alertMessage(this,"red","Information","Please Select SAP");
        return;
    }

    var res = connectServer(cms_url['approve_accounts_task'], datainfo,false);
    res=JSON.parse(res);

    if( parseInt(res.code) === 1 ){
        alertMessage(this,"blue","Status",res.msg);
    }else{
        alertMessage(this,"red","Status",res.msg);
    }
}


function reject_task(){
    var bill_id = $("#transaction_id").val();
    var remarks = $("#remarks").val();
    var datainfo = {};
    datainfo['bill_id'] = bill_id;
    datainfo['remarks'] = remarks;
    datainfo['state'] = "Reject";

    var res = connectServer(cms_url['reject_accounts_task'], datainfo,false);
    res=JSON.parse(res);
    if( parseInt(res.code) === 1 ){
        alertMessage(this,"blue","Status",res.msg);
    }else{
        alertMessage(this,"red","Status",res.msg);
    }
}


function  table_init_manager_task(){
    $('#tbl_accounts_task').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="accounts_task" width="85%"  ><tr><td  align="center">&nbsp;</td></tr></table>');

}

function table_data_manager_task(dataset){
    $('#accounts_task').dataTable({
        "data": dataset,
        select: {
            style:    'os',
            selector: 'td:first-child'
        },
        "columns": [
            {"title": "","class": "center"},

            {"title": "Customer Name", "class": "center"},
            // {"title":"Conversion Date","class":"center"},
            {"title":"Phone","class":"center" },
            {"title":"Package","class":"center" },
            {"title":"InstallCost","class":"center" },
            {"title":"MonthlyCost","class":"center" },
            {"title":"MonthNumber","class":"center" },
            {"title":"RealIPCost","class":"center" },
            {"title":"AdditionalCost","class":"center" },
            {"title":"TotalCost","class":"center" },
            {"title":"Status","class":"center" }
        ],
        "bFilter": true,
        "bLengthChange": false,
        "order": [[0, "asc"]],
        dom: 'T<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "doze_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                "copy", "csv",
                {
                    "sExtends": "xls",
                    "sFileName": "*.xls"
                }
            ],
            "filter": "applied"
        }
    });
}

function get_account_manager_tasks(){
    var datainfo = {};
    alert("DKKKDK");
    var start_date = $("#start_date").val();
    var end_date   = $("#end_date").val();
    datainfo['manager'] = "";
    datainfo['start_date'] = start_date;
    datainfo['end_date'] = end_date;
    var dataset = connectServer(cms_url['get_account_manager_task'], datainfo,false);
   // console.log("Log::",dataset);
    dataset = JSON.parse(dataset);
    table_init_manager_task();
    table_data_manager_task(dataset);
}


function table_init_task_reassign(){
    $('#tbl_accounts_task').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="accounts_task" width="85%"  ><tr><td  align="center">&nbsp;</td></tr></table>');
}

function table_data_task_reassign(dataset){
    $('#accounts_task').DataTable({
        select: false,
        "data": dataset,
        "columns": [
            {"title": "id", "class": "center"},
            {"title": "Customer ID", "class": "center"},
            // {"title":"Conversion Date","class":"center"},
            {"title":"Details","class":"center" },
            {"title":"Reassign","class":"center" }
        ],
        "bFilter": false,
        "bLengthChange": false,
        "order": [[0, "asc"]],
        dom: 'T<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "doze_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                "copy", "csv",
                {
                    "sExtends": "xls",
                    "sFileName": "*.xls"
                }
            ],
            "filter": "applied"
        }
    });
}



function view_manager_task_details(bill_id){

    var status = $("#"+bill_id.trim()).val();

    var datainfo = {};
    datainfo['transaction_id'] = bill_id;
    var res_html = connectServer(cms_url['get_manager_task_details_of_bill_id'], datainfo,false);
    $("#task_view_modal").modal();
    $("#approve_manager_task").show();
    $("#execute_manager_task").show();
    if( status == "Approve" ){
        $("#approve_manager_task").hide();
        $("#execute_manager_task").show();
    }else{
        $("#execute_manager_task").hide();
    }

    $("#transaction_id").val(bill_id);
    $("#task_detail_content").html(res_html);
}


function approve_manager_task(){

    var transaction_id = $("#transaction_id").val();
    var collected_amount = $("#collected_amount").val();
    var receipt_number = $("#receipt_number").val();
    var remarks = $("#remarks").val();
    var nid = $("#nid").val();
    var photo = $("#photo").val();
    var sap = $("#sap").val();
    var datainfo = {};
    datainfo['bill_id'] = transaction_id;
    datainfo['collected_price'] = collected_amount;
    datainfo['receipt'] = receipt_number;
    datainfo['remarks'] = remarks;
    datainfo['nid'] = nid;
    datainfo['photo'] = photo;
    datainfo['sap'] = sap;

    var res = connectServer(cms_url['approve_manager_task'], datainfo,false);
    res = JSON.parse(res);
    if( parseInt(res.code) === 1 ){
        alertMessage(this,"blue","Operation Status",res.msg);
    }else{
        alertMessage(this,"red","Operation Status",res.msg);
    }
}

function execute_manager_task(){

    var transaction_id = $("#transaction_id").val();
    var collected_amount = $("#collected_amount").val();
    var receipt_number = $("#receipt_number").val();
    var remarks = $("#remarks").val();
    var nid = $("#nid").val();
    var photo = $("#photo").val();
    var sap = $("#sap").val();
    var datainfo = {};
    datainfo['bill_id'] = transaction_id;
    datainfo['collected_price'] = collected_amount;
    datainfo['receipt'] = receipt_number;
    datainfo['remarks'] = remarks;
    datainfo['nid'] = nid;
    datainfo['photo'] = photo;
    datainfo['sap'] = sap;

    var res = connectServer(cms_url['execute_manager_task'], datainfo,false);
    res = JSON.parse(res);
    if( parseInt(res.code) === 1 ){
        alertMessage(this,"blue","Operation Status",res.msg);
    }else{
        alertMessage(this,"red","Operation Status",res.msg);
    }
}


function hold_manager_task(){

    var transaction_id = $("#transaction_id").val();
    var collected_amount = $("#collected_amount").val();
    var receipt_number = $("#receipt_number").val();
    var remarks = $("#remarks").val();
    var nid = $("#nid").val();
    var photo = $("#photo").val();
    var sap = $("#sap").val();
    var datainfo = {};
    datainfo['bill_id'] = transaction_id;
    datainfo['collected_price'] = collected_amount;
    datainfo['receipt'] = receipt_number;
    datainfo['remarks'] = remarks;
    datainfo['nid'] = nid;
    datainfo['photo'] = photo;
    datainfo['sap'] = sap;
    var res = connectServer(cms_url['hold_manager_task'], datainfo,false);
    res = JSON.parse(res);
    if( parseInt(res.code) === 1 ){
        alertMessage(this,"blue","Operation Status",res.msg);
    }else{
        alertMessage(this,"red","Operation Status",res.msg);
    }
}

function reject_manager_task(){

    var transaction_id = $("#transaction_id").val();
    var collected_amount = $("#collected_amount").val();
    var receipt_number = $("#receipt_number").val();
    var remarks = $("#remarks").val();
    var nid = $("#nid").val();
    var photo = $("#photo").val();
    var sap = $("#sap").val();
    var datainfo = {};
    datainfo['bill_id'] = transaction_id;
    datainfo['collected_price'] = collected_amount;
    datainfo['receipt'] = receipt_number;
    datainfo['remarks'] = remarks;
    datainfo['nid'] = nid;
    datainfo['photo'] = photo;
    datainfo['sap'] = sap;
    var res = connectServer(cms_url['reject_manager_task'], datainfo,false);
    res = JSON.parse(res);
    if( parseInt(res.code) === 1 ){
        alertMessage(this,"blue","Operation Status",res.msg);
    }else{
        alertMessage(this,"red","Operation Status",res.msg);
    }
}



function get_tasks_to_reassign(){
    var datainfo = {};
    datainfo['manager'] = "";
    var dataset = connectServer(cms_url['get_task_for_reassign'], datainfo,false);
    // console.log("Log::",dataset);
    dataset = JSON.parse(dataset);
    table_init_task_reassign();
    table_data_task_reassign(dataset);
}


function assign_task_to(){
    var datainfo = {};
    var ids = Array();
    var assign_id   = document.getElementById('agent_select').value;
    var reassign_date   = document.getElementById('reassign_date').value;
    var table       = document.getElementById('accounts_task');

    var len = table.rows.length;
    var i;
    for( i=1; i<len; i++ ){
        var bill_id = table.rows[i].cells[0].children[0].value;
        if( table.rows[i].cells[0].children[0].checked ){
            bill_id = bill_id.trim()
            ids.push(bill_id);
        }
    }

    datainfo['reassign_date'] = reassign_date;
    datainfo['assign_id'] = assign_id;
    datainfo['ids'] = ids;
    var res = connectServer(cms_url['assign_task_to_agent'], datainfo,false);
    res = JSON.parse(res);
    var len = res.length;
    var msg_str = "";
    for(i=0;i<len;i++){
        if( msg_str == "" ){
            msg_str = res[i].bill_id +","+res[i].msg;
        }else{
            msg_str+= "<br/>"+res[i].bill_id +","+res[i].msg;
        }
    }

    alertMessage(this,"blue","Operation Status",msg_str);

}


function generate_call_list_page(){
    displayContent("50", "#cmsData", "#contentListLayout", "ContentID");
    $('#renew_date').datetimepicker({
        format: 'yyyy-mm-dd',
        autoclose: 1,
        todayHighlight: 1
    });
}


function generate_call_list(){
    var datainfo = {};

    var renew_date =  $("#renew_date").val();
    datainfo['renew_date'] = renew_date;
    var res = connectServer(cms_url['generate_call_list'], datainfo,false);
    res = JSON.parse(res);
    if( res.status == 1 ){
        alertMessage(this,"blue","Operation Status",res.msg);
    }else{
        alertMessage(this,"red","Operation Status",res.msg);
    }

}


/*
function table_init_call_assign(){
    $('#tbl_monthly_call').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="tbl_monthly_call_assign" width="85%"  ><tr><td  align="center">&nbsp;</td></tr></table>');
}*/

function table_init_call_assign(){
    $('#tbl_monthly_call').html('<table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" id="tbl_monthly_call_assign" width="65%" ><tr><td  align="center">&nbsp;</td></tr></table>');
}

function table_data_call_assign(dataset){
    $('#tbl_monthly_call_assign').DataTable({
        responsive:true,
        select: false,
        "data": dataset,
        "columns": [
            {"title": "", "class": "center"},
            {"title": "Email", "class": "center"},
            {"title": "Renew Date", "class": "center"},
            {"title":"Assign To","class":"center" }
        ],
        "bFilter": false,
        "bLengthChange": false,
        "order": [[0, "asc"]],
        dom: 'T<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "doze_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                "copy", "csv",
                {
                    "sExtends": "xls",
                    "sFileName": "*.xls"
                }
            ],
            "filter": "applied"
        }
    });
}


function get_call_list(){
    var datainfo = {};
    datainfo['renew_date'] = $("#renew_date").val();
    var dataset = connectServer(cms_url['get_call_list'], datainfo,false);
    dataset = JSON.parse(dataset);
    table_init_call_assign();
    table_data_call_assign(dataset);
}

function assign_call_page(){
    displayContent("51", "#cmsData", "#contentListLayout", "ContentID");
    calenderDatePickerOnlyDate(1, "get_call_list");
    fetchDropDownOptionHtml("#assign_to",cms_url['get_accounts_agent']);
    var datainfo = {};
    datainfo['renew_date'] = $("#renew_date").val();
    var dataset = connectServer(cms_url['get_call_list'], datainfo,false);
    // console.log("Log::",dataset);
    dataset = JSON.parse(dataset);
    table_init_call_assign();
    table_data_call_assign(dataset);
}

function assign_call_to_agent(){

    var ids = Array();
    var assign_id   = document.getElementById('assign_to').value;
    var table       = document.getElementById('tbl_monthly_call_assign');

    var len = table.rows.length;
    //var ids_str = "";
    var i;
    for( i=1; i<len; i++ ){
        var bill_id = table.rows[i].cells[0].children[0].value;
        if( table.rows[i].cells[0].children[0].checked ){
            bill_id = bill_id.trim()
            ids.push(bill_id);
          //  ids_str+=","+bill_id;
        }
    }

    var datainfo = {};
    datainfo['assign_id'] = assign_id;
    datainfo['ids'] = ids;

    var res = connectServer(cms_url['assign_call_to_agent'], datainfo,false);
    res = JSON.parse(res);
    if(res.status == 1 ){
        alertMessage(this,"blue","Operation Status",res.msg);
        get_call_list();
    }else{
        alertMessage(this,"red","Operation Status",res.msg);
    }

}

function table_init_monthly_call(){
    $('#tbl_monthly_call').html('<table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" id="tbl_monthly_call_list" width="65%" ><tr><td  align="center">&nbsp;</td></tr></table>');
}
function table_data_monthly_call(dataset){
    $('#tbl_monthly_call_list').DataTable({
        responsive:true,
        select: false,
        "data": dataset,
        "columns": [
            {"title": "Customer Name", "class": "center"},
            {"title": "Contacts", "class": "center"}
        ],
        "bFilter": true,
        "bLengthChange": false,
        "order": [[0, "asc"]],
        dom: 'T<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "doze_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                "copy", "csv",
                {
                    "sExtends": "xls",
                    "sFileName": "*.xls"
                }
            ],
            "filter": "applied"
        }
    });
}
function monthly_call_page(){
    displayContent("52", "#cmsData", "#contentListLayout", "ContentID");
    //calenderDatePickerOnlyDate(1, "get_call_list");
  //  fetchDropDownOptionHtml("#assign_to",cms_url['get_accounts_agent']);
    var datainfo = {};
    var dataset = connectServer(cms_url['get_monthly_call_list'], datainfo,false);
    // console.log("Log::",dataset);
    dataset = JSON.parse(dataset);
    table_init_monthly_call();
    table_data_monthly_call(dataset);
}

function show_monthly_details(contact_id){

    var datainfo = {};

    datainfo['customer_id'] = contact_id;
    var res_html = connectServer(cms_url['get_monthly_call_details'], datainfo,false);
    $("#task_view_modal").modal();
    $("#contact_id").val(contact_id);
    $("#task_detail_content").html(res_html);
    $('#follow_up_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: 1,
        todayHighlight: 1
    });
}

function call_status_change(){
    var call_status = $("#call_status").val();
    if( call_status == "connected" ){
        $("#outcome").show();
    }else{
        $("#outcome").hide();
    }
}

function outcome_change(){
    var outcome = $("#outcome").val();
    if( outcome == "1" ){
        $("#follow_up_date").show();
    }else{
        $("#follow_up_date").hide();
    }
}

function save_monthly_call_status(){

    var datainfo = {};
    var contact_id = $("#contact_id").val();
    var call_status = $("#call_status").val();
    var outcome = $("#outcome").val();
    var mbcl_id = $("#mbcl_id").val();
    var follow_up_date = $("#follow_up_date").val();
    var feedback = $("#remarks").val();
    datainfo['contact_id'] = contact_id;
    datainfo['call_status'] = call_status;
    datainfo['outcome'] = outcome;
    datainfo['follow_up_date'] = follow_up_date;
    datainfo['feedback'] = feedback;
    datainfo['mbcl_id'] = mbcl_id;
    var res = connectServer( cms_url['save_monthly_call_status'], datainfo,false );
    res = JSON.parse(res);
    if( res.status == 0 ){
        alertMessage(this,"blue","Operation Status",res.msg);
    }else{
        alertMessage(this,"red","Operation Status",res.msg);
    }

}

