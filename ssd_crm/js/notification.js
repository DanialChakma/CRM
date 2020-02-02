/**
 * Created by HP on 9/9/2015.
 */




function report_notification_list() {

    var dataSet = [[]];
    var dataInfo = {};

    dataSet = connectServer(cms_url['doze_crm_get_notification_list'], dataInfo);
    dataSet = JSON.parse(dataSet);
    table_data_notification_list(dataSet);


}


function table_data_notification_list(dataSet) {
    $('#task_list_table').dataTable({
        "data": dataSet,
        "columns": [
            //{"title":"Action", "class": "center"},
            //{"title":"Select","class": "center"},
            {"title":"Tasks Notification","class": "center"},
        ],
        "order": [[0, "asc"]],
        dom: 'tip',
        'iDisplayLength': 30,
        scrollX:true
    });
}


function delete_notification(id){

    var dataSet = [[]];
    var dataInfo = {};

    dataInfo['id'] = id;
    dataInfo['type'] = 'single';

    dataSet = connectServer(cms_url['doze_crm_update_notification_list'], dataInfo);
    dataSet = JSON.parse(dataSet);

    if(dataSet.status){
        showUserMenu('index');
    }
}



function delete_notification_list() {
    var data = "";
    var searchIDs = new Array();
    var searchIDs = $("input:checkbox:checked").map(function () {
        return $(this).val();
    }).get();

    var len = searchIDs.length;

    if (len == 0) {
        alertMessage(this, 'red', 'Failure', 'Please select entry to delete.');
        return false;
    }

    $.each(searchIDs, function (index, value) {
        if (index == (len - 1)) {
            data += value;
        } else {
            data += value + "|";
        }

    });


    var dataSet = [[]];
    var dataInfo = {};

    dataInfo['id'] = data;
    dataInfo['type'] = 'multiple';

    dataSet = connectServer(cms_url['doze_crm_update_notification_list'], dataInfo);
    dataSet = JSON.parse(dataSet);

    if(dataSet.status){
        showUserMenu('index');
    }
}