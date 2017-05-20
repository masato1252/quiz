$(document).ready(function(){
    //回答スタートのみ活性
    if(!retry){
        //初回実施
        $('#retry_quiz').attr('disabled', true);
        $('#force_stop').attr('disabled', true);
        if(img_type==1) $('#show_explain').attr('disabled', true);
        $('#show_answer').attr('disabled', true);
    }else{
        //やり直し
        $('#start_quiz').attr('disabled', true);
        $('#force_stop').attr('disabled', true);
        if(img_type==1) $('#show_explain').attr('disabled', true);
        $('#show_answer').attr('disabled', true);
    }
                  
    if(img_type == 0){
        $(".box_tate").css("display", "none");
        $(".sel_guageBox").css("display", "none");
    }else if(img_type == 1){
        $(".select").css("display", "none");
        $(".sel_guageBoxI").css("display", "none");
        $(".sel_imgNameBoxI").css("display", "none");
    }
});


function sound(name, mode){
    
    if(mode==0){
        currentSound.pause();
    }else if(mode==1){
        if(quiz_type==2 && name=="thinking"){
            currentSound = $("#thinking_mini").get(0);
        }else{
            currentSound = $("#"+name).get(0);
        }
        currentSound.play();
    }
}

//問題やり直し
function retry_quiz(){
    
    $.ajax({
           url: './update/retry_quiz.php?quiz_id='+quiz_id,
           type: 'GET',
           dataType: 'json'
           })
    .done(function( data ) {
          
          alert("この問題を仕切り直します");
          
          $('#retry_quiz').attr('disabled', true);
          $('#start_quiz').attr('disabled', false);
          $('#start_quiz').removeAttr('disabled');
          $('#force_stop').attr('disabled', true);
          if(img_type==1) $('#show_explain').attr('disabled', true);
          $('#show_answer').attr('disabled', true);
          
          })
    .fail(function( data ) {
          // ...
          alert("【Error】\nページ更新後、もう一度「回答スタート」を押下");
          })
    .always(function( data ) {
            // ...
            });

    
}


//回答スタート
function start_quiz(){
    
    //receive_count_loop();
    
    $('#retry_quiz').attr('disabled', true);
    $('#start_quiz').attr('disabled', true);
    $('#force_stop').attr('disabled', false);
    $('#force_stop').removeAttr('disabled');
    if(img_type==1) $('#show_explain').attr('disabled', true);
    $('#show_answer').attr('disabled', true);

    //選択肢表示
    if(img_type==0){
        $(".box_tate").css("display", "block");
    }else if(img_type==1){
        $(".select").css("display", "block");
    }
   
    
    var s_date = new Date(); 
    var date = formatDate(s_date, "YYYY-MM-DD hh:mm:ss");
    
    $.ajax({
    url: './update/condition_change.php',
    type: 'POST',
    data: {
        condition: 1,
        quiz_id: quiz_id,
        date: date
    },
    dataType: 'json'
    })
    .done(function( data ) {

            sound("thinking",1);
                    
            var s_mill = s_date.getTime();
            var e_mill = s_date.setSeconds(s_date.getSeconds()+limit_time);
            var left = e_mill - s_mill;
            var a_day = 24 * 60 * 60 * 1000;
            console.log("s_mill="+s_mill);
            console.log("e_mill="+e_mill);
            console.log("left="+left);
            count = Math.floor((left % a_day) / 1000);
            $("#timer").text(count);
            $("#nokori").text("残り");
            setTimeout('countDown()', 1000);
    })
    .fail(function( data ) {
            // ...
            alert("【Error】\nページ更新後、もう一度「回答スタート」を押下");
    })
    .always(function( data ) {
            // ...
    });
    
}

function countDown(){
    
    if(count>0){
        //CountDown
        count=count-1;
        $("#timer").text(count);
        setTimeout('countDown()', 1000);
    }else{
        //TimeUp
        $("#timer").text(0);
        timeUp();
    }
}

