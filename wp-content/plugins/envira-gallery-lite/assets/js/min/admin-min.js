jQuery(document).ready(function($){if("undefined"!=typeof Clipboard){var i=new Clipboard(".envira-clipboard");$(document).on("click",".envira-clipboard",function(i){i.preventDefault()})}$("div.envira-notice").on("click",".notice-dismiss",function(i){i.preventDefault(),$(this).closest("div.envira-notice").fadeOut(),$(this).hasClass("is-dismissible")&&$.post(envira_gallery_admin.ajax,{action:"envira_gallery_ajax_dismiss_notice",nonce:envira_gallery_admin.dismiss_notice_nonce,notice:$(this).parent().data("notice")},function(i){},"json")})});