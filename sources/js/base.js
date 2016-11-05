$(function () {

    jQuery.support.placeholder = false;
    test = document.createElement('input');
    if('placeholder' in test) jQuery.support.placeholder = true;
    if (!$.support.placeholder) {
        $('.field').find ('label').show ();
    }

    $.regex = function (valor, patt) {
        var result = patt.exec(valor);
        if (result == null) {
            return false;
        }
        return true;
    }

    $.mask.masks = $.extend($.mask.masks, {
        'car' : {
            mask: 'aaa-9999'
        }
    });

    $.number_format = function (number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    $.string2float = function(value, input, digits) {
        if (input != undefined && input == true) {
            value = value.replace(/\./g, "");
        }
        value = value.replace(/\,/g, ".");
        value = $.number_format(value, (digits != undefined ? digits : 5), ".", "");
        value = parseFloat(value);
        return value;
    }

    $.changeSelect = function (select, optgroup, option) {
        $("." + select.attr("id")).remove();
        select.parent().append('<input type="hidden" class="' + select.attr("id") + '" name="' + optgroup.attr("id") + '" value="' + option.val() + '">');
    }

    $(".msgbox").each(function() {
        $(this).modal('show');
    });

    $('input[type="text"]').each(function() {
        var alt = $(this).attr("alt");
        if (alt != undefined) {
            var valor = $(this).val();
            switch (alt) {
                case "signed-decimal":
                    var valor = $(this).val();
                    var patt = /\./g;
                    var decimal = patt.exec(valor);
                    if (decimal == null) {
                        $(this).val(valor + ".00");
                    }
                    break;
            }
        }
    }).setMask();

    $("input[type='password']").blur(function() {
        var id = "#" + $(this).attr("id");
        var id_help = id + "_help";
        $(id_help).hide();
        $(id).parent().parent().removeClass("error");
        var valor = $(id).val();
        if (valor.length > 0 && valor.length < 6) {
            $(id_help).text("Mínimo 6 caracteres!").show();
            $(id).parent().parent().addClass("error");
        }
    });

    $("input[type='checkbox']").each(function() {
        if ($(this).attr("rel") == $(this).val()) {
            $(this).click();
        }
    });

    $("input[type='radio']").each(function() {
        if ($(this).attr("rel") == $(this).val()) {
            $(this).click();
        }
    });

    $("select").each(function() {
        var select = $(this);
        var optgroup = $(this).find("optgroup");
        if (optgroup.length) {
            select.change(function() {
                var option = select.find("option:selected");
                $.changeSelect(select, option.parent(), option);
            });
            var selected = false;
            optgroup.each(function() {
                if ($(this).attr("rel").length && !selected) {
                    selected = $(this).find("option[value='" + $(this).attr("rel") + "']").eq(0);
                    selected.attr("selected", "selected");
                    $.changeSelect(select, $(this), selected);
                }
            });
            if (!selected) {
                optgroup = optgroup.eq(0);
                selected = optgroup.find("option").eq(0);
                selected.attr("selected", "selected");
                $.changeSelect(select, optgroup, selected);
            }
        } else {
            if ($(this).attr("rel").length) {
                $(this).val($(this).attr("rel"));
            }
        }
    });

    $("form").submit(function() {
        $(this).find('input[type="checkbox"]').each(function() {
            if (!$(this).is(":checked")) {
                $(this).attr("checked", "checked").val("0");
            }
        });
        $(this).find('input[type="text"]').each(function() {
            var alt = $(this).attr("alt");
            if (alt != undefined) {
                var valor = $(this).val();
                switch (alt) {
                    case "integer":
                        $(this).val(valor.replace(/\./g, ""));
                        break;
                    case "signed-decimal":
                        $(this).val(valor.replace(/\./g, "").replace(/\,/g, "."));
                        break;
                }
            }
        });
    });

});