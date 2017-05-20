$(document).ready(function(){
    
    ctr =  "<table><tr><td><input type='button' id='sel1' class='ui-btn ui-corner-all' value='　　①　　' onclick='tap(1);'></td>\
    <td><input type='button' id='sel2' value='　　②　　' onclick='tap(2);'></td></tr>\
    <tr><td><input type='button' id='sel3' value='　　③　　' onclick='tap(3);'></td>\
    <td><input type='button' id='sel4' value='　　④　　' onclick='tap(4);'></td></tr></table>\
    <br><br><br><br><input type='button' value='リセット' onclick='tap(-1);'>";
    
    user_id = <?php printf($_GET['user_id']); ?>;
    quiz_id=0;
    select=0;
    count=0;
    
    //回答中の問い合わせ間隔をランダム決定
    fp = (Math.floor(Math.random () * 10) + 1)%2;
    if(fp==0){
        force_int = 2000;
    }else if(fp==1){
        force_int = 3000;
    }
    
    myajax = new $.PeriodicalUpdater("./sync/condition.php?user_id="+user_id,{
        minTimeout: 1000,
        method  :'GET',             // 'post'/'get'：リクエストメソッド
        //sendData: {user_id: user_id},             // 送信データ
        maxTimeout:1000,           // 最長のリクエスト間隔(ミリ秒)
        multiplier: 1,           // リクエスト間隔の変更(2に設定の場合、レスポンス内容に変更がないときは、リクエスト間隔が2倍になっていく)
        maxCalls: 0,
        type :"json"                 // xml、json、scriptもしくはhtml (jquery.getやjquery.postのdataType)
    },
    function(data){
        //var array = JSON.parse(data);
        console.log(data);
        if(data["state"]==1){
            $('#condition').html("回答スタート！");
            //$('#ctr').html(ctr);
            $("#ctr").css("display", "block");
            quiz_id = data["quiz"];
            stop();
            
            forceCheck();
            
            setQuizInfo(data["question"], data["quiz_title"], data["select1"], data["select2"], data["select3"], data["select4"]);
            
            countDown_first(data["date"], data["limit_time"], data["now_date"]);
            
        }else if(data["state"]==2){
            $('#condition').html("回答時間切れです");
            $("#ctr").css("display", "block");
            btn_close();
            stop();
            
            setQuizInfo(data["question"], data["quiz_title"], data["select1"], data["select2"], data["select3"], data["select4"]);
            timeUp();
        }else if(data["state"]==100){
            //途中参加かつ回答済み
            $('#condition').html("回答を受け付けました");
            //$('#ctr').html(ctr);
            $("#ctr").css("display", "block");
            btn_close();
            stop();
            
            setQuizInfo(data["question"], data["quiz_title"], data["select1"], data["select2"], data["select3"], data["select4"]);
            
            countDown_first(data["date"], data["limit_time"], data["now_date"]);
        }else if(data["state"]==200){
            //途中参加かつ回答済みかつ回答時間終了時
            $('#condition').html("回答を受け付けました");
            $("#ctr").css("display", "block");
            btn_close();
            stop();
            
            setQuizInfo(data["question"], data["quiz_title"], data["select1"], data["select2"], data["select3"], data["select4"]);
            timeUp();
        }
    });
});


function setQuizInfo(question, quiz_title, sel1, sel2, sel3, sel4){
    
    $('#quiz_title').text(quiz_title);
    $('#question').html(nl2br(question));
    
    $('#sel1').val('① '+sel1);
    $('#sel2').val('② '+sel2);
    $('#sel3').val('③ '+sel3);
    $('#sel4').val('④ '+sel4);
    
    $("#sel1").button("refresh");
    $("#sel2").button("refresh");
    $("#sel3").button("refresh");
    $("#sel4").button("refresh");

}

function countDown_first(date, limit_time, now_date){
    
    var arr = date.split(" ");
    var arr_ = arr[0].split("-");
    var y = arr_[0];
    var mo = arr_[1];
    var d = arr_[2];
    
    var arr2 = arr[1].split(":");
    var h = arr2[0];
    var m = arr2[1];
    var s = arr2[2];
    
    var s_date = new Date(y,mo,d,h,m,s);
    
    var arr = now_date.split(" ");
    var arr_ = arr[0].split("-");
    var y = arr_[0];
    var mo = arr_[1];
    var d = arr_[2];
    
    var arr2 = arr[1].split(":");
    var h = arr2[0];
    var m = arr2[1];
    var s = arr2[2];
    
    var n_date = new Date(y,mo,d,h,m,s);
    
    console.log(y+mo+d+" "+h+m+s);
    

    var e_mill = s_date.getTime() + (limit_time*1000);
    var s_mill = n_date.getTime();

    console.log("limit_time="+limit_time);
    console.log("now_date="+now_date);
    console.log("date="+date);
    //var s_mill = new Date();
    var left = e_mill - s_mill;
    var a_day = 24 * 60 * 60 * 1000;
    console.log("s_mill="+s_mill);
    console.log("e_mill="+e_mill);
    console.log("left="+left);
    count = Math.floor((left % a_day) / 1000);
    $("#timer").text(count);
    setTimeout('countDown()', 1000);
    
}

