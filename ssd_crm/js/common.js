/**
 * Created by Mazhar on 10/2/2014.
 */


/*jQuery(document).ajaxStart(function(event, xhr, settings) {
    //console.log(xhr)
    console.log("Fired!!");
    $('img.processing_img').css("display","block");
    console.log($(".processing_img").show());
}).ajaxComplete(function(event, xhr, settings) {
    //console.log(xhr)
    console.log("End!!");
    $('img.processing_img').css("display","none");
    console.log($(".processing_img").hide());
});*/


$('#task_view_modal')
    .ajaxStart(function(){
        $("#id_loading_image").hide();
        $('img.processing_img').css("display","block");
    })
    .ajaxStop(function(){
        $("#id_loading_image").hide();
        $('img.processing_img').css("display","none");
    });

/*$(document).on({
    ajaxStart: function() {
        $(".processing_img").show();
    },
    ajaxComplete: function() {
        $(".processing_img").hide();
    }
});*/

$('document').ready(function () {

    /*
     * Global verible for getting web content
     */
    CMS_CATEGORY_URL = cms_service_url['cms_service_host'] + "CMSWebService/getCMSCategoryList.php";
    CMS_CONTENT_URL = cms_service_url['cms_service_host'] + "CMSWebService/getCMSContentList.php";

    $(document).ajaxStart(function () {
        $("#id_loading_image").css("display", "block");
       // $('img.processing_img').css("display","block");

    });
    $(document).ajaxComplete(function () {
        $("#id_loading_image").css("display", "none");
       // $('img.processing_img').css("display","none");
    });

    /*
     * function initCMS
     * source : cmscore.js
     * input : a call back function
     */
    initCMS("onCMSDataReceived");

});

/*
 *  this is global variable
 */

var header_menu_html;


/*
 * Base call function
 * */
function onCMSDataReceived() {
    //load catagory
    loadCMSCategory("1", "#cmsData");

    var layout_var = new Object();
    layout_var.header_location = '.header';
    layout_var.footer_location = 'footer';
    layout_var.auth_menu_location = '.header';

    // ############ Check php Session #########
    check_user_session();


    var auth_session_data = checkSession('cms_auth');
    if (auth_session_data != null) {
        var auth_data = JSON.parse(auth_session_data);

        if (parseInt(auth_data.layoutId) > 0) {
            var layoutId = auth_data.layoutId;
            cms_service_url['get_header_footer'] = cms_service_url['cms_service_host'] + 'CMSWebService/getHeaderFooter.php?layoutid=' + layoutId;
        }
    }
    processLayout(layout_var);

    //display content
    defaultViewController();
}


/* =========================================================
 * Created by Mazhar on 10/25/2014.
 *
 * generic call ajax
 *
 * @param dataInfo can be array declare
 *  like:- var dataInfo = {}
 *         dataInfo['matha'] = 'matha';
 * ========================================================= */
function connectServer(fetchURL, dataInfo, asyncFlag) {

    var returnValue;
    if (asyncFlag == undefined) {
        asyncFlag = false;
    }

    $.ajax({
        type: 'POST',
        url: fetchURL,
        async: asyncFlag,
        data: {'info': dataInfo},
        success: function (value) {
            returnValue = value;
        },
        error: function (jqXHR, textStatus, errorThrown) {
            genericError(jqXHR, textStatus, errorThrown);
        }
    });
    return returnValue;
}


function connectServer_mod(fetchURL, dataInfo, asyncFlag) {

    var returnValue;
    if (asyncFlag == undefined) {
        asyncFlag = false;
    }

    $.ajax({
        //global:false,
        type: 'POST',
        url: fetchURL,
        async: asyncFlag,
        data: {'info': dataInfo},
        success: function (value) {
            returnValue = value;
        },
        error: function (jqXHR, textStatus, errorThrown) {
            genericError(jqXHR, textStatus, errorThrown);
        }
    });
    return returnValue;
}

/* =========================================================
 * Created by Mazhar on 10/25/2014.
 *
 * generic call ajax
 * ========================================================= */
