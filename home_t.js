/**
 * Created by Monir on 23/08/2015.
 */


function defaultViewController() {
    var cms_auth = checkSession('cms_auth');
    if (cms_auth == null) {
        showUserMenu('login');
    } else {
        showUserMenu('index');
    }
}

function showUserMenu(field_name) {
    message_clear();
    //$('#id_loading_image').show();

    $(".dropdown-menu").on("click", function () {
        $('.header').html(header_menu_html);
    });

    /*pages available before logging in*/
    if (field_name == 'login') {
        displayContent("1", "#cmsData", "#contentListLayout", "ContentID");
    }

    /*pages available after logged in*/
    var cms_auth = checkSession('cms_auth');
    if (cms_auth != null) {
        if (field_name == 'index') {



            loadLeftSideMenu();
            var auth_data = JSON.parse(cms_auth);
              if ((auth_data.user_role).toLowerCase() == 'account_admin') {
                showUserMenu('accounts_manager');
            return;
          }else if( (auth_data.user_role).toLowerCase() == 'account_agent' ){
            showUserMenu('accounts_agent');
           return;
          }

            displayContent("41", "#cmsData", "#contentListLayout", "ContentID");
            report_notification_list();
        } else if (field_name == 'sign_out') {
            clear_droupdown_data();
            user_logout_action();
            cmsLogout(site_host);
        } else if( field_name == 'accounts_agent' ) {
            displayContent("59", "#cmsData", "#contentListLayout", "ContentID");
            view_accounts_tasks();
        } else if(field_name == 'get_collection_task' ){
             displayContent("59", "#cmsData", "#contentListLayout", "ContentID");
	     view_accounts_tasks();
        } else if( field_name == 'accounts_manager' ){
            displayContent("60", "#cmsData", "#contentListLayout", "ContentID");
            fetchDropDownOptionHtml('#agent_select', cms_url['get_accounts_agent'], '');
            doze_DateTimePicker("reassign_date");
            calenderDatePicker(1, "get_account_manager_tasks");
            get_account_manager_tasks();
        }else if( field_name == 'view_all_task' ){
          //  view_all_task
            displayContent("60", "#cmsData", "#contentListLayout", "ContentID");
            fetchDropDownOptionHtml('#agent_select', cms_url['get_accounts_agent'], '');
            doze_DateTimePicker("reassign_date");
            calenderDatePicker(1, "get_account_manager_tasks");
            get_account_manager_tasks();
        }else if( field_name == 'reassign_task' ){
            displayContent("61", "#cmsData", "#contentListLayout", "ContentID");
            get_tasks_to_reassign();
        }else if( field_name == 'view_collection_task' ){
            //displayContent("47", "#cmsData", "#contentListLayout", "ContentID");
        }else if ( field_name == 'contacts' ) {
            displayContent("7", "#cmsData", "#contentListLayout", "ContentID");
            get_lead();
            show_contacts("all");
        } else if (field_name == 'report') {
            displayContent("10", "#cmsData", "#contentListLayout", "ContentID");

        } else if (field_name == 'lead_management_report') {
            displayContent("11", "#cmsData", "#contentListLayout", "ContentID");
            calenderDatePicker(1, "search_customer_promotion_report");
            table_initialize_lead_management_report();
            report_lead_management_report();
        } else if (field_name == 'transmission_report') {
            displayContent("12", "#cmsData", "#contentListLayout", "ContentID");
            calenderDatePicker(1, "search_transmission_assignment_report");
            table_initialize_transmission_assignment_report();
            report_transmission_assignment_report();
        }
        else if (field_name == 'pipeline_report') {
            displayContent("42", "#cmsData", "#contentListLayout", "ContentID");
            calenderDatePicker(1, "search_pipeline_report");
            table_initialize_pipeline_report();
            report_pipeline_report();
        } else if (field_name == 'conversion_cycle_report') {
            displayContent("43", "#cmsData", "#contentListLayout", "ContentID");
            calenderDatePicker(1, "search_conversion_cycle_report");
            table_initialize_conversion_cycle_report();
            report_conversion_cycle_report();
        } else if (field_name == 'conversion_rate_report') {
            displayContent("44", "#cmsData", "#contentListLayout", "ContentID");
            calenderDatePicker(1, "search_conversion_rate_report");
            table_initialize_conversion_rate_report();
            report_conversion_rate_report();
        } else if (field_name == 'next_follow_up_report') {
            displayContent("45", "#cmsData", "#contentListLayout", "ContentID");
            calenderDatePicker(1, "search_next_follow_up_report");
            table_initialize_next_follow_up_report();
            report_next_follow_up_report();
        }  else if (field_name == 'agent_wise_pipeline_report') {
            displayContent("51", "#cmsData", "#contentListLayout", "ContentID");
            calenderDatePicker(1, "search_agent_wise_pipeline_report");
            table_initialize_agent_wise_pipeline_report();
            report_agent_wise_pipeline_report();
        } else if (field_name == 'hourly_call_report') {
            displayContent("51", "#cmsData", "#contentListLayout", "ContentID");
            fetchDropDownOption('#salesRepresentative', cms_url['doze_crm_sales_representative_list'], '');
            calenderDatePicker(1, "search_hourly_call_report");
            table_initialize_hourly_call_report();
            report_hourly_call_report();
        } else if (field_name == 'hourly_visit_report') {
            displayContent("50", "#cmsData", "#contentListLayout", "ContentID");
            calenderDatePicker(1, "search_hourly_visit_report");
            fetchDropDownOption('#salesRepresentative', cms_url['doze_crm_sales_representative_list'], '');
            table_initialize_hourly_visit_report();
            report_hourly_visit_report();
        }

        else if (field_name == 'collection_report') {
            displayContent("13", "#cmsData", "#contentListLayout", "ContentID");
            calenderDatePicker(1, "search_collection_due_report");
            table_initialize_collection_due_report();
            report_collection_due_report();
        } else if (field_name == 'call_report') {
            displayContent("14", "#cmsData", "#contentListLayout", "ContentID");
            fetchDropDownOption('#salesRepresentative', cms_url['doze_crm_sales_representative_list'], '');
            calenderDatePicker(1, "search_call_details_report");
            table_initialize_call_details_report();
            report_call_details_report();
        } else if (field_name == 'sell_report') {
            displayContent("15", "#cmsData", "#contentListLayout", "ContentID");
            calenderDatePicker(1, "search_sell_collection_report");
            table_initialize_sell_collection_report();
            report_sell_collection_report();
        } else if (field_name == 'call_count_report') {
            displayContent("40", "#cmsData", "#contentListLayout", "ContentID");
            search_call_count_report('customer');
        } else if (field_name == 'workflow') {
            displayContent("31", "#cmsData", "#contentListLayout", "ContentID");
            workflowPluginLoad();
            $('#workflow_def_form input[name="action_table"]').val('work_flow_def');
            $('#workflow_detail_form input[name="action_table"]').val('work_flow_details');
            //$('#action_table').val('work_flow_def');
            fetchDropDownOption('#work_flow_id', cms_url['load_workflow_list'], '');
            fetchDropDownOption('#work_flow_id_list', cms_url['load_workflow_list'], '');
            fetchDropDownOption('#user_list_id', cms_url['get_group_list'], '');
            fetchDropDownOption('#task_id', cms_url['get_workflow_tasks'], '');
        } else if (field_name == 'new_contact') {
            displayContent("8", "#cmsData", "#contentListLayout", "ContentID");
            fetchDropDownOption('#do_area', cms_url['doze_crm_area_list'], '');
            calenderDatePicker(0);
        } else if (field_name == 'user_detail') {
            displayContent("9", "#cmsData", "#contentListLayout", "ContentID");
            calenderDatePicker(0);
        } else if (field_name == 'add_task') {
            displayContent("17", "#cmsData", "#contentListLayout", "ContentID");
            calenderDatePicker(0);
            fetchDropDownOption('#task_catagory', cms_url['load_workflow_list'], '');
            fetchDropDownOption('#assignto', cms_url['doze_crm_sales_representative_list'], '');
            fetchDropDownOption('#user_group_id', cms_url['get_group_list'], '');
        } else if (field_name == 'add_defined_task') {
            displayContent("46", "#cmsData", "#contentListLayout", "ContentID");
            fetchDropDownOption('#task_id_select', cms_url['load_task_list'], '');
        } else if (field_name == 'task_list') {
            displayContent("19", "#cmsData", "#contentListLayout", "ContentID");
            table_init_task_list('taskList');
        } else if (field_name == 'task_detail') {
            displayContent("20", "#cmsData", "#contentListLayout", "ContentID");
        } else if (field_name == 'lead_edit') {
            displayContent("21", "#cmsData", "#contentListLayout", "ContentID");
        } else if (field_name == 'admin') {
            displayContent("22", "#cmsData", "#contentListLayout", "ContentID");
        } else if (field_name == 'agent_table') {
            displayContent("23", "#cmsData", "#contentListLayout", "ContentID");
            table_init_sales_representative('tbl_sales_representative', 'sales_representative_table');
            report_sales_representative('sales_representative_table');
        } else if (field_name == 'agent_registration') {
            displayContent("24", "#cmsData", "#contentListLayout", "ContentID");
        } else if (field_name == 'agent_group_creation') {
            agent_group('insert');
        } else if (field_name == 'agent_group_show') {
            displayContent("26", "#cmsData", "#contentListLayout", "ContentID");
            get_group_name();
        } else if (field_name == 'excel_map') {
            displayContent("29", "#cmsData", "#contentListLayout", "ContentID");
        } else if (field_name == 'import_excel') {
            displayContent("28", "#cmsData", "#contentListLayout", "ContentID");
            document.getElementById('uploadFile').onchange = function () {
                $('#fileName').val(this.value);
            };
            $("div.past-info").css("display", "none");
        } else if (field_name == 'otrs_page') {
            displayContent("33", "#cmsData", "#contentListLayout", "ContentID");
            calenderDatePicker(0, "show_date_filter_orts_report");
            table_init_otrs_list();
        } else if (field_name == 'payment_collection') {
            displayContent("34", "#cmsData", "#contentListLayout", "ContentID");
            calenderDatePicker(0);
        } else if (field_name == 'connection_install') {
            displayContent("35", "#cmsData", "#contentListLayout", "ContentID");
            calenderDatePicker(0);
        }
        else if (field_name == 'agent_wise_kpi_report') {
            displayContent("56", "#cmsData", "#contentListLayout", "ContentID");
            fetchDropDownOption('#salesRepresentative', cms_url['doze_crm_sales_representative_list'], '');
            calenderDatePicker(1,"search_agent_wise_kpi");
            table_initialize_agent_wise_kpi_report();
            report_agent_wise_kpi();
        }
    }

    $('#id_loading_image').hide();

    var auth_session_data = checkSession('cms_auth');
    if (auth_session_data != null) {
        var auth_data = JSON.parse(auth_session_data);
        if ((auth_data.user_role).toLowerCase() != 'admin') {
            $('.forAdmin').css("display", "none"); // only for admin show.
            notification_popup();
        }
    }

    try{
        $('#StartDate').datetimepicker({
            format: 'yyyy-mm-dd hh:ii:ss',
            autoclose: 1,
            todayHighlight: 1,
        });


        $('#EndDate').datetimepicker({
            format: 'yyyy-mm-dd hh:ii:ss',
            autoclose: 1,
            todayHighlight: 1,
        });

        $('#StartDate').val(return_local_time());
        $('#EndDate').val(return_local_time());
    }
    catch (ex){

    }
}

