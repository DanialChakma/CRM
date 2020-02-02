function table_initialize_lead_management_report() {

    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="customer_promotion_report" width="100%"  ><tr><td  align="center"></td></tr></table>');
    /*<tfoot><th>Global Total</th><th></th></tfoot>*/

}

function report_lead_management_report() {

    var dataSet = [[]];
    var dataInfo = {};
    dataInfo['current_date_js'] = return_current_date();

    //$('#StartDate').val(return_current_date());
    //$('#EndDate').val(return_current_date());

    var date_from = $('#StartDate').val();
    var date_to = $('#EndDate').val();

    if (date_from.trim() != '') {
        dataInfo['date_from'] = date_from;
    }

    if (date_to.trim() != '') {
        dataInfo['date_to'] = date_to;
    }

    dataSet = connectServer(cms_url['doze_crm_get_lead_management_report'], dataInfo);
    //  alert(dataSet);
    dataSet = JSON.parse(dataSet);
    //alert(dataSet);
    table_data_customer_promotion_report(dataSet);


}


function table_data_customer_promotion_report(dataSet) {
    $('#customer_promotion_report').dataTable({
        "data": dataSet,
        "columns": [
            { "title": "Name", "class": "center" },
            { "title": "Customer ID", "class": "center" },
            { "title": "Sale Done Date", "class": "center" },
            { "title": "Mobile No", "class": "center" },
            { "title": "Address", "class": "center" },
            { "title": "E-Mail", "class": "center" },
            { "title": "DO", "class": "center" },
            { "title": "Package Type", "class": "center" },
            { "title": "Package", "class": "center" },
            { "title": "Assigned Agent", "class": "center" },
            { "title": "Agent Role", "class": "center" },
            { "title": "Installation Amount", "class": "center" },
            { "title": "Monthly Amount", "class": "center" },
            { "title": "Month Number", "class": "center" },
            { "title": "Real IP Cost", "class": "center" },
            { "title": "Others Amount", "class": "center" },
            { "title": "Total Amount", "class": "center" },
            { "title": "Collection Date", "class": "center" },
            { "title": "Money Receipt No", "class": "center" },
            { "title": "Installation Ticket", "class": "center" },
            { "title": "Payment Mode", "class": "center" },
        ],
        "order": [[0, "asc"]],
        dom: '<"clear">rtipTf',
        scrollX: true,
        'iDisplayLength': 15,
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "lead_managment_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "lead_managment_report.csv"
                }
            ],
            "filter": "applied"

        }/*,
         "footerCallback": function (row, data, start, end, display) {
         var api = this.api(), data;

         if (api.column(1).data().length > 0) {

         // Total over all pages
         //total = api
         //    .column(3)
         //    .data()
         //    .reduce(function (a, b) {
         //        return parseInt(a) + parseInt(b);
         //    });

         // Total over this page
         pageTotal = api
         .column(1, {page: 'current'})
         .data()
         .reduce(function (a, b) {
         return parseInt(a) + parseInt(b);
         }, 0);

         //pageTotal2 = api
         //    .column(2, {page: 'current'})
         //    .data()
         //    .reduce(function (a, b) {
         //        return parseInt(a) + parseInt(b);
         //    }, 0);

         // Update footer
         $(api.column(1).footer()).html(
         '' + pageTotal + ' '
         );
         //$(api.column(2).footer()).html(
         //    '' + pageTotal2 + ' '
         //);

         }
         }*/
    });
}


function search_customer_promotion_report() {
    table_initialize_lead_management_report();
    report_lead_management_report();
}

// Transmission Assignment Report


function table_initialize_transmission_assignment_report() {

    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="transmission_assignment_report" width="100%"  ><tr><td  align="center"></td></tr></table>');

}

function report_transmission_assignment_report() {

    //  var dataSet = [[]];
    var dataInfo = {};

    //$('#StartDate').val(return_current_date());
    //$('#EndDate').val(return_current_date());

    var date_from = $('#StartDate').val();
    var date_to = $('#EndDate').val();

    if (date_from.trim() != '') {
        dataInfo['date_from'] = date_from;
    }

    if (date_to.trim() != '') {
        dataInfo['date_to'] = date_to;
    }

    var dataSet = connectServer(cms_url['doze_crm_get_transmission_assignment_report'], dataInfo);
    //  alert(dataSet);
    try {
        //  console.log(dataSet);

        dataSet = JSON.parse(dataSet);
        //alert(dataSet);
        table_data_transmission_assignment_report(dataSet);
    }
    catch (exp) {
        console.log(exp);
    }

}


