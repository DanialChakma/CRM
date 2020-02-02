/*
 *Host site name
 */
var site_host = window.location.href;
var site_host_temp = site_host.split('/');

var site_host = "";
for (var i = 0; i < (site_host_temp.length - 1); i++) {
    site_host = site_host + site_host_temp[i] + "/";
}

var layoutId = 1;

//var host = site_host; // change with your site name

//cms configuration
cms_service_url = new Array();

cms_service_url['cms_service_host'] = site_host + 'WebFramework/';

cms_service_url['get_header_footer'] = cms_service_url['cms_service_host'] + 'CMSWebService/getHeaderFooter.php?layoutid=' + layoutId;

/*
 *  Cms structure url
 */
cms_url = new Array();
cms_url['get_cms_url'] = site_host + 'ssd_crm/'; // change with your folder name

cms_url['cms_host'] = cms_url['get_cms_url'] + 'webservices/';


cms_url['get_left_menu'] = cms_url['cms_host'] + 'user_login/get_left_menu.php';

cms_url['user_login'] = cms_url['cms_host'] + 'user_login/user_login.php';
cms_url['user_session'] = cms_url['cms_host'] + 'user_login/user_session.php';
cms_url['user_log_out'] = cms_url['cms_host'] + 'user_login/user_log_out.php';
/* =====================================
 *      Talemul
 * =====================================*/
cms_url['select_stage'] = cms_url['cms_host'] + 'contacts/select_stage.php';
cms_url['select_note'] = cms_url['cms_host'] + 'contacts/select_note.php';
cms_url['doze_crm_get_pipeline_report'] = cms_url['cms_host'] + 'report/doze_crm_get_pipeline_report.php';

cms_url['get_conversion_cycle_report'] = cms_url['cms_host'] + 'report/get_conversion_cycle_report.php';
cms_url['get_conversion_rate_report'] = cms_url['cms_host'] + 'report/get_conversion_rate_report.php';
cms_url['get_next_follow_up_report'] = cms_url['cms_host'] + 'report/get_next_follow_up_report.php';
cms_url['notification_popup'] = cms_url['cms_host'] + 'report/notification_popup.php';
cms_url['add_auto_today_task'] = cms_url['cms_host'] + 'task/add_auto_today_task.php';
cms_url['save_change_stage_history'] = cms_url['cms_host'] + 'contacts/save_change_stage_history.php';
cms_url['delete_user_info'] = cms_url['cms_host'] + 'user_info/delete_user_info.php';
cms_url['export_contacts_excel'] = cms_url['cms_host'] + 'contacts/export_contacts_excel.php';
cms_url['get_hourly_call_report'] = cms_url['cms_host'] + 'report/get_hourly_call_report.php';
cms_url['get_hourly_visit_report'] = cms_url['cms_host'] + 'report/get_hourly_visit_report.php';

cms_url['select_connection_type'] = cms_url['cms_host'] + 'contacts/select_connection_type.php';
cms_url['select_corporate_stage'] = cms_url['cms_host'] + 'contacts/select_corporate_stage.php';
cms_url['select_industry_seg'] = cms_url['cms_host'] + 'contacts/select_industry_seg.php';
cms_url['select_other_service_charge'] = cms_url['cms_host'] + 'contacts/select_other_service_charge.php';
cms_url['select_packaging'] = cms_url['cms_host'] + 'contacts/select_packaging.php';

cms_url['save_additional_field'] = cms_url['cms_host'] + 'contacts/save_additional_field.php';
/* =====================================
 *      Monir
 * =====================================*/
cms_url['doze_crm_get_lead_management_report'] = cms_url['cms_host'] + 'report/get_lead_management_report.php';
cms_url['doze_crm_get_transmission_assignment_report'] = cms_url['cms_host'] + 'report/get_transmission_assignment_report.php';
cms_url['doze_crm_get_collection_due_report'] = cms_url['cms_host'] + 'report/get_collection_due_report.php';
cms_url['doze_crm_get_call_details_report'] = cms_url['cms_host'] + 'report/get_call_details_report.php';
cms_url['doze_crm_sales_representative_list'] = cms_url['cms_host'] + 'report/get_representative_list.php';
cms_url['doze_crm_get_sell_collection_report'] = cms_url['cms_host'] + 'report/get_sell_collection_report.php';
cms_url['doze_crm_get_notification_list'] = cms_url['cms_host'] + 'notification/get_notification_list.php';
cms_url['doze_crm_update_notification_list'] = cms_url['cms_host'] + 'notification/update_notification.php';
cms_url['doze_crm_get_call_count_report'] = cms_url['cms_host'] + 'report/get_call_count_report.php';
cms_url['doze_crm_get_pipeline_health_report'] = cms_url['cms_host'] + 'report/get_pipeline_health_report.php';
cms_url['doze_crm_get_agent_wise_projected_sales_report'] = cms_url['cms_host'] + 'report/get_agent_wise_sales_projection.php';