function show_contacts_cat(page) {
    var general_filter = $('#general_filter').val();
    var filter_by_lead_source = $('#filter_by_lead_source').val();
    $('#previous_filter').val(general_filter);
    $('#lead_source_filter').val(filter_by_lead_source);
    displayContent("7", "#cmsData", "#contentListLayout", "ContentID");
    show_contacts(page);
    get_lead();

    $("#filter_by_lead_source option[value='" + filter_by_lead_source + "']").attr('selected', true);
    $("#general_filter option[value='" + general_filter + "']").attr('selected', true);
}

function agent_group(action) {
    displayContent("25", "#cmsData", "#contentListLayout", "ContentID");
    table_init_sales_representative_group_creation('tbl_sales_representative', 'sales_representative_table');
    report_sales_representative_group_creation('sales_representative_table', action);
}


function notification_popup() {
    if ($('#notification_popup_status').val() != 1) {
        var dataInfo = {};
        dataSet = connectServer(cms_url['notification_popup'], dataInfo);
        try {
            dataSet = JSON.parse(dataSet);
            if (dataSet.status) {
                row_data = dataSet.data;
                for (i = 0; i < 1; i++) {
                    var check_id = $("#" + row_data[i][0]).val();
                    if (check_id != 1) {
                        $.notify('<a style="text-decoration:none; color:black;" href="#" onclick="show_detail_lead(' + row_data[i][0] + '); return false;"> Call  <span style="color:red; font-weight:bold;"  >' + row_data[i][1] + ' ( ' + row_data[i][2] + ' ) </span> Follow up Time  <span style="color:red; font-weight:bold;"  >' + row_data[i][3] + ' ' + row_data[i][4] + '</span></a>' + '<input type="hidden" value="1" id="' + row_data[i][0] + '" name="' + row_data[i][0] + '">' + '<input type="hidden" value="1" id="notification_popup_status" name="notification_popup_status">', 'success');
                    }
                }
            }
        } catch (ex) {

        }

    }
}