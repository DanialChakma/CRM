function table_init_sales_representative(div_id, table_id) {
    var div_name = "#" + div_id;
    $(div_name).html('<table class="table table-striped table-bordered table-hover responsive" id="' + table_id + '" width="100%">' +
        '<tr><td  align="center"></td></tr></table>');
}

function report_sales_representative(table_id) {
    var dataSet = [[]];
    var dataInfo = "";
    dataInfo = "test";
    /*
     var date_from = $('#datePickerFrom').val();
     dataInfo += "+" + date_from;

     var date_to = $('#datePickerTo').val();
     dataInfo += "+" + date_to;
     */

    dataSet = connectServer(cms_url['sales_representative'], dataInfo);
    //  alert(dataSet);
    dataSet = JSON.parse(dataSet);
    //alert(dataSet);
    table_data_sales_representative(dataSet, table_id);
    $(".toolbar").css("width", "20%");
    $(".toolbar").css("margin-left", "35%");
    $("div.toolbar").html('<div style="color: #000000;"><b><h4>System Users</h4></b></div>');

}
/*
 function user_data()
 {

 var id=$.session.get('user');
 var dataInfo = id;
 dataSet = connectServer(cms_url['update_user'], dataInfo);
 alert(dataSet);
 }
 */

function delete_user_info(user_id) {

    var data = {};
    data['user_id'] = user_id;
    var res = connectServer(cms_url['delete_user_info'], data, false);
    try {
        res = JSON.parse(res);
        if(res.status == "yes"){
            alertMessage(this, 'green', 'Success', 'Successful');
            showUserMenu('agent_table');
        }else{
            alertMessage(this, 'red', 'Failure', 'Failed!!');
        }
    }catch (ex){


    }
}
function update_user_info(user_info) {
    showUserMenu("agent_registration");
    $('#user_id').val(user_info['user_id']);
    $('#user_name').val(user_info['user_name']);
    $('#working_schedule').val(user_info['working_schedule']);
    $('#user_password').val(user_info['user_password']);
    $('#user_address').val(user_info['user_address']);
    $('#retype_user_password').val(user_info['user_password']);
    $('#user_phone').val(user_info['user_phone']);
    $('#first_name').val(user_info['first_name']);
    $('#user_alt_phone').val(user_info['user_alt_phone']);
    $('#last_name').val(user_info['last_name']);
    $('#user_role').val(user_info['user_role']);
    $('#user_email').val(user_info['user_email']);
    // dataSet = connectServer(cms_url['update_user'], user_info);

}

function table_data_sales_representative(dataSet, table_id) {
    var table_name = "#" + table_id;
    var table = $(table_name).dataTable({
        "data": dataSet,
        scrollX: true,
        "columns": [
            {"title": "User ID", "class": "center"},
            {"title": "First Name", "class": "center"},
            {"title": "Last Name", "class": "center"},
            {"title": "Address", "class": "center"},
            {"title": "Contact", "class": "center"},
            {"title": "#", "class": "center"}

        ],
        "order": [[0, "asc"]],
        'iDisplayLength': 20,
        dom: 'lTf<"toolbar">t<"bottom"rip>',
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
