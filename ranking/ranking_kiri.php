<?php

    require_once("../core/connect_db.inc");
    require_once("../core/setting.php");
    
    $next_link = "./ranking_team.php";
    
    try{
        $db = getDB();
        
        $stt = $db->prepare('SELECT rank FROM ta_rank_kiri ORDER BY rank ASC;');
        $stt->execute();
        $dataCount = $stt->rowCount();
        $c=0;
        while($row = $stt->fetch()){
            $rank[$c] = $row["rank"];
            $c++;
        }
        
    }catch(PDOException $e){
        die("接続エラー:".$e);
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

array = new Array();
mode = 0;
length = <?php printf($dataCount); ?>;
data_count = 0;

currentSound = null;


$(document).ready(function(){
                  
                  $.ajax({
                         url: './ranking_kiri_json.php',
                         type: 'GET',
                         data: {
                         },
                         dataType: 'json'
                         })
                  .done(function( data ) {
                        // ...
                        
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

    sound();
    
    for(var i=1; i<=length; i++){
        showOne(i);
        bg_change(i,0);
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
    
}



function bg_change(rank,s) {
    
    if(s>=8){
        $('#row'+rank).css('background-color', 'White');
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

<div id="r_title_box"><?php printf("総合ランキング（キリ番）"); ?></div><br><br>

<div class="nameBox" id="boxHead">
<div class="rank">順位</div>
<div class="team">チーム</div>
<div class="name">氏名(敬称略)</div>
<div class="num">正解数</div>
<div class="time">回答時間</div>
<div class="clear"></div>
</div>
<?php
    for($i=1; $i<=$dataCount; $i++){
        ?>
<div class="nameBox" id="<?php printf("row".$i); ?>">
<div class="rank" id="<?php printf("row_r".$i); ?>"><?php printf($rank[$i-1]); ?></div>
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
</div></div>
</body>
</html>
