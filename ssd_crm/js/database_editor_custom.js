/**
 * Created by Nazibul on 9/6/2015.
 */


function update_database_row(table_name, update_col_name, update_col_value, search_col_name, search_col_value) {
    var dataSet = [[]];
    var dataInfo = {};
    dataInfo['action'] = 'update_single_row';
    dataInfo['table_name'] = table_name;
    dataInfo['update_col_name'] = update_col_name;
    dataInfo['update_col_value'] = update_col_value;
    dataInfo['search_col_name'] = search_col_name;
    dataInfo['search_col_value'] = search_col_value;

    var response = connectServer(cms_url['database_editor_custom'], dataInfo);

    dataSet = JSON.parse(response);

    if (dataSet.status) {
        //return dataSet;
    } else {
        alertMessage(this, 'red', '', 'error');
    }
}

function delete_database_row(table_name, search_col_name, search_col_value) {
    var dataSet = [[]];
    var dataInfo = {};
    dataInfo['action'] = 'delete_single_row';
    dataInfo['table_name'] = table_name;
    dataInfo['search_col_name'] = search_col_name;
    dataInfo['search_col_value'] = search_col_value;

    var response = connectServer(cms_url['database_editor_custom'], dataInfo);

    dataSet = JSON.parse(response);

    if (dataSet.status) {
        //return dataSet;
    } else {
        alertMessage(this, 'red', '', 'error');
    }
}

function update_database_row_with_form(form_id){
    $('#action').val('update');
    var response = connectServerWithForm(cms_url['database_editor_with_form'], form_id);

}

function insert_database_row_with_form(form_id){
    $('#action').val('save');
    var response = connectServerWithForm(cms_url['database_editor_with_form'], form_id);

}