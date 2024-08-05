/**
* DISCLAIMER
*
* Do not edit or add to this file.
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FMM Modules
*  @copyright FME Modules 2021
*  @license   Single domain
*/

document.addEventListener('DOMContentLoaded', hideB2bModules);

$(document).on('click', '.feature_action', function(event) {
    event.preventDefault();
    var el = $(this).closest('form'),
        action_url = $(this).attr('data-action'),
        action_type = $(this).attr('data-action-type');

    // confirm before disable/uninstall module
    if ($.inArray(action_type, ['disable', 'uninstall']) !== -1) {
        if (!confirm(labels[action_type])) {
            return false;
        }
    }

    runWaitMe(el);

    var requestData = {
        type: 'POST',
        dataType: 'json',
        url: action_url,
        data: {
            ajax: 1,
            action: 'b2bFeature'
        },
        success: function(response) {
            if (response.success) {
                showSuccessMessage(response.msg);
                setTimeout(function(e) {
                    location.reload();
                }, 200);
            } else {
                showErrorMessage(response.msg);
            }
        },
        error: function(response, textStatus, textError) {
            showErrorMessage(textStatus + " : " + textError);
            el.waitMe('hide');
        },
        complete: function (data) {
            el.waitMe('hide');
        }
    }
    $.ajax(requestData);
});

function hideB2bModules() {
    var b2b_ecommerce_mods = ['b2bregistration', 'productquotation', 'restrictcustomergroup', 'restrictpaymentmethods', 'quickproducttable'];
    $('.module-item, input[name=modules]').each(function(event) {
        var opt = 1;
        var techName = $(this).val();
        if ((typeof $(this).attr('data-tech-name') !== typeof undefined && $(this).attr('data-tech-name') !== false)) {
            var techName = $(this).attr('data-tech-name');
            var opt = 2;
        }

        if ($.inArray(techName, b2b_ecommerce_mods) !== -1) {
            switch (opt) {
                case 1:
                    $(this).closest('tr').remove();
                    break;
                case 2:
                    $(this).remove();
                    break;
            }
        }
    });
}

function runWaitMe(el) {
    el.waitMe({
        waitTime: 5000,
        color: '#2eacce',
        effect: 'rotateplane',
        textPos: 'vertical',
        bg: 'rgba(255,255,255,0.7)',
        onClose: function(el) {
            console.log('closed')
        }
    });
}