function connectServerWithForm(fetchURL, formId) {
    var returnValue;
    var formData = new FormData(document.getElementById(formId));

    $.ajax({
        type: "POST",
        url: fetchURL,
        async: false,
        data: formData,
        success: function (value) {
            returnValue = value;
        },
        error: function (jqXHR, textStatus, errorThrown) {
            genericError(jqXHR, textStatus, errorThrown);
        },
        processData: false, // tell jQuery not to process the data
        contentType: false   // tell jQuery not to set contentType
    });
    return returnValue;
}

/* =========================================================
 * Created by Talemul on 03/29/2015.
 *
 * generic call ajax
 * ========================================================= */
function connectServerWithFileUpload(fetchURL, formId, valueIndex, duplicate, phoneValidation) {
    var returnValue;
    var formData = new FormData(document.getElementById(formId));
    //alert("ok1");
    //console.log(valueIndex);
    if (typeof valueIndex !== 'undefined') {
        //alert("ok");
        formData.append('value_index', JSON.stringify(valueIndex));
    }

    if (typeof valueIndex !== 'undefined')
        formData.append('duplicate', duplicate);
    if (typeof phoneValidation !== 'undefined')
        formData.append('phone_validation', phoneValidation);
    $.ajax({
        url: fetchURL, //server script to process data
        type: 'POST',
        success: function (data) {
            returnValue = data;

        },
        error: function (jqXHR, textStatus, errorThrown) {
            genericError(jqXHR, textStatus, errorThrown)
        },
        // Form data
        data: formData,
        //Options to tell JQuery not to process data or worry about content-type
        cache: false,
        async: false,
        contentType: false,
        processData: false
    });
    return returnValue;
}
/* =========================================================
 * Created by Talemul.
 *
 * gets formatted user local time
 * ========================================================= */
function return_local_time() {

    var currentDate = new Date();
    var day = currentDate.getDate();
    var month = currentDate.getMonth() + 1;
    var year = currentDate.getFullYear();
    var curHour = currentDate.getHours();
    var curMinute = currentDate.getMinutes();
    var curSeconds = currentDate.getSeconds();
    return year + "-" + month + "-" + day + " " + curHour + ":" + curMinute + ":" + curSeconds;
    //  2015-01-10 18:30:25
}

function return_current_date() {

    var currentDate = new Date();
    var day = currentDate.getDate();
    var month = currentDate.getMonth() + 1;
    var year = currentDate.getFullYear();
    return year + "-" + month + "-" + day;
    //  2015-01-10
}

/* =========================================================
 * Created by Talemul.
 *
 * gets user local timezone
 *
 * @return minutes
 * ========================================================= */
function local_time_zone() {
    var d = new Date();
    var n = d.getTimezoneOffset();//time zone in minute
    n = parseInt(n);
    n = n * (-1);
    return n;
}


/*============================ show message alert message==================================
 *if its a error message then call call message_alert(message,'red')
 * or call message_alert(message,'error')
 * if success call message_alert(message,'green')
 * or message_alert(message,'success')
 *============================================================================================ */

function message_alert(message, alert_type) {

    var color_type = '#009900';

    if (alert_type != undefined) {
        alert_type = alert_type.toLowerCase();
        if (alert_type == 'red' || alert_type == 'error') {
            var color_type = '#FF0000';
        }
    }

    var all_message = '<div class="panel panel-default"><h4 style="text-align: left; font-weight: bold; padding-left: .5em; color:' + color_type + ';" content_id="11">' + message + '</h4></div>';
    $('#message_display').html(all_message);
}

function message_clear() {
    $('#message_display').html('');
}

function loadLeftSideMenu() {
    //$("#left_menu_area").addClass("col-md-1");
    //$("#main_body_area").addClass("col-md-10");
    var dataInfo = {};
    dataInfo['id'] = 3;
    var response = connectServer(cms_url['get_left_menu'], dataInfo);
    $("#left_menu_area").html(response);
    $('#left_menu_area').show();

    var auth_session_data = checkSession('cms_auth');
    var auth_data = JSON.parse(auth_session_data);
    if ((auth_data.user_role).toLowerCase() != 'admin') {
        $('.forAdmin').css("display", "none");
    }


}