/* =====================================
 *      Nazibul start...................
 * =====================================*/

cms_url['database_editor_custom'] = cms_url['cms_host'] + 'database_editor_custom/database_editor_custom.php';
cms_url['database_editor_with_form'] = cms_url['cms_host'] + 'database_editor_custom/database_editor_with_form.php';
cms_url['doze_crm_sales_representative_list'] = cms_url['cms_host'] + 'report/get_representative_list.php';
cms_url['add_new_task'] = cms_url['cms_host'] + 'task/add_new_task.php';
cms_url['task_list'] = cms_url['cms_host'] + 'task/task_list.php';
cms_url['task_detail'] = cms_url['cms_host'] + 'task/task_detail.php';
cms_url['done_this_task'] = cms_url['cms_host'] + 'task/done_this_task.php';
cms_url['load_workflow_list'] = cms_url['cms_host'] + 'task/load_workflow_list.php';
cms_url['load_workflow_def_details'] = cms_url['cms_host'] + 'workflow/load_workflow_def_details.php';
cms_url['worf_flow_def_save'] = cms_url['cms_host'] + 'workflow/worf_flow_def_save.php';
cms_url['load_workflow_detail_list'] = cms_url['cms_host'] + 'workflow/load_workflow_detail_list.php';
cms_url['workflow_detail_details'] = cms_url['cms_host'] + 'workflow/workflow_detail_details.php';
cms_url['workflow_user_list'] = cms_url['cms_host'] + 'workflow/workflow_user_list.php';
cms_url['save_workflow_user_list'] = cms_url['cms_host'] + 'workflow/save_workflow_user_list.php';
cms_url['get_group_list'] = cms_url['cms_host'] + 'workflow/get_group_list.php';
cms_url['get_workflow_tasks'] = cms_url['cms_host'] + 'workflow/get_workflow_tasks.php';
cms_url['otrs_list'] = cms_url['cms_host'] + 'otrs/otrs_list.php';
cms_url['generate_all_ticket'] = cms_url['cms_host'] + 'otrs/generate_all_ticket.php';
cms_url['generate_ticket'] = cms_url['cms_host'] + 'otrs/generate_ticket.php';
cms_url['payment_collection_record'] = cms_url['cms_host'] + 'task/payment_collection_record.php';
cms_url['payment_status_update'] = cms_url['cms_host'] + 'task/payment_status_update.php';
cms_url['connection_install'] = cms_url['cms_host'] + 'task/connection_install.php';
cms_url['create_cgw_account'] = cms_url['cms_host'] + 'contacts/create_cgw_account.php';
cms_url['create_radius_account'] = cms_url['cms_host'] + 'contacts/create_radius_account.php';
cms_url['excel_contact_import_old'] = cms_url['cms_host'] + 'contacts/excel_contact_import_old.php';
cms_url['remove_contact_from_task'] = cms_url['cms_host'] + 'task/remove_contact_from_task.php';
cms_url['load_task_list'] = cms_url['cms_host'] + 'task/load_task_list.php';
cms_url['add_contact_to_task'] = cms_url['cms_host'] + 'task/add_contact_to_task.php';
cms_url['get_agent_wise_pipeline_report'] = cms_url['cms_host'] + 'report/get_agent_wise_pipeline_report.php';
cms_url['get_sq_report'] = cms_url['cms_host'] + 'report/get_sq_report.php';
cms_url['physical_connection_cgw_check'] = cms_url['cms_host'] + 'contacts/physical_connection_cgw_check.php';

/* =====================================
 *      Nazibul end.....................
 * =====================================*/

/* =====================================
 *      Mahmud start...................
 * =====================================*/
