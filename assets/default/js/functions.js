//window.onerror = function () {
//    return true;
//};
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function preview(that, id) {
    var file = that.files[0];
    var reader = new FileReader();
    reader.addEventListener("load", function () {
        $('#' + id).html($('<img />').css('max-width', '100%').attr('src', reader.result));
    }, false);
    if (file) {
        reader.readAsDataURL(file);
    }
}
function toggleMenuCookie() {
    var hasClass = $('.sidebar,.navbar-header,#page-wrapper').toggleClass('h').hasClass('h');
    if (hasClass)
        $.cookie('cls', 'h', {path: '/'});
    else
        $.removeCookie('cls', {path: '/'});
}
function LoadCities(that, cityId, content, url) {
    var province_id = parseInt($(that).val());
    $(cityId).html('<option value="">' + content + '</option>');
    if (province_id) {
        ajaxpost(url, {province_id}, function (result) {
            var cities = '<option value="">' + content + '</option>';
            if (result) {
                for (var id in result) {
                    var title = result[id];
                    cities += '<option value="' + id + '">' + title + '</option>';
                }
            }
            $(cityId).html(cities);
        });
    }
}
function log() {
    console.log.apply(null, arguments);
}
function toInt(val) {
    return parseInt(val) ? parseInt(val) : 0;
}
function toFloat(val) {
    return parseFloat(val) ? parseFloat(val) : 0;
}
//------------------------------------------------------------------------------
// Shopping
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
// Ajax
//------------------------------------------------------------------------------
var ajaxDoAjax = true;
function ajax(inUrl, inType, inData, inSuccess, inDataType, inError, inComplete) {
    if (ajaxDoAjax) {
        ajaxDoAjax = false;
        showloading();
        $.ajax({
            url: inUrl,
            type: inType,
            data: inData,
            dataType: inDataType ? inDataType : 'json',
            success: function () {
                ajaxDoAjax = true;
                if (typeof inSuccess === 'function') {
                    inSuccess.apply(this, arguments);
                }
            },
            error: function () {
                showmessage('خطا در ارسال اطلاعات', 'red', 'خطا');
                if (typeof inError === 'function') {
                    inError.apply(this, arguments);
                }
            },
            complete: function () {
                ajaxDoAjax = true;
                hideloading();
                if (typeof inComplete === 'function') {
                    inComplete.apply(this, arguments);
                }
            }
        });
    }
}
function ajaxpost(url, data, success, dataType, error, complete) {
    ajax(url, 'post', data, success, dataType, error, complete);
}
function ajaxget(url, data, success, dataType, error, complete) {
    ajax(url, 'get', data, success, dataType, error, complete);
}
function validResult(result) {
    var message = '';
    if (result.messages) {
        for (var i in result.messages) {
            message += result.messages[i] + '<br/>';
        }
        if (message !== '') {
            if (result.saved === true) {
                showmessage(message, 'green');
            } else {
                showmessage(message, 'red', 'خطا');
            }
        }
    }
    return result.saved === true;
}
function showmessage(message, type, title) {
    $.alert({
        title: title ? title : '',
        content: message,
        type: type,
        buttons: {
            ok: {
                text: 'باشه'
            }
        }
    });
}
function showConfirm(message, action, title, type) {
    $.confirm({
        title: title ? title : '',
        content: message,
        type: type ? type : 'blue',
        buttons: {
            ok: {text: 'بله', action},
            no: {text: 'خیر'}
        }
    });
}
function showloading() {

}
function hideloading() {

}
//------------------------------------------------------------------------------
// Form
//------------------------------------------------------------------------------
$.fn.getData = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        var n = this.name;
        var v = this.value;
        if (o[n]) {
            if (!o[n].push) {
                o[n] = [o[n]];
            }
            o[n].push(v || '');
        } else {
            o[n] = v || '';
        }
    });
    //$(this).find(':checkbox').each(function () {
    //    o[$(this).attr('name')] = $(this).prop('checked') ? 1 : 0;
    //});
    return o;
};
$.fn.getData2 = function () {
    var o = {};
    var data = this.serializeArray();
    for (var i in data) {
        var row = data[i];
        o[row.name] = row.value;
    }
    return o;
};
$.fn.disableAll = function () {
    return $(this).each(function () {
        var $items = $(this).find(':input:not(:disabled)');
        $(this).data('disabledItems', $items);
        $items.each(function () {
            $(this).prop('disabled', true);
        });
    });
};
$.fn.enableAll = function () {
    return $(this).each(function () {
        var items = $(this).data('disabledItems');
        if (typeof items === 'object') {
            $(items).each(function () {
                $(this).prop('disabled', false);
            });
        }

    });
};
//------------------------------------------------------------------------------
// Prototype
//------------------------------------------------------------------------------
String.prototype.toInt = function () {
    return toInt(this.valueOf());
};
Number.prototype.toInt = function () {
    return toInt(this.valueOf());
};
String.prototype.toFloat = function () {
    return toFloat(this.valueOf());
};
Number.prototype.toFloat = function () {
    return toFloat(this.valueOf());
};
String.prototype.replaceAll = function (search, replacement) {
    return this.valueOf().replace(new RegExp(search, 'g'), replacement);
};
//------------------------------------------------------------------------------
// on ready
//------------------------------------------------------------------------------
(function (win, doc, $) {
    $('.btn-search-panel').click(function (e) {
        e.preventDefault();
        $("[class$='-index'] [class$='-search']").slideToggle();
    });
//    $('.datePicker').persianDatepicker({format: 'YYYY/MM/DD', autoClose: true});
    $(doc).keyup(function (e) {
        if (e.ctrlKey && !e.shiftKey && e.keyCode === 37) {
            $('.sidebar,.navbar-header,#page-wrapper').removeClass('h');
            $.removeCookie('cls', {path: '/'});
        }
        else if (e.ctrlKey && !e.shiftKey && e.keyCode === 39) {
            $('.sidebar,.navbar-header,#page-wrapper').addClass('h');
            $.cookie('cls', 'h', {path: '/'});
        }
    });
    
    var date = new Date();
    var seconds = date.getSeconds();
    var minutes = date.getMinutes();
    var hours = date.getHours();
    $("#hours").html((hours < 10 ? "0" : "") + hours);
    $("#min").html((minutes < 10 ? "0" : "") + minutes);
    $("#sec").html((seconds < 10 ? "0" : "") + seconds);
    setInterval(function () {
        date = new Date();
        hours = date.getHours();
        minutes = date.getMinutes();
        seconds = date.getSeconds();
        $("#hours").html((hours < 10 ? "0" : "") + hours);
        $("#min").html((minutes < 10 ? "0" : "") + minutes);
        $("#sec").html((seconds < 10 ? "0" : "") + seconds);
    }, 1000);
})(window, document, $);
//------------------------------------------------------------------------------
// End
//------------------------------------------------------------------------------