function table_data_transmission_assignment_report(dataSet) {
    $('#transmission_assignment_report').dataTable({
        "data": dataSet,
        "columns": [
            {"title": "Sr No", "class": "center"},
            {"title": "Ticket No", "class": "center"},
            {"title": "Contact No", "class": "center"},
            {"title": "Connection Name", "class": "center"},
            {"title": "Address", "class": "center"},
            {"title": "Area", "class": "center"},
            {"title": "DO", "class": "center"},
            {"title": "Ticket Entry Date", "class": "center"},
            {"title": "Connection Due Date", "class": "center"},
            {"title": "Issues", "class": "center"},
        ],
        "order": [[0, "asc"]],
        dom: '<"clear">rtipTf',
        scrollX: true,
        'iDisplayLength': 15,
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "transmission_assignment_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "transmission_assignment_report.csv"
                }
            ],
            "filter": "applied"

        }
    });
}


function search_transmission_assignment_report() {
    table_initialize_transmission_assignment_report();
    report_transmission_assignment_report();
}


function table_initialize_pipeline_report() {

    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="pipeline_report" width="100%"  ><tr><td  align="center"></td></tr></table>');

}

function report_pipeline_report() {

    //  var dataSet = [[]];
    var dataInfo = {};

    //$('#StartDate').val(return_current_date());
    //$('#EndDate').val(return_current_date());

    var date_from = $('#StartDate').val();
    var date_to = $('#EndDate').val();

    if (date_from.trim() != '') {
        dataInfo['date_from'] = date_from;
    }

    if (date_to.trim() != '') {
        dataInfo['date_to'] = date_to;
    }

    var dataSet = connectServer(cms_url['doze_crm_get_pipeline_report'], dataInfo);
    //  alert(dataSet);
    try {
        //  console.log(dataSet);

        dataSet = JSON.parse(dataSet);
        //alert(dataSet);
        table_data_pipeline_report(dataSet);
    }
    catch (exp) {
        console.log(exp);
    }

}

/**/

function table_data_pipeline_report(dataSet) {
    var monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    var d = new Date();
    //  document.write("The current month is " + monthNames[d.getMonth()]);
    var previous_1, previous_2, previous_3;
    if (d.getMonth() > 1) {
        previous_1 = d.getMonth() - 1;
    } else {
        previous_1 = d.getMonth() + 11;
    }
    if (d.getMonth() > 2) {
        previous_2 = d.getMonth() - 2;
    } else {
        previous_2 = d.getMonth() + 11 - 1;
    }
    if (d.getMonth() > 3) {
        previous_3 = d.getMonth() - 3;
    } else {
        previous_3 = d.getMonth() + 11 - 2;
    }
//console.log(monthNames[previous_1]+'|'+monthNames[previous_2]+'|'+monthNames[previous_3]+'|'+previous_1+'|'+previous_2+'|'+previous_3);
    $('#pipeline_report').dataTable({
        "data": dataSet,
        "columns": [
            {"title": "Agent", "class": "center"},
            {"title": "Sales Done", "class": "center"},
            {"title": "Current Month", "class": "center"},
            {"title": monthNames[previous_1], "class": "center"},
            {"title": monthNames[previous_2], "class": "center"},
            {"title": monthNames[previous_3], "class": "center"},
            {"title": "Total Sales Done", "class": "center"},
            {"title": "Attempted", "class": "center"},
            {"title": "Connected", "class": "center"},
            {"title": "Intersted", "class": "center"},
            {"title": "Verbally Confirmed", "class": "center"},
            {"title": "Total Verbally Confirmed", "class": "center"},
        ],
        "order": [[0, "asc"]],
        dom: '<"clear">rtipTf',
        scrollX: true,
        'iDisplayLength': 15,
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "transmission_assignment_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "transmission_assignment_report.csv"
                }
            ],
            "filter": "applied"

        }
    });
}

function search_pipeline_report() {
    table_initialize_pipeline_report();
    report_pipeline_report();
}

//conversion cycle report


function table_initialize_conversion_cycle_report() {

    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="pipeline_report" width="100%"  ><tr><td  align="center"></td></tr></table>');

}

function report_conversion_cycle_report() {

    //  var dataSet = [[]];
    var dataInfo = {};

    //$('#StartDate').val(return_current_date());
    //$('#EndDate').val(return_current_date());

    var date_from = $('#StartDate').val();
    var date_to = $('#EndDate').val();

    if (date_from.trim() != '') {
        dataInfo['date_from'] = date_from;
    }

    if (date_to.trim() != '') {
        dataInfo['date_to'] = date_to;
    }

    var dataSet = connectServer(cms_url['get_conversion_cycle_report'], dataInfo);
    //  alert(dataSet);
    try {
        //  console.log(dataSet);

        dataSet = JSON.parse(dataSet);
        //alert(dataSet);
        table_data_conversion_cycle_report(dataSet);
    }
    catch (exp) {
        console.log(exp);
    }

}