cms_url['show_all_contacts'] = cms_url['cms_host'] + 'contacts/show_all_contacts.php';
cms_url['get_lead_source_name'] = cms_url['cms_host'] + 'contacts/get_lead_source_name.php';
cms_url['get_contact_info'] = cms_url['cms_host'] + 'contacts/get_contact_info.php';
cms_url['customer_block_callhistory'] = cms_url['cms_host'] + 'contacts/customer_block_callhistory.php';
cms_url['select_lead_source'] = cms_url['cms_host'] + 'contacts/select_lead_source.php';
cms_url['doze_crm_sales_representative_list'] = cms_url['cms_host'] + 'contacts/get_representative_list.php';
cms_url['doze_crm_area_list'] = cms_url['cms_host'] + 'contacts/get_do_area_list.php';
cms_url['get_otrs_payment'] = cms_url['cms_host'] + 'contacts/get_otrs_payment.php';
cms_url['get_payment_collection_mode'] = cms_url['cms_host'] + 'contacts/get_payment_collection_mode.php';
cms_url['get_conversion_history'] = cms_url['cms_host'] + 'contacts/get_conversion_history.php';
cms_url['doze_crm_get_customer_payment'] = cms_url['cms_host'] + 'contacts/get_customer_payment.php';
cms_url['doze_crm_prospective_customer_save'] = cms_url['cms_host'] + 'contacts/save_prospective_customer.php';
cms_url['save_customer_feedback'] = cms_url['cms_host'] + 'contacts/save_customer_feedback.php';
cms_url['doze_crm_conversion_history_save'] = cms_url['cms_host'] + 'contacts/save_conversion_history.php';
cms_url['doze_crm_otrs_ticket_save'] = cms_url['cms_host'] + 'contacts/save_otrs_ticket.php';
cms_url['doze_crm_payment_collection_save'] = cms_url['cms_host'] + 'contacts/save_payment_collection.php';
cms_url['doze_crm_package_list'] = cms_url['cms_host'] + 'contacts/get_doze_internet_package.php';
cms_url['sales_representative'] = cms_url['cms_host'] + 'sales_representative/sales_representative.php';
cms_url['add_user'] = cms_url['cms_host'] + 'user_info/add_user.php';
cms_url['sales_representative_group_creation'] = cms_url['cms_host'] + 'sales_group/create_group_sales_representative.php';
cms_url['save_new_group'] = cms_url['cms_host'] + 'sales_group/save_new_group.php';
cms_url['get_group_name'] = cms_url['cms_host'] + 'sales_group/get_group_name.php';
cms_url['group_show'] = cms_url['cms_host'] + 'sales_group/group_show.php';
cms_url['create_new_contact'] = cms_url['cms_host'] + 'contacts/create_new_contact.php';
cms_url['gneric_search'] = cms_url['cms_host'] + 'contacts/gneric_search.php';
cms_url['excel_contact_import'] = cms_url['cms_host'] + 'contacts/import_contact.php';
cms_url['excel_coulmmn_name'] = cms_url['cms_host'] + 'contacts/excel_column_name.php';

/* =====================================
 *      Mahmud end.....................
 * =====================================*/

/* =====================================
 *      Danial Start.....................
 * =====================================*/

