/* client_type */
$(document).on("change", "#client_type", function() {
    var client_type = document.getElementById("client_type").value;
    if (client_type == 'home') {
        document.getElementById("first_name_text").innerHTML = "First Name";
        document.getElementById("last_name_text").innerHTML = "Last Name";
    } else if (client_type == 'corporate') {
        document.getElementById("first_name_text").innerHTML = "Company name";
        document.getElementById("last_name_text").innerHTML = "Contact person";
    }

});

function show_contacts(page) {
    var dataSet = [
        []
    ];
    var dataInfo = {};
    var stored_page;
    var general_filter = $('#previous_filter').val();
    var filter_by_lead_source = $('#lead_source_filter').val();


    if (typeof(Storage) !== "undefined") {
        if (page.length != 1)
            localStorage.setItem("page", page);
        else
            stored_page = localStorage.getItem("page");
    }
    if (page == "all") {
        if (filter_by_lead_source != '-1') {
            dataInfo['page'] = "" + ' lead_source like"%' + filter_by_lead_source + '%" ';
        } else {
            dataInfo['page'] = "";
        }


    } else if (page == "unasigned_lead") {
        if (filter_by_lead_source != '-1') {
            dataInfo['page'] = ' ( customer_type="lead" AND (assign_to <=0 OR assign_to IS NULL) ) ' + ' and lead_source like "%' + filter_by_lead_source + '%" ';

        } else {
            dataInfo['page'] = ' ( customer_type="lead" AND (assign_to <=0 OR assign_to IS NULL)) ';
        }
    } else if (page == "unasigned_block") {

        if (filter_by_lead_source != '-1') {

            dataInfo['page'] = ' ( customer_type="block" AND (assign_to <=0 OR assign_to IS NULL) ) ' + ' and lead_source like"%' + filter_by_lead_source + '%" ';

        } else {
            dataInfo['page'] = ' ( customer_type="block" AND (assign_to <=0 OR assign_to IS NULL) ) ';
        }
    } else if (page == "lead") {
        if (filter_by_lead_source != '-1') {
            dataInfo['page'] = ' customer_type="lead" ' + ' and lead_source like "%' + filter_by_lead_source + '%" ';

        } else {
            dataInfo['page'] = ' customer_type="lead" ';
        }

    } else if (page == "prospect") {

        if (filter_by_lead_source != '-1') {
            dataInfo['page'] = ' customer_type="prospect" ' + ' and lead_source like "%' + filter_by_lead_source + '%" ';

        } else {
            dataInfo['page'] = ' customer_type="prospect" ';
        }
    } else if (page == "customer") {

        if (filter_by_lead_source != '-1') {
            dataInfo['page'] = ' customer_type="customer" ' + ' and lead_source like "%' + filter_by_lead_source + '%" ';
        } else {
            dataInfo['page'] = ' customer_type="customer" ';
        }
    } else if (page == "closed") {
        if (filter_by_lead_source != '-1') {
            dataInfo['page'] = ' customer_type="closed" ' + ' and lead_source like "%' + filter_by_lead_source + '%" ';

        } else {
            dataInfo['page'] = ' customer_type="closed" ';
        }

    } else if (page == "block") {

        if (filter_by_lead_source != '-1') {
            dataInfo['page'] = ' customer_type="block" ' + ' and lead_source like "%' + filter_by_lead_source + '%" ';

        } else {
            dataInfo['page'] = ' customer_type="block" ';
        }
    } else if (page == "l7") {
        dataInfo['page'] = 'l7';
    } else if (stored_page == "all" && page.length == 1) {
        if (page == "#")
            dataInfo['page'] = "";
        else
            dataInfo['page'] = " ((first_name like '" + higher + "%' or first_name like '" + page + "%') or ((first_name = '' or first_name = NULL)and (last_name like '" + higher + "%' or last_name like '" + page + "%'))) ";
    } else if (page.length == 1) {
        if (stored_page == "all") {
            if (page == "#")
                dataInfo['page'] = "";
            else
                dataInfo['page'] = " ((first_name like '" + higher + "%' or first_name like '" + page + "%') or ((first_name = '' or first_name = NULL)and (last_name like '" + higher + "%' or last_name like '" + page + "%'))) ";
        } else if (stored_page == "l7") {
            if (page == "#")
                dataInfo['page'] = 'l7';
            else {
                dataInfo['page'] = 'l7';
                dataInfo['alpha'] = page;
            }
        } else {
            var lower = page.charCodeAt(0);
            var higher;
            higher = String.fromCharCode(lower + 32);
            if (page == "#")
                dataInfo['page'] = ' customer_type="' + stored_page + '"';
            else
                dataInfo['page'] = ' customer_type="' + stored_page + '"' + "and ((first_name like '" + higher + "%' or first_name like '" + page + "%') or ((first_name = '' or first_name = NULL)and (last_name like '" + higher + "%' or last_name like '" + page + "%')))";
        }


    } else if (page == "assign_lead") {
        var cms_auth = checkSession('cms_auth');
        cms_auth = JSON.parse(cms_auth);


        if (filter_by_lead_source != '-1') {
            dataInfo['page'] = ' ( customer_type="lead" AND assign_to =' + cms_auth.user_id + ' ) and lead_source like "%' + filter_by_lead_source + '%" ';

        } else {
            dataInfo['page'] = ' ( customer_type="lead" AND assign_to =' + cms_auth.user_id + ' ) ';
        }

    } else if (page == 'Connected' || general_filter == 'Connected') {
        var cms_auth = checkSession('cms_auth');
        cms_auth = JSON.parse(cms_auth);
        if (filter_by_lead_source != '-1') {
            dataInfo['page'] = ' stage_id =2 and lead_source like "%' + filter_by_lead_source + '%" ';
        } else {
            dataInfo['page'] = ' stage_id =2 ';
        }
        if (cms_auth.user_role != 'Admin') {

            dataInfo['page'] = dataInfo['page'] + ' AND assign_to =' + cms_auth.user_id;
        }
        //  console.log(cms_auth);
    } else if (page == 'Not_Connected' || general_filter == 'Not_Connected') {
        var cms_auth = checkSession('cms_auth');
        cms_auth = JSON.parse(cms_auth);
        if (filter_by_lead_source != '-1') {
            dataInfo['page'] = ' stage_id =3 and lead_source like "%' + filter_by_lead_source + '%" ';
        } else {
            dataInfo['page'] = ' stage_id =3 ';
        }
        if (cms_auth.user_role != 'Admin') {

            dataInfo['page'] = dataInfo['page'] + ' AND assign_to =' + cms_auth.user_id;
        }
        //  console.log(cms_auth);
    } else if (page == 'Interested' || general_filter == 'Interested') {
        var cms_auth = checkSession('cms_auth');
        cms_auth = JSON.parse(cms_auth);
        if (filter_by_lead_source != '-1') {
            dataInfo['page'] = ' stage_id =4 and lead_source like "%' + filter_by_lead_source + '%" ';
        } else {
            dataInfo['page'] = ' stage_id =4 ';
        }
        if (cms_auth.user_role != 'Admin') {

            dataInfo['page'] = dataInfo['page'] + ' AND assign_to =' + cms_auth.user_id;
        }
        //  console.log(cms_auth);
    } else if (page == 'Not_Interested' || general_filter == 'Not_Interested') {
        var cms_auth = checkSession('cms_auth');
        cms_auth = JSON.parse(cms_auth);
        if (filter_by_lead_source != '-1') {
            dataInfo['page'] = ' stage_id =5 and lead_source like "%' + filter_by_lead_source + '%" ';
        } else {
            dataInfo['page'] = ' stage_id =5 ';
        }
        if (cms_auth.user_role != 'Admin') {

            dataInfo['page'] = dataInfo['page'] + ' AND assign_to =' + cms_auth.user_id;
        }
        //  console.log(cms_auth);
    } else if (page == 'Verbally_confirmed' || general_filter == 'Verbally_confirmed') {
        var cms_auth = checkSession('cms_auth');
        cms_auth = JSON.parse(cms_auth);
        if (filter_by_lead_source != '-1') {
            dataInfo['page'] = ' stage_id =6 and lead_source like "%' + filter_by_lead_source + '%" ';
        } else {
            dataInfo['page'] = ' stage_id =6 ';
        }
        if (cms_auth.user_role != 'Admin') {

            dataInfo['page'] = dataInfo['page'] + ' AND assign_to =' + cms_auth.user_id;
        }
        //  console.log(cms_auth);
    } else if (page == 'Sales_Done' || general_filter == 'Sales_Done') {
        var cms_auth = checkSession('cms_auth');
        cms_auth = JSON.parse(cms_auth);
        if (filter_by_lead_source != '-1') {
            dataInfo['page'] = ' stage_id =7 and lead_source like "%' + filter_by_lead_source + '%" ';
        } else {
            dataInfo['page'] = ' stage_id =7 ';
        }
        if (cms_auth.user_role != 'Admin') {

            dataInfo['page'] = dataInfo['page'] + ' AND assign_to =' + cms_auth.user_id;
        }
        //  console.log(cms_auth);
    } else if (page == 'Delivered' || general_filter == 'Delivered') {
        var cms_auth = checkSession('cms_auth');
        cms_auth = JSON.parse(cms_auth);
        if (filter_by_lead_source != '-1') {
            dataInfo['page'] = ' stage_id =8 and lead_source like "%' + filter_by_lead_source + '%" ';
        } else {
            dataInfo['page'] = ' stage_id =8 ';
        }
        if (cms_auth.user_role != 'Admin') {

            dataInfo['page'] = dataInfo['page'] + ' AND assign_to =' + cms_auth.user_id;
        }
        //  console.log(cms_auth);
    } else {

        if (general_filter == 'block' || general_filter == 'closed' || general_filter == 'customer' || general_filter == 'prospect' || general_filter == 'lead') {
            dataInfo['page'] = ' customer_type="' + general_filter + '" ' + ' and lead_source like "%' + page + '%" ';

        } else if (general_filter == 'assign_lead') {
            var cms_auth = checkSession('cms_auth');
            cms_auth = JSON.parse(cms_auth);
            dataInfo['page'] = ' ( customer_type="lead" AND assign_to =' + cms_auth.user_id + ' ) and lead_source like "%' + page + '%" ';
        } else if (general_filter == 'unasigned_lead') {
            dataInfo['page'] = ' ( customer_type="lead" AND assign_to <=0 ) ' + ' and lead_source like "%' + filter_by_lead_source + '%" ';
        } else if (general_filter == 'unasigned_block') {
            dataInfo['page'] = ' ( customer_type="block" AND assign_to <=0 ) ' + ' and lead_source like "like' + filter_by_lead_source + '%" ';

        } else {
            dataInfo['page'] = ' lead_source like "%' + page + '%" ';
        }

    }
    var table_id = "contact-list-table";
    table_data_contacts(dataInfo, table_id);
    //  $("div.DTTT_container").css("display", "none");
    //    SELECT * FROM contacts  WHERE ( customer_type="lead") AND ((first_name LIKE 'b%' OR first_name LIKE 'B%'
    //) OR ((first_name = '' OR first_name = NULL) AND last_name LIKE 'b%' OR last_name LIKE 'B%')) ORDER BY
    // id ASC LIMIT 0, 15 
    /*   $('#previous_filter').val(previous_filter_set);
     $('#lead_source_filter').val(lead_source_filter);
     console.log(lead_source_filter + '|' + page + $('#previous_filter').val() + '|' + $('#lead_source_filter').val());*/

    dropdown_chosen_style();
}