//タイムアップ時
function timeUp(){
    
    //stop();
    sound("thinking",0);
    sound("timeup",1);
    
    $('#retry_quiz').attr('disabled', true);
    $('#start_quiz').attr('disabled', true);
    $('#force_stop').attr('disabled', true);
    if(img_type==0){
        $('#show_answer').attr('disabled', false);
        $('#show_answer').removeAttr('disabled');
    }else if(img_type==1 && img_exp_type==0){
        $('#show_explain').attr('disabled', true);
        $('#show_answer').attr('disabled', false);
        $('#show_answer').removeAttr('disabled');
    }else if(img_type==1 && img_exp_type==1){
        $('#show_explain').attr('disabled', false);
        $('#show_exlain').removeAttr('disabled');
        $('#show_answer').attr('disabled', true);
    }
    
    
    //回答数受信(一回)
    receive_count_once();
    
    var s_date = new Date()
    var date = formatDate(s_date, "YYYY-MM-DD hh:mm:ss");
    
    $.ajax({
    url: './update/condition_change.php',
    type: 'POST',
    data: {
        condition: 2,
        quiz_id: quiz_id,
        date: date
    },
    dataType: 'html'
    })
    .done(function( data ) {
            // ...
    })
    .fail(function( data ) {
            // ...
            alert("もう一度「回答スタート」を押下");
    })
    .always(function( data ) {
            // ...
    });
    
    

}


//回答数オープン
function show_count() {
    
    
    //回答数・ゲージ表示
    if(img_type==0){
        $(".sel_guageBox").css("display", "block");
    }else if(img_type==1){
        $(".sel_guageBoxI").css("display", "block");
    }
    
}



//回答数受信 per 1 sec
function　receive_count_loop(){
    
    myajax = new $.PeriodicalUpdater("./sync/tap_count.php",{
        minTimeout: 1000,
        method  :'POST',             // 'post'/'get'：リクエストメソッド
        sendData: {quiz_id: quiz_id},             // 送信データ
        maxTimeout:1000,           // 最長のリクエスト間隔(ミリ秒)
        multiplier: 1,           // リクエスト間隔の変更(2に設定の場合、レスポンス内容に変更がないときは、リクエスト間隔が2倍になっていく)
        maxCalls: 0,
        type :"json"                 // xml、json、scriptもしくはhtml (jquery.getやjquery.postのdataType)
    },
    function(data){
        //var array = JSON.parse(data);
        console.log(data);
        
        $('#sel1').html(data["1"]);
        gauge_change(1, data["1"]);
        $('#sel2').html(data["2"]);
        gauge_change(2, data["2"]);
        $('#sel3').html(data["3"]);
        gauge_change(3, data["3"]);
        $('#sel4').html(data["4"]);
        gauge_change(4, data["4"]);

    });
}

//回答数受信(一回)
function receive_count_once(){
    
    $.ajax({
           url: './sync/tap_count.php',
           type: 'POST',
           data: {
                quiz_id: quiz_id,
           },
           dataType: 'json'
           })
    .done(function( data ) {
          // ...
          console.log(data);
          
          $('#sel1').html(data["1"]);
          gauge_change(1, data["1"]);
          $('#sel2').html(data["2"]);
          gauge_change(2, data["2"]);
          $('#sel3').html(data["3"]);
          gauge_change(3, data["3"]);
          $('#sel4').html(data["4"]);
          gauge_change(4, data["4"]);
          
          })
    .fail(function( data ) {
          // ...
          })
    .always(function( data ) {
            // ...
            });
}

//画像選択肢時、画像名を表示
function show_explain(){
    
    $('#retry_quiz').attr('disabled', true);
    $('#start_quiz').attr('disabled', true);
    $('#force_stop').attr('disabled', true);
    $('#show_explain').attr('disabled', true);
    $('#show_answer').attr('disabled', false);
    $('#show_answer').removeAttr('disabled');
    
    
    $(".sel_imgNameBoxI").css("display", "block");
    
}


