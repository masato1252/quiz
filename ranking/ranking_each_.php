<?php

    require_once("../core/setting.php");
    $quiz_id = $_GET["quiz_id"];

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <audio id="win" preload="auto">
        <source src="../music/delux.mp3" type="audio/mp3">
    </audio>
    <link rel="stylesheet" href="../css/origin.css">
    <link rel="stylesheet" href="../css/ranking_each.css">
    <link rel="stylesheet" href="../css/fontsize.css">
    <script src="../js/jquery-min.js" type="text/javascript"></script>
    <script src="../js/jquery.periodicalupdater.js" type="text/javascript"></script>
    <script type="text/javascript">

quiz_id = <?php printf($_GET["quiz_id"]); ?>        
array = new Array();
length = 10;

currentSound = null;

function sound(){    
    currentSound = $("#win").get(0);
    currentSound.play();
}

function show_start(){

    $.ajax({
    url: './ranking_each_json.php?quiz_id='+quiz_id,
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
               array[i][2] = tmp["diff"];
               console.log(data[i+1]);
               count++;
            }
            
            console.log(array);

            setTimeout('showUP('+count+','+1+')', 600);
    })
    .fail(function( data ) {
            // ...
            alert("【Error】\nページ更新後、もう一度「回答スタート」を押下");
    })
    .always(function( data ) {
            // ...
    });
    
}

function showUP(length, num) {

    
    var now = length - (num-1);
    console.log("now="+now);
    if(now<1) return;
    
    $("#row_d"+now).text(array[now-1][0]);
    $("#row_n"+now).text(array[now-1][1]);
    $("#row_t"+now).text(array[now-1][2]);
    setTimeout('showUP('+length+','+(num+1)+')', 600);
}
        
function showTOP() {

    sound();

    $("#row_d"+1).text(array[0][0]);
    $("#row_n"+1).text(array[0][1]);
    $("#row_t"+1).text(array[0][2]);
    
    bg_change(0);
}

function bg_change(s) {
    
    if(s>=6){
        $('tr#row1').css('background-color', 'White');
        return;
    }else if(s%2==0){
        $('tr#row1').css('background-color', 'Red');
    }else if(s%2==1){
        $('tr#row1').css('background-color', 'White');
    }
    setTimeout('bg_change('+(s+1)+')', 500);
    
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
    <div id="container_rank">

        <div id="r_title_box"><?php printf($quiz_title." 正解者早押しランキング"); ?></div><br>
            <div id="ranking_box">
                <center>
                <table class="ranking">
                    <tr align="center">
                    <th class="ranking_" height="50" width="200" bgcolor="#d3d3d3" ><span class="font24">順位</span></th>
                    <th class="ranking_" height="50" width="250" bgcolor="#d3d3d3" ><span class="font24">チーム</span></th>
                    <th class="ranking_" height="50" width="450" bgcolor="#d3d3d3" ><span class="font24">氏名（敬称略）</span></th>
                    <th class="ranking_" height="50" bgcolor="#d3d3d3" ><span class="font24">回答時間</span></th>
                    </tr>
                    <?php
                        for($i=1; $i<=10; $i++){
                    ?>
                    <tr align="center" class="rr" id="<?php printf("row".$i); ?>">
                    <th class="ranking_" height="50" width="200" ><span class="font40"><?php printf($i); ?></span></th>
                    <th class="ranking_" height="50" width="250" ><span class="font30" id="<?php printf("row_d".$i); ?>"><?php printf($data[$i]["div_name"]); ?></span></th>
                    <th class="ranking_" height="50" width="450" ><span class="font40" id="<?php printf("row_n".$i); ?>"><?php printf($data[$i]["name"]); ?></span></th>
                    <th class="ranking_" height="50" ><span class="font24" id="<?php printf("row_t".$i); ?>"><?php printf($data[$i]["diff"]); ?></span><span class="font20"></span></th>
                    </tr>
                    <?php
                        }
                    ?>
                    

                </table>
                </center>

            </div>
        </div>
    </div>
<div id="footer">
<input type='button' id="start" value='スタート' onclick='show_start();'>  
<!-- <input type='button' id="stop_answer" value='トップ発表' onclick='showTOP();'> -->
<a href="../main.php?quiz_id=<?php printf($quiz_id+1); ?>">次へ</a> 
</div><!-- footer -->
</div>
</div>
</body>
</html>