/*
 *
 * this function is custom date picker with properties of date change function
 * currentDate=any thing; if it set then current date should be set on field
 * functionName= on date change execute function name; this function should be called on change if it is set
 * params= function parameters
 * example: calenderDatePicker(); for only datepicker
 * example: calenderDatePicker(1); for datepicker with current date set
 * example: calenderDatePicker("test"); for datepicker with execute function name test();
 * example: calenderDatePicker("test","5,10"); for datepicker with execute function name test(a,b);
 *
 */
function calenderDatePicker(currentDate, functionName, params) {
    $('.calendarPicker').datetimepicker({
        format: 'yyyy-mm-dd hh:ii:ss',
        autoclose: 1,
        todayHighlight: 1,
    }).on('changeDate', function (e) {
        //alert('11');
        if (functionName == undefined) {
            return;
        } else {
            if (params == undefined) {
                eval(functionName + "()");
            } else {
                eval(functionName + "(" + params + ")");
            }
        }
    });
    if (currentDate == undefined || currentDate == 0) {
        return;
    } else {
        $('.calendarPicker').val(return_current_date());
    }
}

function calenderDatePickerOnlyDate(currentDate, functionName, params) {
    $('.calendarPickerDate').datepicker({
        format: "yyyy-mm-dd",
        weekStart: 2 - 1,
        todayBtn: true,
        calendarWeeks: false,
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function (e) {
        //alert('11');
        if (functionName == undefined) {
            return;
        } else {
            if (params == undefined) {
                eval(functionName + "()");
            } else {
                eval(functionName + "(" + params + ")");
            }
        }
    });
    if (currentDate == undefined || currentDate == 0) {
        return;
    } else {
        $('.calendarPickerDate').val(return_current_date());
    }


}
/*$(document).on("click", "#next_call_date", function () {
 //  alert();
 //  $('#next_call_date').datetimepicker();
 $('#next_call_date').datetimepicker({
 format: 'yyyy-mm-dd hh:ii:ss',
 autoclose: 1,
 todayHighlight: 1,
 });
 });*/

function customDropDownOption(targetID, fetchURL) {
    var innerHTMLCode = '';
    var dataInfo = {};
    try {
        if (targetID == '#stage') {
            // alert(innerHTMLCode);
            innerHTMLCode = localStorage.stage;

            if (innerHTMLCode.search("option") != -1) {
                $(targetID).append(innerHTMLCode);
            } else {
                innerHTMLCode = connectServer(fetchURL, dataInfo);
                $(targetID).append(innerHTMLCode);
            }
        } else if (targetID == '#note_id') {
            innerHTMLCode = localStorage.note_id;
            if (innerHTMLCode.search("option") != -1) {
                $(targetID).append(innerHTMLCode);
            } else {
                innerHTMLCode = connectServer(fetchURL, dataInfo);
                $(targetID).append(innerHTMLCode);
            }
        } else if (targetID == '#assign_agent') {
            innerHTMLCode = localStorage.assign_agent;
            if (innerHTMLCode.search("option") != -1) {
                $(targetID).append(innerHTMLCode);
            } else {
                innerHTMLCode = connectServer(fetchURL, dataInfo);
                $(targetID).append(innerHTMLCode);
            }
        } else if (targetID == '#package') {
            innerHTMLCode = localStorage.package;
            if (innerHTMLCode.search("option") != -1) {
                $(targetID).append(innerHTMLCode);
            } else {
                innerHTMLCode = connectServer(fetchURL, dataInfo);
                $(targetID).append(innerHTMLCode);
            }
        } else if (targetID == '#do_area') {
            innerHTMLCode = localStorage.do_area;
            if (innerHTMLCode.search("option") != -1) {
                $(targetID).append(innerHTMLCode);
            } else {
                innerHTMLCode = connectServer(fetchURL, dataInfo);
                $(targetID).append(innerHTMLCode);
            }
        } else if (targetID == '#lead_source') {
            innerHTMLCode = localStorage.lead_source;
            if (innerHTMLCode.search("option") != -1) {
                $(targetID).append(innerHTMLCode);
            } else {
                innerHTMLCode = connectServer(fetchURL, dataInfo);
                $(targetID).append(innerHTMLCode);
            }
        }else if (targetID == '#packaging') {
            innerHTMLCode = localStorage.packaging;
            if (innerHTMLCode.search("option") != -1) {
                $(targetID).append(innerHTMLCode);
            } else {
                innerHTMLCode = connectServer(fetchURL, dataInfo);
                $(targetID).append(innerHTMLCode);
            }
        }else if (targetID == '#other_service_charge') {
            innerHTMLCode = localStorage.other_service_charge;
            if (innerHTMLCode.search("option") != -1) {
                $(targetID).append(innerHTMLCode);
            } else {
                innerHTMLCode = connectServer(fetchURL, dataInfo);
                $(targetID).append(innerHTMLCode);
            }
        }else if (targetID == '#industry_seg') {
            innerHTMLCode = localStorage.industry_seg;
            if (innerHTMLCode.search("option") != -1) {
                $(targetID).append(innerHTMLCode);
            } else {
                innerHTMLCode = connectServer(fetchURL, dataInfo);
                $(targetID).append(innerHTMLCode);
            }
        }else if (targetID == '#corporate_stage') {
            innerHTMLCode = localStorage.corporate_stage;
            if (innerHTMLCode.search("option") != -1) {
                $(targetID).append(innerHTMLCode);
            } else {
                innerHTMLCode = connectServer(fetchURL, dataInfo);
                $(targetID).append(innerHTMLCode);
            }
        }else if (targetID == '#connection_type') {
            innerHTMLCode = localStorage.connection_type;
            if (innerHTMLCode.search("option") != -1) {
                $(targetID).append(innerHTMLCode);
            } else {
                innerHTMLCode = connectServer(fetchURL, dataInfo);
                $(targetID).append(innerHTMLCode);
            }
        }else {

            innerHTMLCode = connectServer(fetchURL, dataInfo);
            $(targetID).append(innerHTMLCode);
        }


    } catch (ex) {
        innerHTMLCode = connectServer(fetchURL, dataInfo);
        $(targetID).append(innerHTMLCode);
    }
    //  innerHTMLCode = JSON.stringify(innerHTMLCode);
    try {
        if (targetID == '#stage') {
            localStorage.stage = innerHTMLCode;
        } else if (targetID == '#note_id') {
            localStorage.note_id = innerHTMLCode;
        } else if (targetID == '#assign_agent') {
            localStorage.assign_agent = innerHTMLCode;
        } else if (targetID == '#package') {
            localStorage.package = innerHTMLCode;
        } else if (targetID == '#do_area') {
            localStorage.do_area = innerHTMLCode;
        } else if (targetID == '#lead_source') {
            localStorage.lead_source = innerHTMLCode;
        } else if (targetID == '#connection_type') {
            localStorage.connection_type = innerHTMLCode;
        } else if (targetID == '#corporate_stage') {
            localStorage.corporate_stage = innerHTMLCode;
        } else if (targetID == '#industry_seg') {
            localStorage.industry_seg = innerHTMLCode;
        } else if (targetID == '#other_service_charge') {
            localStorage.other_service_charge = innerHTMLCode;
        } else if (targetID == '#packaging') {
            localStorage.packaging = innerHTMLCode;
        }


    } catch (ex) {

    }
    //  console.log(innerHTMLCode + ' | ' + targetID + ' | ' + innerHTMLCode.length);


}

function clear_droupdown_data() {
    try {
        var reset_localstor = '';
        localStorage.stage = reset_localstor;
        localStorage.note_id = reset_localstor;
        localStorage.assign_agent = reset_localstor;
        localStorage.package = reset_localstor;
        localStorage.do_area = reset_localstor;
        localStorage.lead_source = reset_localstor;
        localStorage.get_lead_source_name = reset_localstor;

    } catch (ex) {

    }
}
function doze_DateTimePicker(input_id) {

    input_id = '#' + input_id;
    $(input_id).datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        keyboardNavigation: false,
        autoclose: true,
        todayHighlight: true
    });

}