function countDown(){
    
    if(count>0){
        //CountDown
        count=count-1;
        $("#timer").text(count);
        setTimeout('countDown()', 1000);
    }else{
        //TimeUp
        timeUp();
    }
}

//回答時間中にたまにチェック
function forceCheck(){
    
    myajax3 = new $.PeriodicalUpdater("./sync/force_stop.php?user_id="+user_id+"&quiz_id="+quiz_id,{
        minTimeout: force_int,
        method  :'GET',             // 'post'/'get'：リクエストメソッド
        //sendData: {user_id: user_id},             // 送信データ
        maxTimeout: 5000,           // 最長のリクエスト間隔(ミリ秒)
        multiplier: 1,           // リクエスト間隔の変更(2に設定の場合、レスポンス内容に変更がないときは、リクエスト間隔が2倍になっていく)
        maxCalls: 0,
        type :"json"                 // xml、json、scriptもしくはhtml (jquery.getやjquery.postのdataType)
    },
    function(data){
        //var array = JSON.parse(data);
        console.log(data);
        if(data["state"]==1){
            //回答時間中 OK
        }else if(data["state"]==2 || data["state"]==3){
            //結果発表開示時
            stopAjax3();
            count=0;
        }else if(data["state"]==0 || data["state"]==-1){
            location.reload();
        }
    });
}


function timeUp(){
     $('#condition').text("");
     $("#timer").text("タイムアップ！");
     
    myajax2 = new $.PeriodicalUpdater("./sync/answer_check.php?user_id="+user_id,{
        minTimeout: 2000,
        method  :'GET',             // 'post'/'get'：リクエストメソッド
        //sendData: {user_id: user_id},             // 送信データ
        maxTimeout:2000,           // 最長のリクエスト間隔(ミリ秒)
        multiplier: 1,           // リクエスト間隔の変更(2に設定の場合、レスポンス内容に変更がないときは、リクエスト間隔が2倍になっていく)
        maxCalls: 0,
        type :"json"                 // xml、json、scriptもしくはhtml (jquery.getやjquery.postのdataType)
    },
    function(data){
        //var array = JSON.parse(data);
        console.log(data);
        if(data["state"]==0){
            //正解準備中
        }else if(data["state"]==1){
            //結果発表開示時
            $('#ctr').html("");
            $('#timer').html("");
            
            if(data["result"]==1){
                $('#condition').html("正解！おめでとうございます");
                $('#navi').html("<a href=./quiz_exe.php?user_id="+user_id+" class='ui-btn'  data-ajax='false'>次へ進む</a>");
            }else{
                $('#condition').html("残念、不正解です…");
                $('#navi').html("<a href=./quiz_exe.php?user_id="+user_id+" class='ui-btn'  data-ajax='false'>次へ進む</a>");
            }

            stopAjax2();
        }
    });
}


function stop() { 
    if (myajax != null && myajax != undefined) { 
        myajax.stop(); 
    }
}

function stopAjax2() { 
    if (myajax2 != null && myajax2 != undefined) { 
        myajax2.stop(); 
    } 
}

function stopAjax3() { 
    if (myajax3 != null && myajax3 != undefined) { 
        myajax3.stop(); 
    } 
}

function tap(num){
    
    btn_close();
    
    $.ajax({
    url: './update/tap.php',
    type: 'POST',
    data: {
        user_id: user_id,
        quiz_id: quiz_id,
        answer: num
    },
    dataType: 'json'
    })
    .done(function( data ) {
            // ...
            //alert(data);
        if(data["result"]!=1){
            alert("回答時間外です");
        }else{
            $('#condition').html("回答を受け付けました<br>");
            select = num;
        }
    })
    .fail(function( data ) {
            // ...
       btn_open();

    })
    .always(function( data ) {
            // ...
    });
}

function btn_open(){

    $('#sel1').button('enable');
    $('#sel2').button('enable');
    $('#sel3').button('enable');
    $('#sel4').button('enable');
}

function btn_close(){

    $('#sel1').button('disable');
    $('#sel2').button('disable');
    $('#sel3').button('disable');
    $('#sel4').button('disable');
}

function nl2br(str) {
    return str.replace(/[\n\r]/g, "<br />");
}
