jQuery(function($) {
    jQuery.numberFormat = function(number, precision, decimal, separator) {
        precision = typeof precision === "undefined" ? 0 : precision;
        decimal = typeof decimal === "undefined" ? "." : decimal;
        separator = typeof separator === "undefined" ? "," : separator;
        number = !isNaN(parseFloat(number)) && isFinite(number) ? number : 0;
        var num = Math.round(number + "e" + precision) + "e-" + precision;
        if (isNaN(num)) {
            num = 0;
        }
        var parts = Number(num).toFixed(precision).toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, separator);
        return parts.join(decimal);
    };
    
    jQuery.redirect = function(url, data, method, target) {
        data = typeof data === "undefined" ? {} : data;
        method = typeof method === "undefined" ? "" : method;
        target = typeof target === "undefined" ? false : target;
        if (!(method in ["GET", "POST"])) {
            method = $.isEmptyObject(data) ? "GET" : "POST";
        }
        data = decodeURIComponent($.param(data));
        if (method.toUpperCase() === "GET" && url.indexOf("?") !== -1) {
            var parts = url.split("?");
            data = decodeURIComponent(parts[1]) + "&" + data;
            url = parts[0];
        }
        var form = $("<form>").attr({method: method, action: url});
        if (target) {
            form.attr("target", target);
        }
        $.each(data.split("&"), function(key, value) {
            var parts = value.split("=");
            var input = $("<input>").attr({type: "hidden", name: parts[0], value: parts[1]});
            form.append(input);
        });
        $("body").append(form);
        form.submit();
    };
    
    jQuery.fn.extend({
        appendByPrototype: function(content, name, index, func) {
            func = typeof func === "undefined" ? null : func;
            var element = $(this);
            var obj = $(content);
            if (func !== null) {
                func(obj);
            }
            var str = $(document.createElement("div")).append(obj.clone()).remove().html();
            var row = str.replace(new RegExp(name, "g"), index);
            element.append(row);
        }
    });
    
    $("[data-grid]").each(function() {
        var el = $(this);
        el.load(el.attr("data-grid"), {id: el.attr("id")});
    });
    
    $(document).on("change", "input[type=checkbox][data-choice-group]", function() {
        var group = $(this).attr("data-choice-group");
        var checked = $(this).is(":checked");
        $("input[type=checkbox][data-choice-group="+group+"]").prop("checked", false);
        if (checked) {
            $(this).prop("checked", true);
        }
    });
    
    $(document).on("click", "input[type=submit][data-confirm]", function() {
        return window.confirm($(this).attr("data-confirm"));
    });
    
    $(document).on("keydown", "input[type=text][data-bind-target], input[type=number][data-bind-target]", function() {
        var self = this;
        setTimeout(function() {
            var el = $(self);
            var value;
            switch (el.attr("data-bind-format")) {
                case undefined:
                case "plain":
                    value = el.val();
                    break;
                case "number":
                    var precision = el.attr("data-option-precision");
                    var decimal = el.attr("data-option-decimal-point");
                    var separator = el.attr("data-option-thousand-separator");
                    value = $.numberFormat(el.val(), precision, decimal, separator);
                    break;
                default:
                    value = "";
            }
            $(el.attr("data-bind-target")).text(value);
        }, 1);
    });
    
    $(document).on("focus", "input[type=text][data-pick]", function() {
        var el = $(this);
        var dateformat = "YYYY-MM-DD";
        var timeformat = "HH:mm:ss";
        var format;
        switch (el.attr("data-pick")) {
            case "datetime":
                format = dateformat + " " + timeformat;
                break;
            case "date":
                format = dateformat;
                break;
            case "time":
                format = timeformat;
                break;
            default:
                format = "";
        }
        if (format !== "") {
            el.parent().css("position", "relative");
            el.datetimepicker({
                format: format,
                ignoreReadonly: true,
                showTodayButton: true,
                showClear: true
            });
        }
    });
});
