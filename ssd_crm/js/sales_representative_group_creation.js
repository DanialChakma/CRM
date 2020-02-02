function table_init_sales_representative_group_creation(div_id, table_id) {
    var div_name = "#" + div_id;
    $(div_name).html('<table class="responsive" id="' + table_id + '" width="400px">' +
            '<tr><td  align="center"></td></tr></table>');
}

function report_sales_representative_group_creation(table_id, action) {
    var dataSet = [[]];
    var dataInfo = "";
    dataInfo['action'] = action;
    $('#action').html(action);
    /*
     var date_from = $('#datePickerFrom').val();
     dataInfo += "+" + date_from;
     
     var date_to = $('#datePickerTo').val();
     dataInfo += "+" + date_to;
     */

    dataSet = connectServer(cms_url['sales_representative_group_creation'], dataInfo);
    //  alert(dataSet);
    dataSet = JSON.parse(dataSet);
    //alert(dataSet);
    table_data_sales_representative_group_creation(dataSet, table_id);
    $(".toolbar").css("width", "20%");
    $(".toolbar").css("margin-left", "6%");
    $("div.toolbar").html('<div style="color: #000000;"><b><h4>System Users</h4></b></div>');
    $("div.dataTables_scrollHead").css("display","none");
    $("div.dataTables_scrollBody").css("border-bottom","0px none");
    $("div.dataTables_scroll").css("margin-left","6%");
    $("div.dataTables_scroll").css("padding-bottom","2%");
    
//    table.dataTable thead td {
//    padding: 10px 18px;
//    border-bottom: 1px solid #111;
//}

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

function table_data_sales_representative_group_creation(dataSet, table_id) {
    var table_name = "#" + table_id;
    var table = $(table_name).dataTable({
        "data": dataSet,
        scrollX: true,
        "columns": [
            {"title": "", "class": ""},
            {"title": "", "class": ""}
        ],
        "bLengthChange": false,
        "bFilter" : false,
        "order": [[0, "asc"]],
        "ordering": false,
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

function save_new_group()
{
    var dataSet = [[]];
    var dataInfo = {};
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
    dataInfo['action'] = $('#action').html();
    //console.log(dataInfo);
    if ($('#group_name_input').val() == "")
        alertMessage(this, 'red', '', "Group Name Can't be Blank");
    else {
        dataSet = connectServer(cms_url['save_new_group'], dataInfo);
        dataSet = JSON.parse(dataSet);
        if (dataSet["message"] == 'Successful')
        {
            alertMessage(this, 'green', '', 'Successfully Submitted.');
            showUserMenu('agent_group_show');
        }
        else if (dataSet["message"] == 'Failure')
            alertMessage(this, 'red', '', 'Fail To Create Group');
        else if (dataSet["message"] == 'Exist')
            alertMessage(this, 'red', '', 'This User Group Exist');
    }
}

function get_group_name() {


//   <li class="disabled"><a href="#">Display By:</a></li>
//                                <li class="display display-FirstLast selected"><a href="#" data-setting="display" data-value="FirstLast">First Name</a></li>
    table_show_group();
}

function table_show_group() {
    table_init_sales_representative_group_show('tbl_group_member', 'group_show');
    report_sales_representative_group_show('group_show');
}

function table_init_sales_representative_group_show(div_id, table_id) {
    var div_name = "#" + div_id;
    $(div_name).html('<table class="responsive" id="' + table_id + '" width="500px">' +
            '<tr><td  align="center"></td></tr></table>');
}

function report_sales_representative_group_show(table_id) {
    var dataSet = [[]];
    var dataInfo = {};

//    if(groupID!='all')
//    {
//        dataInfo['condition']=' where groups.id='+groupID;
//    }
    //dataSet = connectServer(cms_url['group_show'], dataInfo);
    //  alert(dataSet);
    var dataSet = connectServer(cms_url['get_group_name'], dataInfo);
    dataSet = JSON.parse(dataSet);
    //alert(dataSet);
    table_data_sales_representative_group_show(dataSet, table_id);
    $(".toolbar").css("width", "20%");
    $(".toolbar").css("margin-left", "35%");
    //$("div.dataTables_scrollHead").css("display","none");
    $("div.dataTables_scrollBody").css("border-bottom","0px none");
    $("div.dataTables_scroll").css("margin-left","6%");
    $("div.dataTables_scroll").css("padding-bottom","2%");
    $("table.dataTable").css("width","400px");
    $("table.dataTable").css("margin","0px");

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

function table_data_sales_representative_group_show(dataSet, table_id) {
    var table_name = "#" + table_id;
    var table = $(table_name).dataTable({
        "data": dataSet,
        scrollX: true,
        "columns": [
            {"title": "Id", "class": "center"},
            {"title": "Group Name", "class": "center"},
            {"title": "#", "class": "center"}
        ],
        "order": [[0, "asc"]],
        "bLengthChange": false,
        "bFilter" : false,
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

function update_group_details(groupID) {

    var dataSet = [[]];
    var dataInfo = {};

    dataInfo['condition'] = groupID;

    dataSet = connectServer(cms_url['group_show'], dataInfo);

    agent_group('update');
    dataSet = JSON.parse(dataSet);
    $('#group_name_input').val(dataSet['name']);
    var i;
    for (i = 0; i < dataSet['length']; i++)
    {
        $("#" + JSON.parse(dataSet[i])).prop('checked', true);
    }
    //$("#" + name).prop('checked', true);
}