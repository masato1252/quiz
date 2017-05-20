<?php

require_once("./core/connect_db.inc");
require_once("./core/setting.php");

$quiz_id = $_GET['quiz_id'];
if($quiz_id == 0){
    printf("Invalid Access!");
    exit();
}
$path = "./img/";

try{
        $db = getDB();
    
        //実施の実績取得
        $stt = $db->prepare('SELECT state FROM ta_condition WHERE quiz=:quiz AND state=:state LIMIT 1;');
        $stt->bindValue(':quiz', $quiz_id);
        $stt->bindValue(':state', 1);
        $stt->execute();
        $check = $stt->rowCount();
    
        //総問題数取得
        $stt = $db->prepare('SELECT quiz_id FROM ta_quiz_data ORDER BY quiz_id DESC LIMIT 1;');
        $stt->execute();
        $tmp = $stt->fetch();
        $last_quiz_id = $tmp["quiz_id"];

        $stt = $db->prepare('INSERT INTO ta_condition(state, quiz, date) VALUES (:state, :quiz, now());');
        $stt->bindValue(':state', 0);
        $stt->bindValue(':quiz', $quiz_id);
        $stt->execute();
    
        $stt = $db->prepare('SELECT * FROM ta_quiz_data WHERE quiz_id=:quiz_id LIMIT 1;');
        $stt->bindValue(':quiz_id', $quiz_id);
        $stt->execute();
        $row = $stt->fetch();
    
        if($row["img_type"]==1){
            //選択肢が画像の場合
            $stt = $db->prepare('SELECT * FROM ta_quiz_img WHERE quiz_id=:quiz_id ORDER BY num ASC;');
            $stt->bindValue(':quiz_id', $quiz_id);
            $stt->execute();
            $c=1;
            while($tmp = $stt->fetch()){
                
                $img_exp[$c] = $tmp["exp"];
                
                $stt2 = $db->prepare('SELECT file_name FROM ta_img_table WHERE img_name=:img_name;');
                $stt2->bindValue(':img_name', $tmp["img_name"]);
                $stt2->execute();
                $tmp2 = $stt2->fetch();
                $img_path[$c] = $path.$tmp2["file_name"];
                $c++;
            }
        }
    
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }
    
    //次へのリンク
    if($last_quiz_id == $quiz_id){
        $next_link = "./ranking/ranking_all.php?start=11&end=20&num=0";
    }else if($row["goods"]==1){
        $next_link = "./ranking/ranking_each.php?quiz_id=".$quiz_id;
    }else{
        $next_link = "./main.php?quiz_id=".($quiz_id+1);
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
    <title><?php printf($TITLE); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Cache-Control" content="no-cache"/>
    <audio id="answer" preload="auto">
        <source src="./music/answer.mp3" type="audio/mp3">
    </audio>
    <audio id="thinking" preload="auto" loop>
        <source src="./music/thinking.mp3" type="audio/mp3">
    </audio>
    <audio id="thinking_mini" preload="auto" loop>
        <source src="./music/thinking_mini.mp3" type="audio/mp3">
    </audio>
    <audio id="timeup" preload="auto">
        <source src="./music/timeup.mp3" type="audio/mp3">
    </audio>
    <link rel="stylesheet" href="./css/origin.css">
    <link rel="stylesheet" href="./css/show_img.css">
    <script src="./js/jquery-min.js" type="text/javascript"></script>
    <script src="./js/jquery.periodicalupdater.js" type="text/javascript"></script>
    <!-- <script src="./js/footerFixed.js" type="text/javascript"></script> -->
    <script type="text/javascript">

quiz_id = <?php printf($quiz_id); ?>;   
answer = <?php printf($row['answer']); ?>;

quiz_type = <?php printf($row['quiz_type']); ?>;
img_type = <?php printf($row['img_type']); ?>;
img_exp_type = <?php if($img_exp[1]!=""){ printf(1); }else{ printf(0); } ?>;

limit_time = <?php printf($row['limit_time']); ?>;



//実施履歴の有無
retry = <?php if($check>0){ printf("true"); }else{ printf("false"); } ?>;

gauge_max = 150;

count = 0;

currentSound = null;

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

</script>
</head>
<body>
<?php
    if($row["img_type"]==0){
?>
<div class="layerImage">
<div class="layerTransparent">
    <div id="container">

        <div id="top">
            <div id="title_box">
                <table>
                    <tr>
                        <td id="title"><?php printf($row['quiz_title']); ?></td>
                        <td><div id="question"><?php printf(nl2br($row['question'])); ?></div></td>
                    </tr>
                </table>

</div>
<div id="timer_box">
<div id="nokori">　</div>
<div id="timer">　</div>
</div>
</div>
<div class="clear"></div>

<!-- <div id="notice" style="display:none;">正解を予想して、投票してください</div> -->
<br>
<div class="box_tate" style="display: none;">
<div class="select" id="select1">
<div class="sel_all">
<div class="sel_numBox" id="sel_numBox1">
１
</div><!-- sel_numBox -->
<div class="sel_nameBox" id="sel_nameBox1">
<?php printf($row['select1']); ?>
</div><!-- sel_nameBox -->

<div class="sel_guageBox" id="sel_guageBox1">
<table width="300px">
    <tr>
        <td class="gaugeLabel">回答者数</td>
    </tr>
    <tr>
        <td height="30px">
            <div class="gauge-wrapper">
                <div class="gauge-type1 gauge-inner gauge1"><div id="sel1">0</div></div>
            </div>
        </td>
    </tr>
</table>
</div><!-- sel_guageBox -->

<div class="clear"></div>
</div><!-- sel_all -->
</div><!-- select1 -->

<div class="select" id="select2">
<div class="sel_all">
<div class="sel_numBox" id="sel_numBox2">
２
</div>
<div class="sel_nameBox" id="sel_nameBox2">
<?php printf($row['select2']); ?>
</div>

<div class="sel_guageBox" id="sel_guageBox2">
<table width="300px">
<tr>
<td class="gaugeLabel">回答者数</td>
</tr>
<tr>
<td height="30px">
<div class="gauge-wrapper">
<div class="gauge-type1 gauge-inner gauge2"><div id="sel2">0</div></div>
</div>
</td>
</tr>
</table>

</div>

<div class="clear"></div>
</div>
</div>

<div class="select" id="select3">
<div class="sel_all">
<div class="sel_numBox" id="sel_numBox3">
３
</div><!-- sel_numBox -->
<div class="sel_nameBox" id="sel_nameBox3">
<?php printf($row['select3']); ?>
</div><!-- sel_nameBox -->

<div class="sel_guageBox" id="sel_guageBox3">
<table width="300px">
<tr>
<td class="gaugeLabel">回答者数</td>
</tr>
<tr>
<td height="30px">
<div class="gauge-wrapper">
<div class="gauge-type1 gauge-inner gauge3"><div id="sel3">0</div></div>
</div>
</td>
</tr>
</table>
</div><!-- sel_guageBox -->
<div class="clear"></div>
</div><!-- sel_all -->
</div><!-- select3 -->

<div class="select" id="select4">
<div class="sel_all">
<div class="sel_numBox" id="sel_numBox4">
４
</div><!-- sel_numBox -->
<div class="sel_nameBox" id="sel_nameBox4">
<?php printf($row['select4']); ?>
</div><!-- sel_nameBox -->

<div class="sel_guageBox" id="sel_guageBox4">
<table width="300px">
<tr>
<td class="gaugeLabel">回答者数</td>
</tr>
<tr>
<td height="30px">
<div class="gauge-wrapper">
<div class="gauge-type1 gauge-inner gauge4"><div id="sel4">0</div></div>
</div>
</td>
</tr>
</table>
</div><!-- sel_guageBox -->
<div class="clear"></div>
</div><!-- sel_all -->
</div><!-- select4 -->

</div><!-- container -->
</div><!-- main -->
<div id="footer">
<input type='button' id="retry_quiz" value='問題やり直し' onclick='retry_quiz();'><?php printf(" "); ?>
<input type='button' id="start_quiz" value='回答スタート' onclick='start_quiz();'><?php printf(" "); ?>
<input type='button' id="force_stop" value='回答打ち切り' onclick='force_stop();'><?php printf(" "); ?>
<input type='button' id="show_answer" value='アンサーチェック' onclick='answer_open_();'><?php printf(" "); ?>
<a href="<?php printf($next_link); ?>">次へ</a><?php printf(" "); ?>
　(for debug)
<?php if($last_quiz_id == $quiz_id){ ?>
<input type='button' id="make_rank" value='総合RANKデータ作成' onclick='make_ranking();'><?php printf(" "); ?>
<?php } ?>
<a href=./ranking/ranking_all.php?start=11&end=20&num=0>ランキングへ</a><?php printf(" "); ?>
<a href=./main.php?quiz_id=<?php printf($quiz_id-1); ?>>前問題</a>／
<a href=./main.php?quiz_id=<?php printf($quiz_id+1); ?>>次問題</a>
</div><!-- footer -->
</div>
</div>

<?php
    }else if($row["img_type"]==1){
?>

<div class="layerImage">
<div class="layerTransparent">
<div id="main">
<div id="container">

<div id="top">
<div id="title_box">
<table>
<tr>
<td id="title"><?php printf($row['quiz_title']); ?></td>
<td><div id="question"><?php printf(nl2br($row['question'])); ?></div></td>
</tr>
</table>

</div>
<div id="timer_box">
<div id="nokori">　</div>
<div id="timer">　</div>
</div>
</div>
<div class="clear"></div>

<div class="select" id="selectIMG1">
    <div class="sel_numBoxI" id="sel_numBox1">
        １
    </div><!-- sel_numBox1 -->
    <div class="sel_guageBoxI" id="sel1">
        0
    </div><!-- sel1 -->
    <div class="sel_imgNameBoxI" id="sel_inBox1">
        <?php printf($img_exp[1]); ?>
    </div><!-- sel_inBox1 -->
    <img class="select_img" src="<?php printf($img_path[1]); ?>">
</div><!-- select1 -->

<div class="select" id="selectIMG2">
    <div class="sel_numBoxI" id="sel_numBox2">
        ２
    </div><!-- sel_numBox2 -->
    <div class="sel_guageBoxI" id="sel2">
        0
    </div><!-- sel2 -->
    <div class="sel_imgNameBoxI" id="sel_inBox2">
        <?php printf($img_exp[2]); ?>
    </div><!-- sel_inBox2 -->
    <img class="select_img" src="<?php printf($img_path[2]); ?>">
</div><!-- select2 -->

<div class="clear"></div>

<div class="select" id="selectIMG3">
    <div class="sel_numBoxI" id="sel_numBox3">
        ３
    </div><!-- sel_numBox3 -->
    <div class="sel_guageBoxI" id="sel3">
        0
    </div><!-- sel3 -->
    <div class="sel_imgNameBoxI" id="sel_inBox3">
        <?php printf($img_exp[3]); ?>
    </div><!-- sel_inBox3 -->
    <img class="select_img" src="<?php printf($img_path[3]); ?>">
</div><!-- select3 -->

<div class="select" id="selectIMG4">
    <div class="sel_numBoxI" id="sel_numBox4">
        ４
    </div><!-- sel_numBox4 -->
    <div class="sel_guageBoxI" id="sel4">
        0
    </div><!-- sel4 -->
    <div class="sel_imgNameBoxI" id="sel_inBox4">
　       <?php printf($img_exp[4]); ?>
    </div><!-- sel_inBox4 -->
    <img class="select_img" src="<?php printf($img_path[4]); ?>">
</div><!-- select4 -->

</div><!-- container -->
</div><!-- main -->
<div id="footer">
<input type='button' id="retry_quiz" value='問題やり直し' onclick='retry_quiz();'><?php printf(" "); ?>
<input type='button' id="start_quiz" value='回答スタート' onclick='start_quiz();'><?php printf(" "); ?>
<input type='button' id="force_stop" value='回答打ち切り' onclick='force_stop();'><?php printf(" "); ?>
<input type='button' id="show_explain" value='画像名表示' onclick='show_explain();'><?php printf(" "); ?>
<input type='button' id="show_answer" value='アンサーチェック' onclick='answer_open_();'><?php printf(" "); ?>
<a href="<?php printf($next_link); ?>">次へ</a><?php printf(" "); ?>
　(for debug)
<?php if($last_quiz_id == $quiz_id){ ?>
<input type='button' id="make_rank" value='総合RANKデータ作成' onclick='make_ranking();'><?php printf(" "); ?>
<?php } ?>
<a href=./ranking/ranking_all.php?start=11&end=20&num=0>ランキングへ</a><?php printf(" "); ?>
<a href=./main.php?quiz_id=<?php printf($quiz_id-1); ?>>前問題</a>／
<a href=./main.php?quiz_id=<?php printf($quiz_id+1); ?>>次問題</a>
</div><!-- footer -->
</div><!-- layerTransparent -->
</div><!-- layerImage -->

<?php
    }
?>
</body>
</html>
