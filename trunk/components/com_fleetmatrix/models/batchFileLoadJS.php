<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script>

var FILE_MISSING = "Missing required file";

jQuery(document).ready(function(event) {
    jQuery('.batchFileLoad-error').hide();
    jQuery('#batch-file-load-submit-button').click(submitFile);
});


function submitFile() {
    resetAllError();
    if(!validateInput()) { return; }
    
    var driverFileContent = [];
    var subFileContent = [];
    
    var readerForDriver = new FileReader();
    readerForDriver.onload = function(e) {
        driverFileContent = readerForDriver.result.match(/[^\r\n]+/g);
    };
    readerForDriver.readAsText(jQuery('input[type=file]')[0].files[0]);
    
    var readerForSub = new FileReader();
    readerForSub.onload = function(e) {
        subFileContent = readerForSub.result.match(/[^\r\n]+/g);
        console.log(driverFileContent);
        console.log(subFileContent);
        
        jQuery.ajax({
//            url: '../components/com_fleetmatrix/models/batchFileLoadSubmit.php',
            url: '../../components/com_fleetmatrix/models/batchFileLoadSubmit.php',
            type: 'post',
            data: { "driverList": driverFileContent, "subList": subFileContent},
            success: function(data, status) {
                if (data.status === 'success') {
                    console.log(data);
//                    window.location.href = "../component/fleetmatrix/?view=subscription&cmd=subscription";
                    window.location.href = "../../component/fleetmatrix/?view=subscription&cmd=subscription";
                }
                else {
                    // handle driver creation file errors
                    if (data.driverErrorMessage !== '') {
                        jQuery('#batchFileLoad-td-driver-create-error-0').text(data.driverErrorMessage);
                        jQuery('#batchFileLoad-tr-driver-create-error-0').show();
                    }
                    else if (data.driverErrorList && data.driverErrorList.length > 0) {
                        for (var i = 0; i < data.driverErrorList.length; i++) {
                            var errorModel = data.driverErrorList[i];
                            var errorToShow = 'Input: ' + errorModel.input + "--->";
                            errorToShow += 'Error: ' + errorModel.error;
                                
                            if (i === 0) {
                                jQuery('#batchFileLoad-td-driver-create-error-0').text(errorToShow);
                            }
                            else {
                                var lastvTr = jQuery('#batchFileLoad-tr-driver-create-error-' + (i - 1));
                                var c = lastvTr.clone();
                                c.prop("id", 'batchFileLoad-tr-driver-create-error-' + i);
                                c.find('td').prop("id", 'batchFileLoad-td-driver-create-error-' + i);
                                c.find('td').text(errorToShow);

                                c.insertAfter(lastvTr);
                            }
                        }
                        jQuery('.batchFileLoad-tr-driver-error').show();
                        jQuery('#fixErrorMessage').show();
                    }
                    
                    // handle sub creation file errors
                    if (data.subErrorMessage !== '') {
                        jQuery('#batchFileLoad-td-sub-create-error-0').text(data.subErrorMessage);
                        jQuery('#batchFileLoad-tr-sub-create-error-0').show();
                    }
                    else if (data.subErrorList && data.subErrorList.length > 0) {
                        for (var i = 0; i < data.subErrorList.length; i++) {
                            var errorModel = data.subErrorList[i];
                            var errorToShow = 'Input: ' + errorModel.input + "--->";
                            errorToShow += 'Error: ' + errorModel.error;
                                
                            if (i === 0) {
                                jQuery('#batchFileLoad-td-sub-create-error-0').text(errorToShow);
                            }
                            else {
                                var lastvTr = jQuery('#batchFileLoad-tr-sub-create-error-' + (i - 1));
                                var c = lastvTr.clone();
                                c.prop("id", 'batchFileLoad-tr-sub-create-error-' + i);
                                c.find('td').prop("id", 'batchFileLoad-td-sub-create-error-' + i);
                                c.find('td').text(errorToShow);

                                c.insertAfter(lastvTr);
                            }
                        }
                        jQuery('.batchFileLoad-tr-sub-error').show();
                        jQuery('#fixErrorMessage').show();
                    }
                }
            },
            error: function(data, status) {
                console.log(data);
            }
        });
    };
    
    readerForSub.readAsText(jQuery('input[type=file]')[1].files[0]);
    
}

function resetAllError() {
    // remove all driver create errors except the first one
    var driverTr = jQuery('.batchFileLoad-tr-driver-error');
    for (var i = driverTr.size() - 1; i > 0; i--) {
        driverTr[i].remove();
    }
    
    // remove all sub create errors except the first one
    var subTr = jQuery('.batchFileLoad-tr-sub-error');
    for (var i = subTr.size() - 1; i > 0; i--) {
        subTr[i].remove();
    }
    
    jQuery('.batchFileLoad-error').hide();
}

function validateInput() {
    result = true;
    if(!jQuery('input[type=file]')[0].files[0]) {
        result = false;
        jQuery('#batchFileLoad-td-driver-create-error-0').text(FILE_MISSING);
        jQuery('.batchFileLoad-tr-driver-error').show();
    } 
    
    if(!jQuery('input[type=file]')[1].files[0]) {
        result = false;
        jQuery('#batchFileLoad-td-sub-create-error-0').text(FILE_MISSING);
        jQuery('.batchFileLoad-tr-sub-error').show();
    }
    return result;
}


</script>