
function change_func(form_id)
{
    excel_controller_contact_import_old(form_id);

    /*
    //this portion is for new import system that use column mapping......
    var fchange = $("#change_function").val();
    if (fchange == 0)
    {
        excel_controller_contact_import(form_id);
        fchange++;
        $("#change_function").val(fchange);
    }
    else
    {
        save_excel(form_id);
    }
    */

}

function excel_controller_contact_import(form_id) {
    // call connectServerWithForm with a php url and for id
    var d = new Date();
    var n = d.getTime();
    $("#row_count").val(n);

    excel_controller_contact_import_new(form_id);

}

function excel_controller_contact_import_new(form_id) {
    // call connectServerWithForm with a php url and for id

    var response = connectServerWithFileUpload(cms_url['excel_coulmmn_name'], form_id);
    //var formData = new FormData(document.getElementById(form_id));
    alert("ok");
    response = JSON.parse(response);
    //console.log(response['sql_column']);

    var tableStr = '<table syle="white-spacing:nowrap;" class="upload-table" cellspacing="0" cellpadding="0">';
    tableStr = tableStr + '<tbody>';
    tableStr = tableStr + '<tr>';
    tableStr = tableStr + '<th class="first-th">Doze CRM Field</th>';
    tableStr = tableStr + '<th>Column Name</th>';
    tableStr = tableStr + '<th>Your Data</th>';
    tableStr = tableStr + '</tr>';
    for (var i = 0; i < response['excel-column'][0].length; i++)
    {
        tableStr = tableStr + '<tr>';
        tableStr = tableStr + '<td><select name="mapping_field_0" class="selectpicker show-tick left5 options" ></select></td>';
        tableStr = tableStr + '<td>' + response['excel-column'][0][i] + '</td>';
        tableStr = tableStr + '<td>' + response['data'][0][i] + '</td>';
        tableStr = tableStr + '</tr>';
    }
    tableStr = tableStr + '</tbody>';
    tableStr = tableStr + '</table>';
    $("#upload-table").html(tableStr);
    $("#duplicate_option").html(response['duplicate_option']);
    $("#phone_validation_option").html(response['duplicate_option']);
    $(".options").html(response['sql_column']);
    $("div.past-info").css("display", "block");
    $("#file_form").css("margin", "0% 0% 0% 0%");
}

function save_excel(form_id) {
    var inputs = $(".options");
    var values = {};
    var valueIndex = {};
    var duplicate = {};
    var j = 0;
    for (var i = 0; i < inputs.length; i++) {
        if (typeof values[$(inputs[i]).val()] !== 'undefined')
        {
            values[$(inputs[i]).val()]++;
        }
        else {
            values[$(inputs[i]).val()] = 1;
            valueIndex[j] = $(inputs[i]).val();
            j++;
        }
    }
    var k = 0;
    var duplicatestr = "(";
    for (var i = 0; i < Object.size(valueIndex); i++)
    {
        if (values[valueIndex[i]] > 1)
        {
            if (duplicatestr != "(") {
                duplicatestr = duplicatestr + ",";
            }
            duplicate[k] = valueIndex[i];
            duplicatestr = duplicatestr + valueIndex[i];
            k++;

        }
    }
    //console.log(values);
    //alert("ok");
    var data = {};
    data['value_index'] = valueIndex;
    data['duplicate'] = $("#duplicate_option").val();
    data['phone_validate'] = $("#phone_validation_option").val();
    duplicatestr = duplicatestr + ")";
    if (Object.size(duplicate) > 0)
        alertMessage(this, 'red', '', Object.size(duplicate) + ' Duplicate Column Selected ' + duplicatestr);
    else
    {
        var response_data = connectServerWithFileUpload(cms_url['excel_contact_import'], form_id, valueIndex, data['duplicate'],data['phone_validate']);
        try {
            response_data = JSON.parse(response_data);
            // alert message for success.
            if (response_data.status == true) {
                $("#import_message").html(response_data.message);
                alertMessage(this, 'green', 'Success', '  Success');
            } else {
                // excel_controller_contact_import_new(form_id);

            }
        } catch (exception) {
            excel_controller_contact_import_new(form_id);

        }
    }

}

Object.size = function (obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key))
            size++;
    }
    return size;
};

/*===================================================================================
 * update data
 *==================================================================================== */
function excel_controller_contact_import_old(form_id) {
    // call connectServerWithForm with a php url and for id
    var d = new Date();
    var n = d.getTime();
    $("#row_count").val(n);

    var response = connectServerWithFileUpload(cms_url['excel_contact_import_old'], form_id);
    try {
        response = JSON.parse(response);
        // alert message for success.
        if (response.status == true) {
            $("#import_message").html(response.message);
            alertMessage(this, 'green', 'Success', '  Success');
        } else {
            // excel_controller_contact_import_new(form_id);

        }
    } catch (exception) {
        excel_controller_contact_import_new(form_id);

    }


}

