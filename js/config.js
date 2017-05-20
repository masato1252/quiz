    $(document).bind("mobileinit", function(){
    	$.mobile.ajaxEnabled = false;
    	$.mobile.ajaxLinksEnabled = false; // Ajax を使用したページ遷移を無効にする
    	$.mobile.ajaxFormsEnabled = false; // Ajax を使用したフォーム遷移を無効にする
    });