// fin bill
cms_url['initiate_fin_bill'] = cms_url['cms_host'] + 'fintelligent/initiate_fin_bill.php';
cms_url['agent_wise_accounts_task'] = cms_url['cms_host'] + 'fintelligent/view_accounts_task.php';
cms_url['get_task_details'] = cms_url['cms_host'] + 'fintelligent/get_task_details.php';
cms_url['get_account_manager_task'] = cms_url['cms_host'] + 'fintelligent/get_account_manager_tasks.php';
cms_url['get_task_details_of_bill_id'] = cms_url['cms_host'] + 'fintelligent/get_task_details_of_bill.php';
cms_url['get_task_details_of_mbill_id'] = cms_url['cms_host'] + 'fintelligent/get_task_details_of_mbill.php';
cms_url['get_manager_task_details_of_bill_id'] = cms_url['cms_host'] + 'fintelligent/get_manager_task_details_of_bill.php';
cms_url['get_manager_task_details_for_monthly_bill'] = cms_url['cms_host'] + 'fintelligent/get_manager_task_details_mbill.php';
cms_url['approve_accounts_task'] = cms_url['cms_host'] + 'fintelligent/approve_accounts_task.php';
cms_url['approve_accounts_task_mbill'] = cms_url['cms_host'] + 'fintelligent/approve_accounts_task_mbill.php';
cms_url['hold_accounts_task'] = cms_url['cms_host'] + 'fintelligent/hold_accounts_task.php';
cms_url['hold_accounts_task_mbill'] = cms_url['cms_host'] + 'fintelligent/hold_accounts_task_mbill.php';
cms_url['reject_accounts_task'] = cms_url['cms_host'] + 'fintelligent/reject_accounts_task.php';
cms_url['reject_accounts_task_mbill'] = cms_url['cms_host'] + 'fintelligent/reject_accounts_task_mbill.php';
cms_url['get_accounts_agent'] = cms_url['cms_host'] + 'fintelligent/get_accounts_agent.php';
cms_url['assign_task_to_agent'] = cms_url['cms_host'] + 'fintelligent/assign_task_to_agent.php';
cms_url['execute_manager_task'] = cms_url['cms_host'] + 'fintelligent/execute_manager_task.php';
cms_url['reject_manager_task']  =  cms_url['cms_host'] + 'fintelligent/reject_manager_task.php';
cms_url['hold_manager_task']  =  cms_url['cms_host'] + 'fintelligent/hold_manager_task.php';
cms_url['approve_manager_task'] = cms_url['cms_host'] + 'fintelligent/approve_manager_task.php';
cms_url['select_zone'] = cms_url['cms_host'] + 'fintelligent/get_select_zone.php';
cms_url['generate_call_list'] = cms_url['cms_host'] + 'fintelligent/generate_call_list.php';
cms_url['get_call_list'] = cms_url['cms_host'] + 'fintelligent/get_call_list.php';
cms_url['assign_call_to_agent'] =  cms_url['cms_host'] + 'fintelligent/assign_call_to_agent.php';
cms_url['get_monthly_call_list'] = cms_url['cms_host'] + 'fintelligent/get_monthly_call_list.php';
cms_url['get_monthly_call_history'] = cms_url['cms_host'] + 'fintelligent/get_radius_call_history.php';
cms_url['get_monthly_call_details'] = cms_url['cms_host'] + 'fintelligent/get_monthly_call_details.php';
cms_url['save_monthly_call_status'] = cms_url['cms_host'] + 'fintelligent/save_monthly_call_status.php';
cms_url['generate_check_book'] = cms_url['cms_host'] + 'fintelligent/generate_check_book.php';
cms_url['get_cheque_book_option'] = cms_url['cms_host'] + 'fintelligent/get_cheque_book_option.php';
cms_url['get_cheque_book_pages'] = cms_url['cms_host'] + 'fintelligent/get_cheque_book_pages.php';
cms_url['get_available_cheque_book_pages'] = cms_url['cms_host'] + 'fintelligent/get_available_cheque_book_pages.php';

cms_url['hold_manager_task_mbill'] = cms_url['cms_host'] + 'fintelligent/hold_manager_task_mbill.php';
cms_url['reject_manager_task_mbill'] = cms_url['cms_host'] + 'fintelligent/reject_manager_task_mbill.php';
cms_url['approve_manager_task_mbill'] = cms_url['cms_host'] + 'fintelligent/approve_manager_task_mbill.php';
cms_url['execute_manager_task_mbill'] = cms_url['cms_host'] + 'fintelligent/execute_manager_task_mbill.php';
cms_url['get_invoice_data'] = cms_url['cms_host'] + 'fintelligent/get_invoice_data.php';
cms_url['get_month_wise_connection_data'] =  cms_url['cms_host'] + 'report/month_wise_connection_report.php';
cms_url['get_current_month_connection_data'] = cms_url['cms_host'] + 'report/current_month_connection_report.php';
cms_url['get_agent_wise_connection_data'] = cms_url['cms_host'] + 'report/agent_wise_connection.php';

cms_url['get_cgw_call_list'] = cms_url['cms_host'] + 'fintelligent/get_radius_call_list.php';
cms_url['save_cgw_call_list'] = cms_url['cms_host'] + 'fintelligent/save_cgw_call_list.php';
cms_url['get_call_list_history'] = cms_url['cms_host'] + 'fintelligent/get_call_list_history.php';
cms_url['serach_for_radius_user'] = cms_url['cms_host'] + 'fintelligent/get_user_search_result.php';
cms_url['save_collection_task'] = cms_url['cms_host'] + 'fintelligent/save_collection_task.php';