function table_data_contacts(dataInfo, table_id) {
    var table_name = "#" + table_id;

    var general_filter = $('#previous_filter').val();
    var filter_by_lead_source = $('#lead_source_filter').val();

    $(table_name).dataTable({
        //   "data": dataSet,

        "processing": true,
        "serverSide": true,
        "ajax": {
            url: cms_url['show_all_contacts'],
            type: 'POST',
            data: {
                'info': dataInfo
            }
        },
        scrollX: true,
        "columns": [{
            "data": "part1",
            "class": "center"
        }, {
            "data": "part2",
            "class": "center"
        }, {
            "data": "part3",
            "class": "center"
        }, {
                "data": "part4",
                "class": "center"
        }],
        "bFilter": true,
        "bLengthChange": false,
        "order": [
            [0, "asc"]
        ],
        "lengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        'iDisplayLength': 15,
        dom: 'lTf<"toolbar">t<"bottom"rip>',
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [{
                "sExtends": "select",
                "sButtonText": "Excel",
                "fnClick": function(nButton, oConfig, oFlash) {
                    window.open(cms_url['export_contacts_excel'] + "?general_filter="+general_filter+"&filter_by_lead_source="+filter_by_lead_source, "_blank");

                }
            },
                "csv"
            ],
            "filter": "applied"
        }
    });
}

function get_lead() {
    var i, str = '<option value="-1"> --Select Lead Source--</option>';
    var dataInfo = {};
    var dataSet;
    var ul;
    try {

        dataSet = localStorage.get_lead_source_name;
        if (dataSet.length > 21) {
            // $(targetID).append(innerHTMLCode);
            ul = JSON.parse(dataSet);
        } else {
            dataSet = connectServer(cms_url['get_lead_source_name'], dataInfo);
            ul = JSON.parse(dataSet);
        }


    } catch (ex) {
        dataSet = connectServer(cms_url['get_lead_source_name'], dataInfo);
        ul = JSON.parse(dataSet);
    }

    //   <li class="disabled"><a href="#">Display By:</a></li>
    //                                <li class="display display-FirstLast selected"><a href="#" data-setting="display" data-value="FirstLast">First Name</a></li>
    for (i = 0; i < ul.length; i++) {
        if (ul[i] == "")
            str = str + '<option onclick="show_contacts_cat(\'' + ul[i] + '\')" value="' + ul[i] + '">Undefined</option>';
        else
            str = str + '<option onclick="show_contacts_cat(\'' + ul[i] + '\')" value="' + ul[i] + '">' + ul[i] + ' </option>';
    }
    $("#filter_by_lead_source").html(str);
    $("#filter_by_lead_source").prop("onclick", null);
    $('.chosen-select').trigger('liszt:updated');
    try {
        ul = JSON.stringify(ul);
        localStorage.get_lead_source_name = ul;
    } catch (ex) {

    }
}