/**/

function table_data_conversion_cycle_report(dataSet) {
    var monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    var d = new Date();
    //  document.write("The current month is " + monthNames[d.getMonth()]);


    $('#pipeline_report').dataTable({
        "data": dataSet,
        "columns": [
            {"title": "Date", "class": "center"},
            {"title": "Agent Name", "class": "center"},
            {"title": "Lead Source", "class": "center"},
            {"title": "Lead Contact No", "class": "center"},
            {"title": "Customer Name", "class": "center"},
            {"title": "Connected Date", "class": "center"},
            {"title": "Interested Day", "class": "center"},
            {"title": "Interested Date", "class": "center"},
            {"title": "VC Day", "class": "center"},
            {"title": "VC Date", "class": "center"},
            {"title": "Sales Done Day", "class": "center"},
            {"title": "Sales Done Date", "class": "center"},
            {"title": "Total Days", "class": "center"}
        ],
        "order": [[0, "asc"]],
        dom: '<"clear">rtipTf',
        scrollX: true,
        'iDisplayLength': 15,
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "transmission_assignment_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "transmission_assignment_report.csv"
                }
            ],
            "filter": "applied"

        }
    });
}

function search_conversion_cycle_report() {
    table_initialize_conversion_cycle_report();
    report_conversion_cycle_report();
}

// conversion rate report


function table_initialize_conversion_rate_report() {

    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="pipeline_report" width="100%"  ><tr><td  align="center"></td></tr></table>');

}

function report_conversion_rate_report() {

    //  var dataSet = [[]];
    var dataInfo = {};

    //$('#StartDate').val(return_current_date());
    //$('#EndDate').val(return_current_date());

    var date_from = $('#StartDate').val();
    var date_to = $('#EndDate').val();

    if (date_from.trim() != '') {
        dataInfo['date_from'] = date_from;
    }

    if (date_to.trim() != '') {
        dataInfo['date_to'] = date_to;
    }

    var dataSet = connectServer(cms_url['get_conversion_rate_report'], dataInfo);
    //  alert(dataSet);
    try {
        //  console.log(dataSet);

        dataSet = JSON.parse(dataSet);
        //alert(dataSet);
        table_data_conversion_rate_report(dataSet);
    }
    catch (exp) {
        console.log(exp);
    }

}

/**/

function table_data_conversion_rate_report(dataSet) {
    var monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    var d = new Date();
    //  document.write("The current month is " + monthNames[d.getMonth()]);


    $('#pipeline_report').dataTable({
        "data": dataSet,
        "columns": [
            {"title": "Agent", "class": "center"},
            {"title": "Attempted>Connected", "class": "center"},
            {"title": "Connected>Intersted", "class": "center"},
            {"title": "Intersted>Varbally Confirmed", "class": "center"},
        ],
        "order": [[0, "asc"]],
        dom: '<"clear">rtipTf',
        scrollX: true,
        'iDisplayLength': 15,
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "transmission_assignment_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "transmission_assignment_report.csv"
                }
            ],
            "filter": "applied"

        }
    });
}

function search_conversion_rate_report() {
    table_initialize_conversion_rate_report();
    report_conversion_rate_report();
}

// next follow up date


function table_initialize_next_follow_up_report() {

    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="next_follow_up_report" width="100%"  ><tr><td  align="center"></td></tr></table>');

}

function report_next_follow_up_report() {

    //  var dataSet = [[]];
    var dataInfo = {};

    //$('#StartDate').val(return_current_date());
    //$('#EndDate').val(return_current_date());

    var date_from = $('#StartDate').val();
    var date_to = $('#EndDate').val();

    if (date_from.trim() != '') {
        dataInfo['date_from'] = date_from;
    }

    if (date_to.trim() != '') {
        dataInfo['date_to'] = date_to;
    }

    var dataSet = connectServer(cms_url['get_next_follow_up_report'], dataInfo);
    //  alert(dataSet);
    try {
        //  console.log(dataSet);

        dataSet = JSON.parse(dataSet);
        //alert(dataSet);
        table_data_next_follow_up_report(dataSet);
    }
    catch (exp) {
        console.log(exp);
    }

}

/**/

