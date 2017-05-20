<?php
    
require_once("../core/setting.php");
    
$start = $_GET["start"];
$end = $_GET["end"];
$num = $_GET["num"];

if($start==11 && $end==20){
    $next_link = "./ranking_all.php?start=1&end=10&num=10";
}else if($start==1 && $end==10 && $num==10){
    $next_link = "./ranking_all.php?start=1&end=10&num=3";
}else if($start==1 && $end==10 && $num==1){
    $next_link = "./ranking_kiri.php";
}else if($start==1 && $end==10 && $num<=3){
    $next_link = "./ranking_all.php?start=1&end=10&num=".($num-1);
}


?>
 
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <audio id="win" preload="auto">
        <source src="../music/delux.mp3" type="audio/mp3">
    </audio>
    <link rel="stylesheet" href="../css/origin.css">
    <link rel="stylesheet" href="../css/ranking.css">
    <link rel="stylesheet" href="../css/fontsize.css">
    <script src="../js/jquery-min.js" type="text/javascript"></script>
    <script src="../js/jquery.periodicalupdater.js" type="text/javascript"></script>
    <script type="text/javascript">

start = <?php printf($_GET["start"]); ?>;  
end = <?php printf($_GET["end"]); ?>;       
num = <?php printf($_GET["num"]); ?>; 
    
array = new Array();
mode = 0;
length = end-start+1;
data_count = 0;

currentSound = null;


$(document).ready(function(){
    
    $.ajax({
    url: './ranking_all_json.php?start='+start+'&end='+end,
    type: 'GET',
    data: {
    },
    dataType: 'json'
    })
    .done(function( data ) {
            // ...
            //挙動決定
            mode=0;
          if(start==11 && end==20){
            mode=0;
          }else if(start==1 && end==10 && num==10){
            mode=1;
          }else if(start==1 && end==10 && num<=3){
            mode=2;
          }
          
            var count=0;
            for(var i=0; i<length; i++){
                if(!data[i+1]){
                    continue;
                }
               array[i] = new Array();
               var tmp = data[i+1];
               array[i][0] = tmp["div_name"];
               array[i][1] = tmp["name"];
               array[i][2] = tmp["correct"];
               array[i][3] = tmp["sum_time"];
               array[i][4] = tmp["rank"];

                console.log(data[i+1]);
                count++;
            }
          
            data_count = count;
            console.log(array);
          

            if(mode==0){
                //全表示
            }else if(mode==1){
                //4位まで表示
            }else if(mode==2){
                //num未満まで表示
                for(var i=data_count; i>num; i--){
                    showOne(i);
                }
            }
          
    })
    .fail(function( data ) {
            // ...
            alert("json受信エラー");
    })
    .always(function( data ) {
            // ...
    });
    
    
});


function sound(){    
    currentSound = $("#win").get(0);
    currentSound.play();
}

//「スタート」押下
function show_start(){

    if(mode==0){
        setTimeout('showUP_All('+data_count+','+1+')', 600);
    }else if(mode==1){
        setTimeout('showUP_Until('+data_count+','+1+','+4+')', 600);
    }else if(mode==2){
        showOne(num);
        sound();
    }
}


//上位一つのみ未発表
function showUP(length, num) {

    
    var now = length - (num-1);
    console.log("now="+now);
    if(now<2) return;
    
    $("#row_d"+now).text(array[now-1][0]);
    $("#row_n"+now).text(array[now-1][1]);
    $("#row_c"+now).text(array[now-1][2]+"問");
    $("#row_t"+now).text(array[now-1][3]);
    $("#row_r"+now).text(array[now-1][4]);
    
    setTimeout('showUP('+length+','+(num+1)+')', 600);
}

//任意の位まで発表
function showUP_Until(length, num, until) {
    
    
    var now = length - (num-1);
    console.log("now="+now);
    
    
    $("#row_d"+now).text(array[now-1][0]);
    $("#row_n"+now).text(array[now-1][1]);
    $("#row_c"+now).text(array[now-1][2]+"問");
    $("#row_t"+now).text(array[now-1][3]);
    $("#row_r"+now).text(array[now-1][4]);
    
    if(now <= until) return;
    
    setTimeout('showUP_Until('+length+','+(num+1)+','+until+')', 600);
}

//全て発表
function showUP_All(length, num) {

    
    var now = length - (num-1);
    console.log("now="+now);
    
    $("#row_d"+now).text(array[now-1][0]);
    $("#row_n"+now).text(array[now-1][1]);
    $("#row_c"+now).text(array[now-1][2]+"問");
    $("#row_t"+now).text(array[now-1][3]);
    $("#row_r"+now).text(array[now-1][4]);
    
    if(now <= 1) return;
    
    setTimeout('showUP_All('+length+','+(num+1)+')', 500);
}

//上位１つの発表
function showTOP() {
    
    sound();

    $("#row_d"+1).text(array[0][0]);
    $("#row_n"+1).text(array[0][1]);
    $("#row_c"+1).text(array[0][2]+"問");
    $("#row_t"+1).text(array[0][3]);
    $("#row_r"+1).text(array[0][4]);
    
    bg_change(1,0);
}

//任意の１つのみ発表
function showOne(n) {
    
    $("#row_d"+n).text(array[n-1][0]);
    $("#row_n"+n).text(array[n-1][1]);
    $("#row_c"+n).text(array[n-1][2]+"問");
    $("#row_t"+n).text(array[n-1][3]);
    $("#row_r"+n).text(array[n-1][4]);
    
    if(num==n){
        bg_change(n,0);
    }
}



function bg_change(rank,s) {
    
    if(s>=8){
        $('#row'+rank).css('background-color', '#FA5858');
        return;
    }else if(s%2==0){
        $('#row'+rank).css('background-color', '#FA5858');
    }else if(s%2==1){
        $('#row'+rank).css('background-color', 'White');
    }
    setTimeout('bg_change('+rank+','+(s+1)+')', 500);
    
}        
    </script>
</head>
<body>
<div class="layerImage">
<div class="layerTransparent">
<div id="main">
<div data-role="header">
<h1><?php printf($TITLE); ?></h1>
</div>
<div id="containerRank">

<div id="r_title_box"><?php printf("総合ランキング ".$start."位〜".$end."位"); ?></div>

<div class="nameBox" id="boxHead">
<div class="rank">順位</div>
<div class="team">チーム</div>
<div class="name">氏名(敬称略)</div>
<div class="num">正解数</div>
<div class="time">回答時間</div>
<div class="clear"></div>
</div>
<?php
    for($i=1; $i<=10; $i++){
    ?>
<div class="nameBox" id="<?php printf("row".$i); ?>">
<div class="rank" id="<?php printf("row_r".$i); ?>"><?php printf($start+$i-1); ?></div>
<div class="team" id="<?php printf("row_d".$i); ?>">　</div>
<div class="name" id="<?php printf("row_n".$i); ?>">　</div>
<div class="num" id="<?php printf("row_c".$i); ?>">　</div>
<div class="time" id="<?php printf("row_t".$i); ?>">　</div>
<div class="clear"></div>
</div>
<?php
    }
    ?>
</div><!-- containerRank -->
</div><!-- main -->
<div id="footer">
<input type='button' id="start" value='スタート' onclick='show_start();' />
<a href="<?php printf($next_link); ?>">次へ</a>
</div><!-- footer -->
</div>
</div>
</body>
</html>