function show_detail_lead(id) {
    star_call_duration();
    showUserMenu('user_detail');
    customDropDownOption('#lead_source', cms_url['select_lead_source']);
    customDropDownOption('#do_area,#collect_person_do', cms_url['doze_crm_area_list']);
    customDropDownOption('#package', cms_url['doze_crm_package_list']);
    customDropDownOption('#stage', cms_url['select_stage']);
    customDropDownOption('#note_id', cms_url['select_note']);
    customDropDownOption('#assign_agent', cms_url['doze_crm_sales_representative_list']);

    customDropDownOption('#connection_type', cms_url['select_connection_type']);
    customDropDownOption('#corporate_stage', cms_url['select_corporate_stage']);
    customDropDownOption('#industry_seg', cms_url['select_industry_seg']);
    customDropDownOption('#other_service_charge', cms_url['select_other_service_charge']);
    customDropDownOption('#packaging', cms_url['select_packaging']);
    fetchDropDownOptionHtml('#zone', cms_url['select_zone']);
    var datainfo = {};
    datainfo['action_id'] = id;
    var data = connectServer(cms_url['get_contact_info'], datainfo);
    data = JSON.parse(data);
    //  console.log(data);
    var auth_session_data = checkSession('cms_auth');
    var auth_data = JSON.parse(auth_session_data);
    if ((auth_data.user_role).toLowerCase() != 'admin') {
        $('#tab-5').hide();
        $('#tab-6').hide();
    }

    if ((auth_data.user_role).toLowerCase() == 'asde') {
        $("#tab-61").show();
        $("#action_menu_contact_detail").hide();
        $(".notForASDE").hide();
        $("#tab-51").hide();
        $("#tab-52").hide();
        $("#tab-2").hide();
        $("#tab-3").hide();
        $("#tab-4").hide();
        $("#tab-5").hide();
        $("#tab-6").hide();
    }

    if (data.customer_type == 'lead') {
        show_lead_detail(data);
        $('#make_prospect_btn').show();
        $('#make_closed_btn').show();
    } else if (data.customer_type == 'prospect') {
        show_prospect_detail(data);
        $('#make_closed_btn').show();
    } else if (data.customer_type == 'closed') {
        show_closed_detail(data, id);
    } else if (data.customer_type == 'block') {
        show_blocked_detail(data, id);
    } else if (data.customer_type == 'customer') {
        show_customer_detail(data, id);
    }

    try {
        if (data.stage_id >= 2) {
            data.stage_id = data.stage_id;
        } else {
            data.stage_id = 1;
        }
        if (data.note_id >= 1) {
            data.note_id = data.note_id;
        } else {
            data.note_id = 0;
        }
        $("#stage option[value='" + data.stage_id + "']").attr('selected', true);
        $("#note option[value='" + data.note_id + "']").attr('selected', true);
        $("#assign_agent option[value='" + data.assign_to + "']").attr('selected', true);
        $("#zone option[value='" + data.zone + "']").attr('selected', true);
        $("#collect_person_do option[value='" + data.collection_person_do + "']").attr('selected', true);
        $("#collection_person").val(data.collection_person);
        $("#collection_person_phone").val(data.collection_person_phone);
        $('#date_of_birth').val(data.date_of_birth);
        $('#upload_id').val(data.upload_id);
        $('.chosen-select').trigger('liszt:updated');
        // alert(data.upload_id+''+data.date_of_birth+'stage'+data.stage_id+'');

        $('.chosen-select').trigger('liszt:updated');
        $('.chosen-select').trigger("chosen:updated");
    } catch (ex) {
        console.log(ex);
    }

    $('#back_button').attr("onClick", "showUserMenu('contacts')");
    $('#date_of_birth').datepicker({
        format: "yyyy-mm-dd",
        weekStart: 2 - 1,
        todayBtn: true,
        calendarWeeks: false,
        autoclose: true,
        todayHighlight: true,
        todayHighlight: 1,
    });
    var date2323 = new Date();
    $('#next_call_date').datepicker({
        format: "yyyy-mm-dd",
        weekStart: 2 - 1,
        todayBtn: true,
        calendarWeeks: false,
        autoclose: true,
        todayHighlight: true,
        todayHighlight: 1
    });

    $('#raise_date').datepicker({
        format: "yyyy-mm-dd",
        weekStart: 2 - 1,
        todayBtn: true,
        calendarWeeks: false,
        autoclose: true,
        todayHighlight: true,
        todayHighlight: 1
    });

    $('#due_date').datepicker({
        format: "yyyy-mm-dd",
        weekStart: 2 - 1,
        todayBtn: true,
        calendarWeeks: false,
        autoclose: true,
        todayHighlight: true,
        todayHighlight: 1
    });

    $('#collection_date').datepicker({
        format: "yyyy-mm-dd",
        weekStart: 2 - 1,
        todayBtn: true,
        calendarWeeks: false,
        autoclose: true,
        todayHighlight: true,
        todayHighlight: 1
    });

    $('#conversion_collection_date').datepicker({
        format: "yyyy-mm-dd",
        weekStart: 2 - 1,
        todayBtn: true,
        calendarWeeks: false,
        autoclose: true,
        todayHighlight: true,
        todayHighlight: 1
    });
    $('#assignment_date').datepicker({
        format: "yyyy-mm-dd",
        weekStart: 2 - 1,
        todayBtn: true,
        calendarWeeks: false,
        autoclose: true,
        todayHighlight: true,
        todayHighlight: 1
    });

    $('#raise_date').val(return_current_date());
    //for_corporate

    var auth_session_data = checkSession('cms_auth');
    if (auth_session_data != null) {
        var auth_data = JSON.parse(auth_session_data);
        if ((auth_data.user_role).toLowerCase() == 'corporate' || (auth_data.user_role).toLowerCase() == 'admin') {
            // only for corporate show.

        } else {
            $('.for_corporate').css("display", "none");
        }
        if ((auth_data.user_role).toLowerCase() == 'channel' || (auth_data.user_role).toLowerCase() == 'admin') {
            // only for channel show.

        } else {
            $('.for_chanel').css("display", "none");
        }
    }

    $(".collect_parson").hide();
    localStorage.previous_stage = $('#stage').val();
}


function table_init_customer_callhistory(div_id, table_id) {
    var div_name = "#" + div_id;
    $(div_name).html('<table class="table table-striped table-bordered table-hover responsive" id="' + table_id + '" width="100%">' +
        '<thead><tr><th>Call Date</th><th>Agent Name</th><th>Feedback</th><th>Stage</th></tr></thead>' +
        '<tr><td  align="center"><img src="doze_crm/img/31.GIF"></td></tr></table>');

    // $(".nowrap2").css("width", "25%");
    // $(".nowrap3").css("width", "15%");
}