function table_data_next_follow_up_report(dataSet) {
    var monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    var d = new Date();
    //  document.write("The current month is " + monthNames[d.getMonth()]);


    $('#next_follow_up_report').dataTable({
        "data": dataSet,
        "columns": [
            {"title": "SL", "class": "center"},
            {"title": "Mobile Number", "class": "center"},
            {"title": "Agent Name", "class": "center"},
            {"title": "Next Follow-up", "class": "center"},
            {"title": "Time", "class": "center"},
            {"title": "Feedback", "class": "center"},
            {"title": "Stage", "class": "center"}
        ],
        "order": [[0, "asc"]],
        dom: '<"clear">rtipTf',
        scrollX: true,
        'iDisplayLength': 15,
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "transmission_assignment_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "transmission_assignment_report.csv"
                }
            ],
            "filter": "applied"

        }
    });
}

function search_next_follow_up_report() {
    table_initialize_next_follow_up_report();
    report_next_follow_up_report();
}


// hourly_visit_report


function table_initialize_hourly_visit_report() {

    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="hourly_visit_report" width="100%"  ><tr><td  align="center"></td></tr></table>');

}

function report_hourly_visit_report() {

    //  var dataSet = [[]];
    var dataInfo = {};

    //$('#StartDate').val(return_current_date());
    //$('#EndDate').val(return_current_date());

    var date_from = $('#StartDate').val();
    var date_to = $('#EndDate').val();
    var sales_representative = $('#salesRepresentative').val();
    if (date_from.trim() != '') {
        dataInfo['date_from'] = date_from;
    }

    if (date_to.trim() != '') {
        dataInfo['date_to'] = date_to;
    }
    if (sales_representative.trim() != '') {
        dataInfo['sales_representative'] = sales_representative;
    }
    var dataSet = connectServer(cms_url['get_hourly_visit_report'], dataInfo);
    //  alert(dataSet);
    try {
        //  console.log(dataSet);

        dataSet = JSON.parse(dataSet);
        //alert(dataSet);
        table_data_hourly_visit_report(dataSet);
    }
    catch (exp) {
        console.log(exp);
    }

}

/**/

function table_data_hourly_visit_report(dataSet) {

  //  console.log(dataSet);
    $('#hourly_visit_report').dataTable({
        "data": dataSet,
        "columns": [
            {"title": "Date", "class": "center", "defaultContent": ""},
            {"title": "Hour", "class": "center", "defaultContent": ""},
            {"title": "Agent Name", "class": "center", "defaultContent": ""},
            {"title": "Sum of Follow Up Visit Made", "class": "center", "defaultContent": ""},
            {"title": "Sum of New Visit", "class": "center", "defaultContent": ""},
            {"title": "Sum of Total Call", "class": "center", "defaultContent": ""},
            {"title": "Explore", "class": "center", "defaultContent": ""},
            {"title": "Establish", "class": "center", "defaultContent": ""},
            {"title": "Evaluate", "class": "center", "defaultContent": ""},
            {"title": "Execute", "class": "center", "defaultContent": ""},
            {"title": "Large Companies", "class": "center", "defaultContent": ""},
            {"title": "Bank, Insurance & Leasing", "class": "center", "defaultContent": ""},
            {"title": "MNC", "class": "center", "defaultContent": ""},
            {"title": "IT & Software Firms", "class": "center", "defaultContent": ""},
        ],
        "order": [[0, "asc"]],
        dom: '<"clear">rtipTf',
        scrollX: true,
        'iDisplayLength': 15,
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "transmission_assignment_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "transmission_assignment_report.csv"
                }
            ],
            "filter": "applied"

        }
    });
}

function search_hourly_visit_report() {
    table_initialize_hourly_visit_report();
    report_hourly_visit_report();
}// hourly_call_report


function table_initialize_hourly_call_report() {

    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="hourly_call_report" width="100%"  ><tr><td  align="center"></td></tr></table>');

}

function report_hourly_call_report() {

    //  var dataSet = [[]];
    var dataInfo = {};

    var date_from = $('#StartDate').val();
    var date_to = $('#EndDate').val();
    var sales_representative = $('#salesRepresentative').val();

    if (date_from.trim() != '') {
        dataInfo['date_from'] = date_from;
    }

    if (date_to.trim() != '') {
        dataInfo['date_to'] = date_to;
    }
    if (sales_representative.trim() != '') {
        dataInfo['sales_representative'] = sales_representative;
    }
    var dataSet = connectServer(cms_url['get_hourly_call_report'], dataInfo);
    //  alert(dataSet);
    try {
        //  console.log(dataSet);

        dataSet = JSON.parse(dataSet);
        //alert(dataSet);
        table_data_hourly_call_report(dataSet);
    }
    catch (exp) {
        console.log(exp);
    }

}

/**/

