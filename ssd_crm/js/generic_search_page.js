/**
 * Created by Nazibul on 8/5/2015.
 */

function generic_search() {

    var search_index = $('#globalSearch').val();
    if(search_index=='' || search_index==null){
        return;
    }

    displayContent("27", "#cmsData", "#contentListLayout", "ContentID");

    //alert(search_index);//

    var dataInfo = {};
    dataInfo['sample'] = search_index;

    var table_id="generic_search_table";
    $('#tbl_generic_search').html('<table class="table table-striped table-bordered table-hover responsive" id="' + table_id + '" width="100%">' +
    '</table>');

    table_id="#"+table_id;

    var table = $(table_id).dataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: cms_url['gneric_search'],
            type: 'POST',
            data: {'info': dataInfo}
        },
        scrollX: true,
        "columns": [
            {"title": "Manage", "data": "id", "class": "center"},
            {"title": "Contact ID", "data": "contact_id", "class": "center"},
            {"title": "Customer Type", "data": "customer_type", "class": "center"},
            {"title": "Phone", "data": "phone1", "class": "center"},
            {"title": "Email", "data": "email", "class": "center"},
            {"title": "First Name", "data": "first_name", "class": "center"},
            {"title": "Last Name", "data": "last_name", "class": "center"},
            {"title": "Address", "data": "address1", "class": "center"},

        ],
        "order": [[0, "asc"]],
        'iDisplayLength': 15,
        "sDom": 'tp'
    });
    
    //$("div.dataTables_scrollHead").css("margin-left","6%");
}