function report_customer_callhistory(table_id, id) {

    var dataInfo = {};
    dataInfo['id'] = id;

    table_data_customer_callhistory(dataInfo, table_id);

}

function table_data_customer_callhistory(dataSet, table_id) {
    var table_name = "#" + table_id;
    $(table_name).dataTable({
        //   "data": dataSet,

        "processing": true,
        "serverSide": true,
        "ajax": {
            url: cms_url['customer_block_callhistory'],
            type: 'POST',
            data: {
                'info': dataSet
            }
        },
        "columns": [{
            "data": "call_date",
            "class": "center"
        }, {
            "data": "call_agent",
            "class": "center"
        }, {
            "data": "feedback",
            "class": "center"
        }, {
            "data": "stage",
            "class": "center"
        }, ],
        "bFilter": false,
        "bLengthChange": false,
        "order": [
            [0, "asc"]
        ],
        'iDisplayLength': 15,
        dom: 'T<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
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


function show_lead_detail(data) {


    if (data) {
        $('#action').val('update');
        $('#action_id').val(data.id);
        $('#contact_id').val(data.id);
        $('#next_call_date').val(data.next_call_date);
        $('#first_name').val(data.first_name);
        $('#last_name').val(data.last_name);
        $('#area').val(data.area);
        $("#lead_source option[value='" + data.lead_source + "']").attr('selected', true);
        $("#assign_agent option[value='" + data.assign_to + "']").attr('selected', true);
        $('#address1').val(data.address1);
        $('#address2').val(data.address2);
        $('#phone1').val(data.phone1);
        $('#phone2').val(data.phone2);
        $('#email').val(data.email);
        $('#note').val(data.note);
        $('#contact_id').prop('readonly', true);
        //alert(data.status);
        $("#status option[value='" + data.status + "']").attr('selected', true);
        $("#final_status option[value='" + data.final_status + "']").attr('selected', true);
        $("#do_area").find("option[value='" + data.do_area + "']").attr('selected', true);
        $("#type").find("option[value='" + data.customer_type + "']").attr('selected', true);
    }
    $('#tab-2').hide();
    //$('#tab-3').hide();
    $('#tab-4').hide();
    $('#tab-5').hide();
    $('#tab-6').hide();
    table_init_customer_callhistory('tbl_call_history', 'call_history');
    report_customer_callhistory('call_history', data.id);
    dropdown_chosen_style();

}

function show_prospect_detail(data) {
    if (data) {
        $('#action').val('update');
        $('#action_id').val(data.id);
        $('#contact_id').val(data.id);
        $('#next_call_date').val(data.next_call_date);
        $('#first_name').val(data.first_name);
        $('#last_name').val(data.last_name);
        $('#area').val(data.area);
        $("#lead_source option[value='" + data.lead_source + "']").attr('selected', true);
        $("#assign_agent option[value='" + data.assign_to + "']").attr('selected', true);
        $('#address1').val(data.address1);
        $('#address2').val(data.address2);
        $('#phone1').val(data.phone1);
        $('#phone2').val(data.phone2);
        $('#email').val(data.email);
        $('#note').val(data.note);
        $('#contact_id').prop('readonly', true);
        //alert(data.status);
        $("#status option[value='" + data.status + "']").attr('selected', true);
        $("#final_status option[value='" + data.final_status + "']").attr('selected', true);
        $("#do_area").find("option[value='" + data.do_area + "']").attr('selected', true);
        $("#type").find("option[value='" + data.customer_type + "']").attr('selected', true);
    }
    $('#tab-2').hide();
    $('#tab-4').hide();
    $('#tab-5').hide();
    $('#tab-6').hide();
    table_init_customer_callhistory('tbl_call_history', 'call_history');
    report_customer_callhistory('call_history', data.id);
    dropdown_chosen_style();
}

function show_customer_detail(data, id) {

    if (data) {
        $('#action').val('update');
        $('#action_id').val(data.id);
        $('#contact_id').val(data.id);
        $('#next_call_date').val(data.next_call_date);
        $('#first_name').val(data.first_name);
        $('#last_name').val(data.last_name);
        $('#area').val(data.area);
        $("#lead_source option[value='" + data.lead_source + "']").attr('selected', true);
        $("#assign_agent option[value='" + data.assign_to + "']").attr('selected', true);
        $('#address1').val(data.address1);
        $('#address2').val(data.address2);
        $('#phone1').val(data.phone1);
        $('#phone2').val(data.phone2);
        $('#email').val(data.email);
        $('#note').val(data.note);
        $('#contact_id').prop('readonly', true);
        $('#radius_user').val(data.radius_user);
        //alert(data.status);
        $("#status option[value='" + data.status + "']").attr('selected', true);
        $("#final_status option[value='" + data.final_status + "']").attr('selected', true);
        $("#do_area").find("option[value='" + data.do_area + "']").attr('selected', true);
        $("#type").find("option[value='" + data.customer_type + "']").attr('selected', true);
    }
    table_init_customer_callhistory('tbl_call_history', 'call_history');
    report_customer_callhistory('call_history', data.id);
    dropdown_chosen_style();
    $('#payment_doze_id').val(data.doze_id);
    $('#payment_contact_id').val(data.id);
    //$('#otrs_contact_id').val(data.contact_id);
    $('#payment_doze_id').prop('readonly', true);
    //$('#otrs_contact_id').prop('readonly', true);
    $('#payment_contact_id').prop('readonly', true);


    var datainfo = {};
    datainfo['action_id'] = data.id;
    var response = connectServer(cms_url['get_otrs_payment'], datainfo);
    try {

        response = JSON.parse(response);

        customDropDownOption('#collected_by', cms_url['get_payment_collection_mode']);
        console.log(response);
        $('#otrs_contact_id').val(id);
        $('#otrs_contact_id').prop('readonly', true);
        $('#raise_date').val(response.otrs_rise_date);
        $('#due_date').val(response.connection_due_date);
        $('#ticket_number').val(response.otrs_tic_number);
        $('#ticket_agent').val(response.otrs_tic_agent);
        $("#otrs_status option[value='" + response.otrs_status + "']").attr('selected', true);
        $('#raise_date').prop('readonly', true);
        $('#ticket_number').prop('readonly', true);

        // doze_DateTimePicker('due_date');

        $('#collection_date').val(response.payment_collection_date);
        //$('#collected_by').val(response.payment_collected_by);
        $("#collected_by option[value='" + response.payment_collected_by + "']").attr('selected', true);
        $('#receipt_number').val(response.payment_rec_number);
        $('#collected_amount_readonly').val(response.cash_receive);
        $('#remarks').val(response.remarks);
        $("#nid option[value='" + response.nid + "']").attr('selected', true);
        $("#photo option[value='" + response.photo + "']").attr('selected', true);
        $("#sap option[value='" + response.sap + "']").attr('selected', true);
        $("#payment_mode option[value='" + response.payment_mode + "']").attr('selected', true);
        $("#collection_status option[value='" + response.payment_status + "']").attr('selected', true);


        var datainfo = {};

        datainfo['action_id'] = data.id;
        var response = connectServer(cms_url['get_conversion_history'], datainfo);

        $('#conversion_update_id').val(id);

        if (response != '') {
            response = JSON.parse(response);
            $('#conversion_date').val(return_current_date());
            $('#conversion_date').prop('readonly', true);

            var auth_session_data = checkSession('cms_auth');
            var auth_data = JSON.parse(auth_session_data);

            $('#conversion_agent').val(auth_data.first_name + " " + auth_data.last_name);

            $('#conversion_note').val(response.conversion_note);
            $('#collection_amount').val(response.collection_amount);
            $('#install_cost').val(response.install_cost);
            $('#monthly_cost').val(response.monthly_cost);
            $('#month_number').val(response.month_number);
            $('#collection_note').val(response.collection_note);
            $('#assignment_date').val(response.assignment_date);
            $('#conversion_collection_date').val(response.collection_date);
            $('#conversion_collection_time').val(response.collection_time);
            $('#real_ip_charge').val(response.real_ip_cost);
            $('#additional_charge').val(response.additional_cost);

            var m_cost = $("#monthly_cost").val();
            var i_cost = $("#install_cost").val();
            var m_no = $("#month_number").val();
            var real_ip_cost = $("#real_ip_charge").val();
            var additional_charge = $("#additional_charge").val();

            m_no = (!m_no || m_no == null || m_no == '') ? 0 : m_no;
            i_cost = parseFloat(i_cost);
            m_no = parseInt(m_no);
            m_cost = parseFloat(m_cost);
            real_ip_cost = parseFloat(real_ip_cost);
            additional_charge = parseFloat(additional_charge);

            var total = (i_cost ? i_cost:0) + ( m_no && m_no >0 ? m_no:0 )*( m_cost ? m_cost:0 ) + ( real_ip_cost ? real_ip_cost:0 ) + ( additional_charge ? additional_charge:0);
            $("#total_cost").val(total);
            $('#total_cost').prop('readonly', true);


            $("#conversion_status option[value='" + response.conversion_status + "']").attr('selected', true);
            $("#client_type option[value='" + response.client_type + "']").attr('selected', true);
            $("#package option[value='" + response.package + "']").attr('selected', true);
            $("#payment_mode_conversion option[value='" + response.paymode + "']").attr('selected', true);
            $("#payment_type option[value='" + response.paytype + "']").attr('selected', true);
        }
        table_initialize_customer_payment();
        report_customer_payment(data.doze_id);
    } catch (ex) {
        console.log(ex);
    }
}

function show_closed_detail(data, id) {
    if (data) {
        $('#action').val('update');
        $('#action_id').val(data.id);
        $('#contact_id').val(data.id);
        $('#next_call_date').val(data.next_call_date);
        $('#first_name').val(data.first_name);
        $('#last_name').val(data.last_name);
        $('#area').val(data.area);
        $("#lead_source option[value='" + data.lead_source + "']").attr('selected', true);
        $("#assign_agent option[value='" + data.assign_to + "']").attr('selected', true);
        $('#address1').val(data.address1);
        $('#address2').val(data.address2);
        $('#phone1').val(data.phone1);
        $('#phone2').val(data.phone2);
        $('#email').val(data.email);
        $('#note').val(data.note);
        $('#contact_id').prop('readonly', true);
        $('#radius_user').val(data.radius_user);
        //alert(data.status);
        $("#status option[value='" + data.status + "']").attr('selected', true);
        $("#final_status option[value='" + data.final_status + "']").attr('selected', true);
        $("#do_area").find("option[value='" + data.do_area + "']").attr('selected', true);
        $("#type").find("option[value='" + data.customer_type + "']").attr('selected', true);
    }
    table_init_customer_callhistory('tbl_call_history', 'call_history');
    report_customer_callhistory('call_history', data.id);
    dropdown_chosen_style();
    $('#payment_doze_id').val(data.doze_id);
    $('#payment_contact_id').val(data.id);
    //$('#otrs_contact_id').val(data.contact_id);
    $('#payment_doze_id').prop('readonly', true);
    //$('#otrs_contact_id').prop('readonly', true);
    $('#payment_contact_id').prop('readonly', true);


    var datainfo = {};
    datainfo['action_id'] = data.id;
    var response = connectServer(cms_url['get_otrs_payment'], datainfo);
    response = JSON.parse(response);

    customDropDownOption('#collected_by', cms_url['get_payment_collection_mode']);

    if (response) {
        $('#otrs_contact_id').prop('readonly', true);
        $('#raise_date').val(response.otrs_rise_date);
        $('#due_date').val(response.connection_due_date);
        $('#ticket_number').val(response.otrs_tic_number);
        $('#ticket_agent').val(response.otrs_tic_agent);
        $("#otrs_status option[value='" + response.otrs_status + "']").attr('selected', true);
        $('#raise_date').prop('readonly', true);
        $('#ticket_number').prop('readonly', true);

        doze_DateTimePicker('due_date');
        //doze_DateTimePicker('raise_date');

        //$('#due_date').datetimepicker({});
        //$('#raise_date').datetimepicker({});


        $('#collection_date').val(response.payment_collection_date);
        //$('#collected_by').val(response.payment_collected_by);
        $("#collected_by option[value='" + response.payment_collected_by + "']").attr('selected', true);
        $('#receipt_number').val(response.payment_rec_number);
        $("#payment_mode option[value='" + response.payment_mode + "']").attr('selected', true);
        $("#collection_status option[value='" + response.payment_status + "']").attr('selected', true);
    }

    var datainfo = {};

    datainfo['action_id'] = data.id;
    var response = connectServer(cms_url['get_conversion_history'], datainfo);

    $('#conversion_update_id').val(id);

    if (response != '') {
        response = JSON.parse(response);
        $('#conversion_date').val(return_current_date());
        $('#conversion_date').prop('readonly', true);

        var auth_session_data = checkSession('cms_auth');
        var auth_data = JSON.parse(auth_session_data);

        $('#conversion_agent').val(auth_data.first_name + " " + auth_data.last_name);

        $('#conversion_note').val(response.conversion_note);
        $('#collection_amount').val(response.collection_amount);
        $('#install_cost').val(response.install_cost);
        $('#monthly_cost').val(response.monthly_cost);
        $('#month_number').val(response.month_number);
        $('#collection_note').val(response.collection_note);
        $('#assignment_date').val(response.assignment_date);
        $('#conversion_collection_date').val(response.collection_date);
        $('#conversion_collection_time').val(response.collection_time);

        var m_cost = $("#monthly_cost").val();
        var i_cost = $("#install_cost").val();
        var m_no = $("#month_number").val();
        m_no = (!m_no || m_no == null || m_no == '') ? 0 : m_no;

        var total = parseFloat(i_cost) + (parseFloat(m_cost) * parseInt(m_no));
        $("#total_cost").val(total);
        $('#total_cost').prop('readonly', true);


        $("#conversion_status option[value='" + response.conversion_status + "']").attr('selected', true);
        $("#client_type option[value='" + response.client_type + "']").attr('selected', true);
        $("#package option[value='" + response.package + "']").attr('selected', true);

        if ((auth_data.user_role).toLowerCase() == 'admin') {
            $('#make_customer_btn').show();
        }
    }
    $('#tab-2').hide();
    $('#tab-6').hide();
}

function show_blocked_detail(data, id) {
    if (data) {
        $('#action').val('update');
        $('#action_id').val(data.id);
        $('#contact_id').val(data.id);
        $('#next_call_date').val(data.next_call_date);
        $('#first_name').val(data.first_name);
        $('#last_name').val(data.last_name);
        $('#area').val(data.area);
        $("#lead_source option[value='" + data.lead_source + "']").attr('selected', true);
        $("#assign_agent option[value='" + data.assign_to + "']").attr('selected', true);
        $('#address1').val(data.address1);
        $('#address2').val(data.address2);
        $('#phone1').val(data.phone1);
        $('#phone2').val(data.phone2);
        $('#email').val(data.email);
        $('#note').val(data.note);
        $('#contact_id').prop('readonly', true);
        $('#radius_user').val(data.radius_user);
        //alert(data.status);
        $("#status option[value='" + data.status + "']").attr('selected', true);
        $("#final_status option[value='" + data.final_status + "']").attr('selected', true);
        $("#do_area").find("option[value='" + data.do_area + "']").attr('selected', true);
        $("#type").find("option[value='" + data.customer_type + "']").attr('selected', true);
    }
    table_init_customer_callhistory('tbl_call_history', 'call_history');
    report_customer_callhistory('call_history', data.id);
    dropdown_chosen_style();
    $('#payment_doze_id').val(data.doze_id);
    $('#payment_contact_id').val(data.id);
    //$('#otrs_contact_id').val(data.contact_id);
    $('#payment_doze_id').prop('readonly', true);
    //$('#otrs_contact_id').prop('readonly', true);
    $('#payment_contact_id').prop('readonly', true);


    var datainfo = {};
    datainfo['action_id'] = data.id;
    //var response = connectServer(cms_url['get_otrs_payment'], datainfo);
    //response = JSON.parse(response);

    // customDropDownOption('#collected_by', cms_url['get_payment_collection_mode']);

    /*if (response) {
     $('#otrs_contact_id').prop('readonly', true);
     $('#raise_date').val(response.otrs_rise_date);
     $('#due_date').val(response.connection_due_date);
     $('#ticket_number').val(response.otrs_tic_number);
     $('#ticket_agent').val(response.otrs_tic_agent);
     $("#otrs_status option[value='" + response.otrs_status + "']").attr('selected', true);
     $('#raise_date').prop('readonly', true);
     $('#ticket_number').prop('readonly', true);

     doze_DateTimePicker('due_date');
     //doze_DateTimePicker('raise_date');

     //$('#due_date').datetimepicker({});
     //$('#raise_date').datetimepicker({});


     $('#collection_date').val(response.payment_collection_date);
     //$('#collected_by').val(response.payment_collected_by);
     $("#collected_by option[value='" + response.payment_collected_by + "']").attr('selected', true);
     $('#receipt_number').val(response.payment_rec_number);
     $("#payment_mode option[value='" + response.payment_mode + "']").attr('selected', true);
     $("#collection_status option[value='" + response.payment_status + "']").attr('selected', true);
     }*/

    var datainfo = {};

    datainfo['action_id'] = data.id;
    var response = connectServer(cms_url['get_conversion_history'], datainfo);

    $('#conversion_update_id').val(id);

    if (response != '') {
        response = JSON.parse(response);
        $('#conversion_date').val(return_current_date());
        $('#conversion_date').prop('readonly', true);

        var auth_session_data = checkSession('cms_auth');
        var auth_data = JSON.parse(auth_session_data);

        $('#conversion_agent').val(auth_data.first_name + " " + auth_data.last_name);

        $('#conversion_note').val(response.conversion_note);
        $('#collection_amount').val(response.collection_amount);
        $('#install_cost').val(response.install_cost);
        $('#monthly_cost').val(response.monthly_cost);
        $('#month_number').val(response.month_number);


        $('#collection_note').val(response.collection_note);
        $('#assignment_date').val(response.assignment_date);
        $('#conversion_collection_date').val(response.collection_date);
        $('#conversion_collection_time').val(response.collection_time);

        var m_cost = $("#monthly_cost").val();
        var i_cost = $("#install_cost").val();
        var m_no = $("#month_number").val();
        m_no = (!m_no || m_no == null || m_no == '') ? 0 : m_no;

        var total = parseFloat(i_cost) + (parseFloat(m_cost) * parseInt(m_no));
        $("#total_cost").val(total);
        $('#total_cost').prop('readonly', true);


        $("#conversion_status option[value='" + response.conversion_status + "']").attr('selected', true);
        $("#client_type option[value='" + response.client_type + "']").attr('selected', true);
        $("#package option[value='" + response.package + "']").attr('selected', true);
    }
    table_initialize_customer_payment();
    report_customer_payment(data.doze_id);
    $('#tab-5').hide();
    $('#tab-6').hide();
}


function table_initialize_customer_payment() {

    $('#tbl_payment').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="blocked_payment" width="100%"  ><tr><td  align="center"><img src="doze_crm/img/31.gif"></td></tr></table>');

}

function report_customer_payment(doze_id) {

    var dataSet = [
        []
    ];
    var dataInfo = {};
    dataInfo['doze_id'] = doze_id;

    dataSet = connectServer(cms_url['doze_crm_get_customer_payment'], dataInfo);
    //  alert(dataSet);
    dataSet = JSON.parse(dataSet);
    //alert(dataSet);
    table_data_customer_payment(dataSet);
    $("div.DTTT_container").css("display", "none");

}


function table_data_customer_payment(dataSet) {
    $('#blocked_payment').dataTable({
        "data": dataSet,
        "columns": [{
            "title": "TransactionDate",
            "class": "center"
        }, {
            "title": "Amount(TK)",
            "class": "center"
        }, {
            "title": "Reference",
            "class": "center"
        }, {
            "title": "Transaction Id",
            "class": "center"
        }, ],
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
                "select_all", "select_none", "copy", "csv", {
                    "sExtends": "xls",
                    "sFileName": "*.xls"
                }
            ],
            "filter": "applied"
        }
    });
}

function edit_customer_controller(obj) {

    var back_fnc = ($('#back_button').attr("onClick"));

    var auth_session_data = checkSession('cms_auth');
    var auth_data = JSON.parse(auth_session_data);
    var bool;
    var form_id = 'customer_contact_edit';
    var datainfo = {};
    datainfo['action_id'] = $('#contact_id').val();

    var data = connectServer(cms_url['get_contact_info'], datainfo);
    data = JSON.parse(data);
    var next_call_date = $('#next_call_date').val();
    $('#date_time').val(return_local_time());

    var email = $("#email").val();
    var feedback = $("#feedback").val();
    var stage = $("#stage").val();
    stage = parseInt(stage.trim());
    feedback = feedback.trim();
    var lead_source = $('#lead_source').val();
    lead_source = lead_source.trim();

    if (!feedback || feedback == "undefined" || feedback === "" || stage === 1 || lead_source == '-1') {
        alertMessage(this, 'red', 'Data Missing', 'Please fill up the required fields to save your input data');
        return;
    }

    if($("#conversion_collection_date").length != 0 && $("#conversion_collection_date").is(":visible")){
        var temp_collection_date = $("#conversion_collection_date").val();
        if (!temp_collection_date || temp_collection_date == "undefined" || temp_collection_date.trim() == ""){
            alertMessage(this, 'red', 'Data Missing', 'Please fill up the required fields to save your input data');
            return;
        }
    }
    if($("#due_date").length != 0 && $("#due_date").is(":visible")){
        var temp_due_date = $("#due_date").val();
        if (!temp_due_date || temp_due_date == "undefined" || temp_due_date.trim() == ""){
            alertMessage(this, 'red', 'Data Missing', 'Please fill up the required fields to save your input data');
            return;
        }
    }


    var response = connectServerWithForm(cms_url['doze_crm_prospective_customer_save'], form_id);
    if (response == 0 || response.trim() === '0') {

        save_feedback_customer();

        if (data.customer_type == 'closed' || data.customer_type == 'block' || data.customer_type == 'customer') {
            bool = conversion_history_controller();
        }
        if ((auth_data.user_role).toLowerCase() == 'admin' && (data.customer_type == 'closed' || data.customer_type == 'customer')) {

            payment_collection_edit_controller();
            if (data.customer_type != 'closed')
                otrs_edit_controller();
        }

        alertMessage(this, 'green', '', 'Successfully Submitted.');
        /*
         if (bool == true) {
         displayContent("7", "#cmsData", "#contentListLayout", "ContentID");
         show_contacts('all');
         }
         else if (data.customer_type == 'lead') {
         displayContent("7", "#cmsData", "#contentListLayout", "ContentID");
         show_contacts('all');
         }
         */
        //show_detail_customer($('#contact_id').val());
        //showUserMenu('customer_search');
    } else {
        alertMessage(this, 'red', '', 'Failed.');
    }

    $('#back_button').attr("onClick", back_fnc);

}

function save_additional_field_corporate() {
    var datainfo = {};

    datainfo['industry_seg'] = $('#industry_seg').val();
    datainfo['corporate_stage'] = $('#corporate_stage').val();
    datainfo['packaging'] = $('#packaging').val();
    datainfo['connection_type'] = $('#connection_type').val();
    datainfo['other_service_charge'] = $('#other_service_charge').val();
    datainfo['distributor_name'] = $('#distributor_name').val();

    datainfo['distributor_address'] = $('#distributor_address').val();
    datainfo['distributor_contact_no'] = $('#distributor_contact_no').val();
    datainfo['retailer_name'] = $('#retailer_name').val();
    datainfo['retailer_address'] = $('#retailer_address').val();
    datainfo['retailer_contact_no'] = $('#retailer_contact_no').val();


    datainfo['action_id'] = $('#action_id').val();

    datainfo['time'] = return_local_time(return_local_time());

    var response = connectServer(cms_url['save_additional_field'], datainfo);
    if (response == 0 || response.trim() === '0') {
        //alertMessage(this, 'green', '', 'Successfully Submitted.');
        //showUserMenu('customer_search');
        // response = connectServerWithForm(cms_url['save_change_stage_history'], 'customer_contact_edit');

        return 0;
    } else {
        return response;
        // alertMessage(this, 'red', '', 'Failed.');
    }

}


function save_feedback_customer() {
    var datainfo = {};
    var feedback = $('#feedback').val();
    if( feedback == "Others" ){
        feedback = $("#other_feedback").val();
    }
    var notifyme = $('#notifyme:checked').val();
    datainfo['action_id'] = $('#action_id').val();
    datainfo['feedback'] = feedback;
    datainfo['note_id'] = $('#note_id').val();
    datainfo['stage_id'] = $('#stage').val();
    datainfo['star_call_duration'] = localStorage.star_call_duration;
    datainfo['previous_stage_id'] = localStorage.previous_stage;
    var obj_date = new Date();
    var milliseconds = obj_date.getTime();
    /* Return the number of milliseconds since 1970/01/01:*/
    var seconds = milliseconds / 1000;
    datainfo['end_call_duration'] = seconds;
    localStorage.star_call_duration = seconds
    if (notifyme == 'on') {
        datainfo['notifyme'] = 'yes';
    } else {
        datainfo['notifyme'] = 'no';
    }
    datainfo['time'] = return_local_time(return_local_time());
    if (feedback.trim() != '') {
        var response = connectServer(cms_url['save_customer_feedback'], datainfo);
        if (response == 0 || response.trim() === '0') {
            //alertMessage(this, 'green', '', 'Successfully Submitted.');
            //showUserMenu('customer_search');
            response = connectServerWithForm(cms_url['save_change_stage_history'], 'customer_contact_edit');
            save_additional_field_corporate();

            return 0;
        } else {
            return response;
            // alertMessage(this, 'red', '', 'Failed.');
        }
    }
}

function conversion_history_controller(obj) {

    //var a = $('#conversion_collection_date').val();
    //a = a.replace(" ", "");
    //if (a == '' || a == null) {
    //    alertMessage(this, 'red', '', 'Please give to be collection Date !!');
    //    return false;
    //}
    var package = $("select#package").val();
    var find_length = $("form#customer_conversion_history").find('input[ name="package"]').length;
    if( typeof find_length == 'undefined' || find_length == 0 || find_length == null ){
        $("form#customer_conversion_history").append('<input type="hidden" name="package" id="package" value="'+package+'" />');
    }else{
        $("input#package").val(package);
    }

    var form_id = 'customer_conversion_history';
    var response = connectServerWithForm(cms_url['doze_crm_conversion_history_save'], form_id);
    if (response == 0 || response.trim() === '0') {} else {}
    return true;
}

function payment_collection_edit_controller(obj) {
    var form_id = 'payment_collection_edit';
    var response = connectServerWithForm(cms_url['doze_crm_payment_collection_save'], form_id);
    if (response === 0 || response.trim() === '0') {
        //alertMessage(this, 'green', '', 'Successfully Submitted.');
        //showUserMenu('closed_customer');
    } else {
        //alertMessage(this, 'red', '', 'Failed.');
    }
}

function otrs_edit_controller(obj) {
    var form_id = 'otrs_edit';
    var response = connectServerWithForm(cms_url['doze_crm_otrs_ticket_save'], form_id);
    if (response == 0 || response.trim() === '0') {
        //alertMessage(this, 'green', '', 'Successfully Submitted.');
        //showUserMenu('closed_customer');
    } else {
        //alertMessage(this, 'red', '', 'Failed.');
    }
}

function cancel_menu() {
    displayContent("7", "#cmsData", "#contentListLayout", "ContentID");
    show_contacts('all');
}

function total_cost_on_input() {
    var i_cost = $("#install_cost").val();
    var m_cost = $("#monthly_cost").val();
    var m_no = $("#month_number").val();
    var real_ip_charge = $("#real_ip_charge").val();
    var additional_charge = $("#additional_charge").val();
    m_no = (!m_no || m_no == null || m_no == '') ? 0 : m_no;

    real_ip_charge = (!real_ip_charge || real_ip_charge == null || real_ip_charge == '') ? 0 : real_ip_charge;
    additional_charge = (!additional_charge || additional_charge == null || additional_charge == '') ? 0 : additional_charge;
    real_ip_charge = parseFloat(real_ip_charge);
    real_ip_charge = real_ip_charge < 0 ? 0 : real_ip_charge;
    additional_charge = parseFloat(additional_charge);
    additional_charge = additional_charge < 0 ? 0 : additional_charge;

    i_cost = (!i_cost || i_cost == null || i_cost == '') ? 0 : i_cost;
    i_cost = parseFloat(i_cost);
    i_cost = i_cost < 0 ? 0 : i_cost;

    m_cost = (!m_cost || m_cost == null || m_cost == '') ? 0 : m_cost;
    m_cost = parseFloat(m_cost);
    m_cost = m_cost < 0 ? 0 : m_cost;

    var total = real_ip_charge + additional_charge + i_cost + (m_cost * parseInt(m_no));
    total = total.toFixed(4);
    $('#total_cost').attr('readonly', false);
    //  $("#total_cost").removeAttr("readonly");
    $("#total_cost").val(total);
    $('#total_cost').attr('readonly', true);
}

function make_prospect() {
    var back_fnc = ($('#back_button').attr("onClick"));
    var id = $('#contact_id').val();
    update_database_row("contacts", "customer_type", "prospect", "id", id);
    edit_customer_controller();
    show_detail_lead(id);
    $('#back_button').attr("onClick", back_fnc);
}

function make_block() {
    var back_fnc = ($('#back_button').attr("onClick"));
    var id = $('#contact_id').val();
    update_database_row("contacts", "customer_type", "block", "id", id);
    edit_customer_controller();
    show_detail_lead(id);
    $('#back_button').attr("onClick", back_fnc);
}

function make_unblock(){
    var back_fnc = ($('#back_button').attr("onClick"));
    var id = $('#contact_id').val();
    update_database_row("contacts", "customer_type", "prospect", "id", id);
    edit_customer_controller();
    show_detail_lead(id);
    $('#back_button').attr("onClick", back_fnc);
}

function make_closed() {
    var back_fnc = ($('#back_button').attr("onClick"));
    var id = $('#contact_id').val();
    var email = $('#email').val();
    if (email.trim() == '') {
        alertMessage(this, 'red', '', 'Please Give Email Address.');
    } else {
        var status_code = create_radius_account();
        if( status_code == 1 ){
          return;
        }
        edit_customer_controller();
        update_database_row("contacts", "customer_type", "closed", "id", id);
        show_detail_lead(id);
    }
    $('#back_button').attr("onClick", back_fnc);
}

function make_customer() {
    var back_fnc = ($('#back_button').attr("onClick"));
    var id = $('#contact_id').val();
    var collection_status = $('#collection_status').val();

    if (collection_status.trim() != 'closed') {
        alertMessage(this, 'red', '', 'Collection status should be closed.');
    } else {
        update_database_row("contacts", "customer_type", "customer", "id", id);
        edit_customer_controller();
        show_detail_lead(id);
    }
    $('#back_button').attr("onClick", back_fnc);
}


function create_radius_account(){
    var dataInfo = {};
    dataInfo['action_id'] = $('#contact_id').val();
    dataInfo['email_address'] = $('#email').val();
    dataInfo['first_name'] = $('#first_name').val();
    dataInfo['last_name'] = $('#last_name').val();
    dataInfo['mobile_no'] = $('#phone1').val();
    dataInfo['phone'] = $("#phone2").val();
    dataInfo['city'] = $("#zone").val();
    dataInfo['srvid'] = $("#package").val();
    dataInfo['user'] = $("#radius_user").val();
    dataInfo['connection_address'] = $('#address1').val();
    var response = connectServer(cms_url['create_radius_account'], dataInfo);
    response = JSON.parse(response);
    if( response.status == "SUCCESS" ){
        return 0;
    }else{
        alertMessage(this, 'red', '', response.msg);
        return 1;
    }
}

function create_cgw_account() {
    var dataInfo = {};
    dataInfo['action_id'] = $('#contact_id').val();
    dataInfo['email_address'] = $('#email').val();
    dataInfo['first_name'] = $('#first_name').val();
    dataInfo['last_name'] = $('#last_name').val();
    dataInfo['mobile_no'] = $('#phone1').val();
    dataInfo['phone'] = $("#phone2").val();
    dataInfo['city'] = $("#zone").val();
    dataInfo['srvid'] = $("#package").val();
    dataInfo['user'] = $("#user").val();
    dataInfo['connection_address'] = $('#address1').val();
    var response = connectServer(cms_url['create_cgw_account'], dataInfo);
    response = JSON.parse(response);
    if( response.status == "SUCCESS" ){
        return 0;
    }else{
        alertMessage(this, 'red', '', response.msg);
        return 1;
    }
  //  $('#payment_doze_id').val(response.doze_id);
}

function star_call_duration() {
    var obj_date = new Date();
    var milliseconds = obj_date.getTime();
    /* Return the number of milliseconds since 1970/01/01:*/
    var seconds = milliseconds / 1000;
    localStorage.star_call_duration = seconds;

}


function show_collect_field() {
    if ($("#enable_collect_person").is(":checked")) {
        $(".collect_parson").show();
    } else {
        $(".collect_parson").hide();
    }
}

function edit_additional_field_connection_cgw(obj) {
    var id = $("#contact_id").val();
    var email = $("#email").val();

    if (email == "" || email == null) {
        alertMessage(this, 'red', '', 'No email address found');
        return false;
    }

    var auth_session_data = checkSession('cms_auth');
    var auth_data = JSON.parse(auth_session_data);

    var dataInfo = {};
    dataInfo['contact_id'] = id;
    dataInfo['email_address'] = email;
    dataInfo['user_id'] = auth_data.user_id;
    dataInfo['user_name'] = auth_data.user_name;

    var response = connectServer(cms_url['physical_connection_cgw_check'], dataInfo);
    response = JSON.parse(response);
    if (response.status == true) {
        alertMessage(this, 'green', 'Success', response.mes);
    } else {
        alertMessage(this, 'red', 'Failed', response.mes);
    }

}

function hide_asde_element() {
    var auth_session_data = checkSession('cms_auth');
    var auth_data = JSON.parse(auth_session_data);
    if ((auth_data.user_role).toLowerCase() == 'asde' || auth_data.group_name == 'ASDE') {
        $('.notForASDE').hide();
        $('#search .btn-group').hide();
    }
}


function check_other_option(){

    var feedback = $("#feedback").val();

    if( feedback == "Others" ){
        $(".other_feedback").show();
    }else{
        $(".other_feedback").hide();
    }
}