function table_data_hourly_call_report(dataSet) {


    var d = new Date();
    //  document.write("The current month is " + monthNames[d.getMonth()]);


    $('#hourly_call_report').dataTable({
        "data": dataSet,
        "columns": [
            {"title": "Time", "class": "center", "defaultContent": ""},
            {"title": "Date", "class": "center", "defaultContent": ""},
            {"title": "Total Call", "class": "center", "defaultContent": ""},
            {"title": "New Call", "class": "center", "defaultContent": ""},
            {"title": "Not Intersted", "class": "center", "defaultContent": ""},
            {"title": "Intersted", "class": "center", "defaultContent": ""},
            {"title": "Verbally Closed", "class": "center", "defaultContent": ""},
            {"title": "New Call", "class": "center", "defaultContent": ""},
            {"title": "Not Intersted", "class": "center", "defaultContent": ""},
            {"title": "Intersted", "class": "center", "defaultContent": ""},
            {"title": "Verbally Closed", "class": "center", "defaultContent": ""},
            {"title": "Not Intersted", "class": "center", "defaultContent": ""},
            {"title": "Intersted", "class": "center", "defaultContent": ""},
            {"title": "Verbally Closed", "class": "center", "defaultContent": ""},
            {"title": "Sale", "class": "center", "defaultContent": ""},
            {"title": "Agent Count", "class": "center", "defaultContent": ""}
        ],
        "order": [[1, "asc"]],
        dom: '<"clear">rtipTf',
        scrollX: true,
        'iDisplayLength': 15,
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "transmission_assignment_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "transmission_assignment_report.csv"
                }
            ],
            "filter": "applied"

        }
    });
}

function search_hourly_call_report() {
    table_initialize_hourly_call_report();
    report_hourly_call_report();
}
// Collection Due Report


function table_initialize_collection_due_report() {

    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="collection_due_report" width="100%"  ><tr><td  align="center"></td></tr></table>');

}

function report_collection_due_report() {

    var dataSet = [[]];
    var dataInfo = {};
    dataInfo['current_date_js'] = return_current_date();

    var date_from = $('#StartDate').val();
    var date_to = $('#EndDate').val();

    if (date_from.trim() != '') {
        dataInfo['date_from'] = date_from;
    }

    if (date_to.trim() != '') {
        dataInfo['date_to'] = date_to;
    }

    dataSet = connectServer(cms_url['doze_crm_get_collection_due_report'], dataInfo);
    //  alert(dataSet);
    dataSet = JSON.parse(dataSet);
    //alert(dataSet);
    table_data_collection_due_report(dataSet);


}


function table_data_collection_due_report(dataSet) {
    $('#collection_due_report').dataTable({
        "data": dataSet,
        "columns": [
            {"title": "Sr No", "class": "center"},
            {"title": "Action", "class": "center"},
            {"title": "Connection Name", "class": "center"},
            {"title": "Email", "class": "center"},
            {"title": "Contact No", "class": "center"},
            {"title": "Address", "class": "center"},
            {"title": "DO", "class": "center"},
            {"title": "Area", "class": "center"},
            {"title": "Assignment Date", "class": "center"},
            {"title": "Collection Date", "class": "center"},
            {"title": "Collection Time", "class": "center"},
            {"title": "Closed Sale Date", "class": "center"},
            {"title": "Package", "class": "center"},
            {"title": "Total Amount", "class": "center"},
        ],
        "order": [[0, "asc"]],
        dom: '<"clear">rtipTf',
        scrollX: true,
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "collection_due_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "collection_due_report.csv"
                }
            ],
            "filter": "applied"

        }
    });
}


function search_collection_due_report() {
    table_initialize_collection_due_report();
    report_collection_due_report();
}


// Call Details Report


function table_initialize_call_details_report() {

    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="call_details_report" width="100%"  ><tr><td  align="center"></td></tr></table>');

}

function report_call_details_report() {

    var dataSet = [[]];
    var dataInfo = {};

    //$('#StartDate').val(return_current_date());
    //$('#EndDate').val(return_current_date());

    var date_from = $('#StartDate').val();
    var date_to = $('#EndDate').val();
    var sales_representative = $('#salesRepresentative').val();

    if (date_from.trim() != '') {
        dataInfo['date_from'] = date_from;
    }

    if (date_to.trim() != '') {
        dataInfo['date_to'] = date_to;
    }
    dataInfo['sales_representative'] = sales_representative;

    dataSet = connectServer(cms_url['doze_crm_get_call_details_report'], dataInfo);
    //  alert(dataSet);
    dataSet = JSON.parse(dataSet);
    //alert(dataSet);
    table_data_call_details_report(dataSet);
    //$("div.toolbar").html('<div style="color: #000000;"><b><h4>Call Details Report</h4></b></div>');

    /*
     $('td').css("padding-left","2%");
     $('td').css("padding-right","2%");
     $('th').css("padding-left","2%");
     $('th').css("padding-right","2%");
     */
}


