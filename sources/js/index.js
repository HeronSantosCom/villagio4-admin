function getParamFromURI(name) {
    return decodeURI((RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]);
}

$(function () {

    $.menu = function() {
        var menu = getParamFromURI("m");
        menu = (menu == "null" ? "index" : (menu.length > 0 ? menu : "index"));
        return menu;
    }

    $.url = function(m) {
        m = (m == undefined ? menu : m);
        var url = "index.html?m=" + m;
        return url;
    }

//    $('.subnavbar').find('li').each(function (i) {
//        var mod = i % 3;
//        if (mod === 2) {
//            $(this).addClass ('subnavbar-open-right');
//        }
//    });

    var menu = $.menu();
    $("ul.mainnav li[rel='"+ menu +"']").addClass("active");
    $("ul.mainnav li.dropdown ul.dropdown-menu li[rel='"+ menu +"']").parent().parent().addClass("active");

    $(".btn-action-home").click(function() {
        window.location = "index.html";
        return false;
    });

    $(".btn-action-faq").click(function() {
        var question = $(this).attr("rel");
        window.location = $.url("faq") + (question != undefined && question.length > 0 ? "&q=" + question : "");
    });

    $(".btn-action-suporte").click(function() {
//        var question = $(this).attr("rel");
        window.location = $.url("atendimentos"); // + (question != undefined && question.length > 0 ? "&q=" + question : "");
    });

    $(".btn-action-cancel").click(function() {
            var this_menu = $(this).attr("menu");
        var parent = $(this).attr("parent");
        var parent_id = $(this).attr("parent_id");
        window.location = (this_menu != undefined && this_menu.length > 0 ? $.url(this_menu): $.url()) + (parent_id != undefined && parent_id.length > 0 ? "&parent=" + parent + "&parent_id=" + parent_id : "");
        return false;
    });

    $(".btn-action-new").click(function() {
            var this_menu = $(this).attr("menu");
        var parent = $(this).attr("parent");
        var parent_id = $(this).attr("parent_id");
        window.location = (this_menu != undefined && this_menu.length > 0 ? $.url(this_menu): $.url()) + "&cadastrar" + (parent_id != undefined && parent_id.length > 0 ? "&parent=" + parent + "&parent_id=" + parent_id : "");
        return false;
    });

    $(".btn-action-delete").click(function() {
        var id = $(this).attr("rel");
        if (id.length > 0) {
            var this_menu = $(this).attr("menu");
            var parent = $(this).attr("parent");
            var parent_id = $(this).attr("parent_id");
            window.location = (this_menu != undefined && this_menu.length > 0 ? $.url(this_menu): $.url()) + "&remover&id=" + id + (parent_id != undefined && parent_id.length > 0 ? "&parent=" + parent + "&parent_id=" + parent_id : "");
        }
        return false;
    });

    $(".btn-action-update").click(function() {
        var id = $(this).attr("rel");
        if (id.length > 0) {
            var this_menu = $(this).attr("menu");
            var parent = $(this).attr("parent");
            var parent_id = $(this).attr("parent_id");
            window.location = (this_menu != undefined && this_menu.length > 0 ? $.url(this_menu): $.url()) + "&editar&id=" + id + (parent_id != undefined && parent_id.length > 0 ? "&parent=" + parent + "&parent_id=" + parent_id : "");
        }
        return false;
    });

    $(".btn-action-assinar").click(function() {
        var id = $(this).attr("rel");
        if (id.length > 0) {
            window.location = $.url("assinatura") + "&assinar&id=" + id;
        }
        return false;
    });

    $(".btn-action-sublist").click(function() {
        var id = $(this).attr("rel");
        if (id.length > 0) {
            var children = $(this).attr("children");
            window.location = (children != undefined && children.length > 0 ? $.url(children) : $.url()) + "&parent=" + menu + "&parent_id=" + id;
        }
        return false;
    });

    $(".btn-action-sublist-return").click(function() {
        var parent = $(this).attr("parent");
        window.location = (parent.length > 0 ? $.url(parent) : $.url());
        return false;
    }).css("float","right");

    $("#info_motivo_bloqueado").click(function() {
        $("#BloqueioMotivo").modal('show');
    });

    $('.btn').tooltip({
        placement: "bottom"
    });

    $('.stat, .label, .badge').css("cursor","help").tooltip({
        placement: "bottom"
    });

    $('.summary-box').find("tr").css("cursor","pointer").tooltip({
        placement: "bottom"
    });

    $("form").attr("autocomplete", "off");

});