function answer_open_(){
    
    $.ajax({
    url: './sync/answer_valid.php?quiz_id='+quiz_id+'&type='+2,
    type: 'GET',
    data: {
    },
    dataType: 'json'
    })
    .done(function( data ) {
            // ...
        console.log(data);
        
        if(data['answer']==0){
            return;
        }else if(data['answer']==1 || data['answer']==2 || data['answer']==3 || data['answer']==4){
          
            show_count();
          
            answer = data['answer']; 
            sound("answer", 1);
            answer_open(0);
        }else{
            return;
        }
        
    })
    .fail(function( data ) {
            // ...
            alert("もう一度「アンサーチェック」を押下");
    })
    .always(function( data ) {
            // ...
    });
    

}

function answer_open(s){
    
    
    if(s==0){
        var s_date = new Date()
        var date = formatDate(s_date, "YYYY-MM-DD hh:mm:ss");
        $.ajax({
        url: './update/condition_change.php',
        type: 'POST',
        data: {
            condition: 3,
            quiz_id: quiz_id,
            date: date
        },
        dataType: 'html'
        })
        .done(function( data ) {
                // ...
        })
        .fail(function( data ) {
                // ...
        })
        .always(function( data ) {
                // ...
        });
        
    }

    if(img_type==0){
        if(s>=4){
            $('#select'+answer).css('background-color', 'Red');
            return;
        }else if(s%2==0){
            $('#select'+answer).css('background-color', 'Red');
        }else if(s%2==1){
            $('#select'+answer).css('background-color', 'White');
        }
        
    }else if(img_type==1){
        if(s>=4){
            $('#selectIMG'+answer).css('background-color', 'Red');
            return;
        }else if(s%2==0){
            $('#selectIMG'+answer).css('background-color', 'Red');
        }else if(s%2==1){
            $('#selectIMG'+answer).css('background-color', 'White');
        }
    
    }
    
    
    setTimeout('answer_open('+(s+1)+')', 500);
}

function stop() { 
    if (myajax != null && myajax != undefined) { 
        myajax.stop(); 
    } 
}

function stopA() { 
    if (myajaxA != null && myajaxA != undefined) { 
        myajaxA.stop(); 
    } 
}

function tap(num){
    
    $.ajax({
    url: './update/tap.php',
    type: 'POST',
    data: {
        user_id: user_id,
        quiz_id: quiz_id,
        answer: num
    },
    dataType: 'html'
    })
    .done(function( data ) {
            // ...
            //alert(data);
    })
    .fail(function( data ) {
            // ...
    })
    .always(function( data ) {
            // ...
    });
}

function force_stop(){
    //回答打ち切り
    count = 0;
    $('#answer').attr('disabled', false);
}



 function formatDate(date, format) {
      if (!format) format = 'YYYY-MM-DD hh:mm:ss';
  format = format.replace(/YYYY/g, date.getFullYear());
  format = format.replace(/MM/g, ('0' + (date.getMonth() + 1)).slice(-2));
  format = format.replace(/DD/g, ('0' + date.getDate()).slice(-2));
  format = format.replace(/hh/g, ('0' + date.getHours()).slice(-2));
  format = format.replace(/mm/g, ('0' + date.getMinutes()).slice(-2));
  format = format.replace(/ss/g, ('0' + date.getSeconds()).slice(-2));
  return format;
}


function gauge_change(sel, count){
  if(count >= gauge_max){
      count = gauge_max;
  }
  var per = Math.floor((count/gauge_max)*100);
  $(".gauge"+sel).css("width", per+"%");
}


function debug_func(num){
    
    
    $.ajax({
    url: './update/tap.php',
    type: 'POST',
    data: {
        user_id: 0,
        quiz_id: 0,
        answer: num
    },
    dataType: 'json'
    })
    .done(function( data ) {
            // ...

    })
    .fail(function( data ) {
            // ...

    })
    .always(function( data ) {
            // ...
    });
    
}

function make_ranking() {
    $.ajax({
    url: './process/make_ranking.php',
    type: 'GET',
    data: {
    },
    dataType: 'html'
    })
    .done(function( data ) {
            // ...
            alert("ランキングデータ作成完了");
            $('#make_rank').attr('disabled', true);
    })
    .fail(function( data ) {
            // ...

    })
    .always(function( data ) {
            // ...
    });
}