function table_data_call_details_report(dataSet) {
    $('#call_details_report').dataTable({
        "data": dataSet,
        "columns": [
            {"title": "Serial", "class": "center"},
            {"title": "Retail", "class": "center"},
            {"title": "Contact", "class": "center"},
            {"title": "Date", "class": "center"},
            {"title": "Time", "class": "center"},
            {"title": "Customer Type", "class": "center"},
            {"title": "Next Call Date", "class": "center"},
            {"title": "Feedback", "class": "center"},
            {"title": "Call Type", "class": "center"},
            {"title": "Note", "class": "center"},
            {"title": "Call Duration", "class": "center"},
        ],
        //"bSort": false,
        "order": [[0, "asc"]],
        //dom: 'T<"clear">frtip',
        dom: '<"clear">rtipTf',
        'iDisplayLength': 40,
        scrollX: true,
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "call_details_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "call_details_report.csv"
                }
            ],
            "filter": "applied"

        }
    });
}


function search_call_details_report() {
    table_initialize_call_details_report();
    report_call_details_report();
}


function table_initialize_sell_collection_report() {

    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="sell_collection_report" width="100%"  ><tr><td  align="center"></td></tr></table>');

}

function report_sell_collection_report() {

    var dataSet = [[]];
    var dataInfo = {};

    //$('#StartDate').val(return_current_date());
    //$('#EndDate').val(return_current_date());

    var date_from = $('#StartDate').val();
    var date_to = $('#EndDate').val();
    var sales_representative = $('#salesRepresentative').val();

    if (date_from.trim() != '') {
        dataInfo['date_from'] = date_from;
    }

    if (date_to.trim() != '') {
        dataInfo['date_to'] = date_to;
    }
    dataInfo['sales_representative'] = sales_representative;

    dataSet = connectServer(cms_url['doze_crm_get_sell_collection_report'], dataInfo);
    //  alert(dataSet);
    dataSet = JSON.parse(dataSet);
    //alert(dataSet);
    table_data_sell_collection_report(dataSet);
    //$("div.toolbar").html('<div style="color: #000000;"><b><h4>Sell Collection Report</h4></b></div>');

    /*
     $('td').css("padding-left","2%");
     $('td').css("padding-right","2%");
     $('th').css("padding-left","2%");
     $('th').css("padding-right","2%");
     */
}


function table_data_sell_collection_report(dataSet) {
    $('#sell_collection_report').dataTable({
        "data": dataSet,
        "columns": [
            {"title": "Details", "class": "center"},
            {"title": "First Name", "class": "center"},
            {"title": "Last Name", "class": "center"},
            {"title": "Address", "class": "center"},
            {"title": "Lead Source", "class": "center"},
            {"title": "Email", "class": "center"},
            {"title": "Phone Number", "class": "center"},
            {"title": "Additional Contact", "class": "center"},
            {"title": "D.O.", "class": "center"},
            {"title": "Ticket Number", "class": "center"},
            {"title": "Ticket Entry Date", "class": "center"},
            {"title": "Total Amount", "class": "center"},
            {"title": "Collection Date", "class": "center"},
            {"title": "Collection Agent", "class": "center"},
        ],
        //"bSort": false,
        "order": [[0, "asc"]],
        //dom: 'T<"clear">frtip',
        dom: '<"clear">rtipTf',
        'iDisplayLength': 40,
        scrollX: true,
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "call_details_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "call_details_report.csv"
                }
            ],
            "filter": "applied"

        }
    });
}


function search_sell_collection_report() {
    table_initialize_sell_collection_report();
    report_sell_collection_report();
}


// call couter report

function table_initialize_call_count_report() {

    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="call_count_report" width="100%"  ><tr><td  align="center"><img src="doze_crm/img/31.gif"></td></tr></table>');

}

function report_call_count_report(type) {

    var dataSet = [[]];
    var dataInfo = {};

    var customer_type = type;//$('#customer_type').val();

    dataInfo['customer_type'] = customer_type;

    dataSet = connectServer(cms_url['doze_crm_get_call_count_report'], dataInfo);
    //  alert(dataSet);
    dataSet = JSON.parse(dataSet);
    //alert(dataSet);
    table_data_call_count_report(dataSet);
    $("div.toolbar").html('<div style="color: #000000;"><b><h4>Call Details Report</h4></b></div>');

    /*
     $('td').css("padding-left","2%");
     $('td').css("padding-right","2%");
     $('th').css("padding-left","2%");
     $('th').css("padding-right","2%");
     */
}


