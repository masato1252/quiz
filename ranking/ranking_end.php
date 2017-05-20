<?php
    
require_once("../core/connect_db.inc");
require_once("../core/setting.php");

try{
        $db = getDB();
  
        //正解取得
        $stt = $db->prepare('SELECT user_id FROM quiz_ranking WHERE answered > 0;');
        $stt->execute();
        $count = $stt->rowCount();
 
}catch(PDOException $e){
    die("接続エラー:".$e);
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="../css/origin.css">
    <link rel="stylesheet" href="../css/ranking.css">
    <link rel="stylesheet" href="../css/fontsize.css">
    <script src="../js/jquery-min.js" type="text/javascript"></script>
    <script src="../js/jquery.periodicalupdater.js" type="text/javascript"></script>
    <script type="text/javascript">

   
    
array = new Array();

data_count = 0;

tmp_rank = 0;
tmp_correct = 0;
same_flag = false;

$(document).ready(function(){
    
    $.ajax({
    url: './ranking_end_json.php',
    type: 'GET',
    data: {
    },
    dataType: 'json'
    })
    .done(function( data ) {
            // ...
            //挙動決定
            
            data_count = 0;
            for(var tmp in data){
                data_count++;
            }
            
            var count=0;
            for(var i=0; i<data_count; i++){
                if(!data[i]){
                    continue;
                }
               array[i] = new Array();
               var tmp = data[i];
               array[i][0] = tmp["div_name"];
               array[i][1] = tmp["name"];
               array[i][2] = tmp["correct"];
               if(tmp["sum_time"]=="00:00.000"){
                   if(tmp_correct != tmp["correct"]) same_flag = false;
                   if(!same_flag){
                        tmp_rank = (i+1);
                        tmp_correct = tmp["correct"];
                        same_flag = true;
                    }
                    array[i][3] = "早押し未正解";
                    array[i][4] = tmp_rank;
               }else{
                   if(same_flag){
                       same_flag = false;
                   }
                    array[i][3] = tmp["sum_time"];
                    array[i][4] = (i+1);
               }
               console.log(data[i+1]);
               count++;
            }
            
            data_count = count;
            console.log(array);
            
            
            //setTimeout('showUP('+count+','+1+')', 600);
    })
    .fail(function( data ) {
            // ...
            alert("【Error】\nページ更新後、もう一度「回答スタート」を押下");
    })
    .always(function( data ) {
            // ...
    });
    
    
});

function show_start(){

     setTimeout('showUP('+data_count+','+1+')', 400);
}

function showOne(n) {

    $("#row_d"+n).text(array[n-1][0]);
    $("#row_n"+n).text(array[n-1][1]);
    $("#row_c"+n).text(array[n-1][2]);
    $("#row_t"+n).text(array[n-1][3]);
    $("#row_r"+n).text(array[n-1][4]);
    
    if(num==n){
        bg_change(n,0);
    }
}

function showUP(length, num) {

    
    var now = length - (num-1);
    console.log("now="+now);
    
    var row = (now % 10);
    if(row==0){
        row = 10;
    }

    $("#row_d"+row).text(array[now-1][0]);
    $("#row_n"+row).text(array[now-1][1]);
    $("#row_c"+row).text(array[now-1][2]);
    $("#row_t"+row).text(array[now-1][3]);
    $("#row_r"+row).text(array[now-1][4]);
    bg_change(row);
    if(now<=1) return;
    setTimeout('showUP('+length+','+(num+1)+')', 400);
}


function bg_change(rank) {
    
    $('#row'+rank).css('background-color', '#FFEBCD');
    if(rank==10){
        $('#row1').css('background-color', 'White');
    }else{
        $('#row'+(rank+1)).css('background-color', 'White');
    }
    
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

<div id="r_title_box"><?php printf("総合ランキング（全て）"); ?></div>

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
<div class="rank" id="<?php printf("row_r".$i); ?>">　</div>
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
</div><!-- main --><div id="footer">
<input type='button' id="start" value='スタート' onclick='show_start();'>  
</div><!-- footer -->
</div>
</div>
</body>
</html>