function table_data_call_count_report(dataSet) {
    $('#call_count_report').dataTable({
        "data": dataSet,
        "columns": [
            //{"title": "ID", "class": "center"},
            {"title": "Phone", "class": "center"},
            {"title": "Name", "class": "center"},
            {"title": "Address", "class": "center"},
            {"title": "Email", "class": "center"},
            {"title": "No of Call", "class": "center"},
            {"title": "No of Days", "class": "center"},
            {"title": "Present Status", "class": "center"},
        ],
        //"bSort": false,
        "order": [[1, "desc"]],
        //dom: 'T<"clear">frtip',
        dom: 'Tf<"toolbar">t<"bottom"rip>',
        'iDisplayLength': 30,
        scrollX: true,
        tableTools: {
            "sSwfPath": "doze_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "call_count_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "call_count_report.csv"
                }
            ],
            "filter": "applied"

        }
    });
}

function search_call_count_report(type) {
    $('a').removeClass('selectedLink');
    var ids = "#" + type + "_list";
    $(ids).addClass("selectedLink");
    table_initialize_call_count_report();
    report_call_count_report(type);
}


function report_otrs_due() {

    var dataInfo = {};
    dataInfo['time_range'] = $('#otrs_range').val();
    dataInfo['local_time'] = return_current_date();
    dataInfo['table_name'] = 'otrs_due_report_able';
    var tb;
    tb = connectServer(cms_url['otrs_due_report'], dataInfo);
    //$.ajax({
    //    type: 'POST',
    //    url: 'http://103.239.252.132/otrs/test.pl',
    //    data: { 'info': dataInfo },
    //    success: function(res) {
    //        //tb=res;
    //        alert("your ID is: " + res);
    //
    //    },
    //    error: function() {alert("did not work");}
    //});

    $('#tbl_otrs_due_report').html(tb);

    $('#otrs_due_report_able').dataTable({
        dom: 'Tf<"toolbar">t<"bottom"ri>',
        'iDisplayLength': -1,
        tableTools: {
            "aButtons": [
                {
                    "sExtends": "select",
                    "sButtonText": "Select All",
                    "fnClick": function (nButton, oConfig, oFlash) {
                        $(".checkbox").prop('checked', true);
                    }
                },
                {
                    "sExtends": "select",
                    "sButtonText": "Deselect All",
                    "fnClick": function (nButton, oConfig, oFlash) {
                        $(".checkbox").prop('checked', false);
                    }
                }
            ],
            "filter": "applied"
        }
    });

    $('td').addClass("center");
    $('th').addClass("center");

    $("div.toolbar").html('<div class="row" style="color: #000000;"><div class="col-md-3"><b><h4>OTRS Due Report</h4></b></div><div class="col-md-2"><h4><select onchange="report_otrs_due();" id="otrs_range" name="otrs_range"><option value="24">24</option><option value="36">36</option><option value="48">48</option><option value="72">72</option> </select> hours</h4></div></div>');

    $("#otrs_range option[value='" + dataInfo['time_range'] + "']").attr('selected', true);
}

// agent_wise_pipeline_report


function table_initialize_agent_wise_pipeline_report() {

    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="agent_wise_pipeline_report" width="100%"  ><tr><td  align="center"></td></tr></table>');

}


function report_agent_wise_pipeline_report() {

    //  var dataSet = [[]];
    var dataInfo = {};

    //$('#StartDate').val(return_current_date());
    //$('#EndDate').val(return_current_date());

    var date_from = $('#StartDate').val();
    var date_to = $('#EndDate').val();

    if (date_from.trim() != '') {
        dataInfo['date_from'] = date_from;
    }

    if (date_to.trim() != '') {
        dataInfo['date_to'] = date_to;
    }

    var dataSet = connectServer(cms_url['get_agent_wise_pipeline_report'], dataInfo);
      //alert(dataSet);
    try {
          //console.log(dataSet);

        dataSet = JSON.parse(dataSet);
        //alert(dataSet);
        table_data_agent_wise_pipeline_report(dataSet);
    }
    catch (exp) {
        console.log(exp);
    }

}

/**/

function table_data_agent_wise_pipeline_report(dataSet) {

    $('#agent_wise_pipeline_report').dataTable({
        "data": dataSet,
        "columns": [
            {"title": "Agent Name", "class": "center"},
            {"title": "Total Number of Calls", "class": "center"},
            {"title": "Successful Calls", "class": "center"},
            {"title": "Successful New Calls", "class": "center"},
            {"title": "Follow Up Calls", "class": "center"},
            {"title": "Interested", "class": "center"},
            {"title": "Verbally Confirmation", "class": "center"},
            {"title": "Sales", "class": "center"}
        ],
        "order": [[0, "asc"]],
        dom: '<"clear">rtipTf',
        scrollX: true,
        'iDisplayLength': 15,
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "transmission_assignment_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "transmission_assignment_report.csv"
                }
            ],
            "filter": "applied"

        }
    });
}

function search_agent_wise_pipeline_report() {
    table_initialize_agent_wise_pipeline_report();
    report_agent_wise_pipeline_report();
}

// month_wise_connection
function table_initialize_month_wise_connection() {

    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="month_wise_connection_report" width="100%"  ><tr><td  align="center"></td></tr></table>');
}

function get_dataset_month_wise_connection(){

    var dataInfo ={};
    var dataSet = connectServer(cms_url['get_month_wise_connection_data'], dataInfo);
    dataSet = JSON.parse(dataSet);

    $("#month_wise_connection_report").dataTable({
        "data": dataSet,
        "columns": [
            {"title": "Month", "class": "center"},
            {"title": "Total Connection", "class": "center"},
            {"title": "Dhaka", "class": "center"},
            {"title": "Chittagong", "class": "center"},
            {"title": "Comilla", "class": "center"},
            {"title": "Channel", "class": "center"}
        ],
        "ordering": false,
       // "order": [[0, "asc"]],
        dom: '<"clear">rtipTf',
        scrollX: true,
        'iDisplayLength': 15,
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "month_wise_connection_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "month_wise_connection_report.csv"
                },
                {
                    "sExtends": "pdf",
                    "sFileName": "month_wise_connection_report.pdf"
                }
            ],
            "filter": "applied"

        }
    });

}


// current month upto current date report
function table_initialize_current_month_connection() {

    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="current_month_connection_report" width="100%"  ><tr><td  align="center"></td></tr></table>');
}


function get_dataset_current_month_connection(){

    var dataInfo ={};
    var dataSet = connectServer(cms_url['get_current_month_connection_data'], dataInfo);
    dataSet = JSON.parse(dataSet);

    $("#current_month_connection_report").dataTable({
        "data": dataSet,
        "columns": [
            {"title": "Region/Source", "class": "center"},
            {"title": "Telesales", "class": "center"},
            {"title": "Channel", "class": "center"},
            {"title": "RomotiQ", "class": "center"},
            {"title": "Telesales(MTD)", "class": "center"},
            {"title": "Channel(MTD)", "class": "center"},
            {"title": "RomotiQ(MTD)", "class": "center"},
            {"title": "Total", "class": "center"}
        ],
        "order": [[0, "asc"]],
        dom: '<"clear">rtipTf',
        scrollX: true,
        'iDisplayLength': 15,
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "month_wise_connection_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "month_wise_connection_report.csv"
                },
                {
                    "sExtends": "pdf",
                    "sFileName": "month_wise_connection_report.pdf"
                }
            ],
            "filter": "applied"

        }
    });

}


function table_initialize_agent_wise_connection(){
    $('#reportLoader').html('<table class="table table-striped table-bordered table-hover responsive bootstrap-datatable datatable" id="agent_wise_connection_report" width="100%"  ><tr><td  align="center"></td></tr></table>');
}

function get_dataset_agent_wise_connection(){
    var dataInfo ={};
    var dataSet = connectServer(cms_url['get_agent_wise_connection_data'], dataInfo);
    dataSet = JSON.parse(dataSet);
    var rowcount = dataSet.length;
    var len = Object.keys(dataSet[0]).length;
    var months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
    var colums = [];
    for(var i=0;i<(len-3);i++){
        colums[i]= months[i];
    }
    colums[i++]="Today";
    colums[i++]="Name";
    colums[i]="SL";
    colums = colums.reverse();
    var obj = [];
    for(var j=0;j<colums.length;j++){
        obj[j]= {"title":colums[j],"class":"center"};
    }

    $("#agent_wise_connection_report").dataTable({
        "data": dataSet,
        "columns": obj,
        "ordering": false,
      //  "order": [[0, "asc"]],
        dom: '<"clear">rtipTf',
        scrollX: true,
        'iDisplayLength': rowcount,
        tableTools: {
            "sSwfPath": "ssd_crm/img/datatable/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "multi",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sFileName": "agent_wise_connection_report.xls"
                },
                {
                    "sExtends": "csv",
                    "sFileName": "agent_wise_connection_report.csv"
                },
                {
                    "sExtends": "pdf",
                    "sFileName": "agent_wise_connection_report.pdf"
                }
            ],
            "filter": "applied"

        }
    });
}

function ontest(s, t) {
    alert(s);
    alert(t);
}