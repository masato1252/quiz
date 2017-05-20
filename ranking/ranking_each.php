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

quiz_id = <?php printf($_GET["quiz_id"]); ?>;
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
          array[i][3] = (i+1);
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
    $("#row_r"+now).text(array[now-1][3]);
    setTimeout('showUP('+length+','+(num+1)+')', 600);
}

function showTOP() {
    
    sound();
    
    $("#row_d"+1).text(array[0][0]);
    $("#row_n"+1).text(array[0][1]);
    $("#row_t"+1).text(array[0][2]);
    $("#row_r"+1).text(array[0][3]);
    
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
<div id="containerRank">

<div id="r_title_box"><?php printf($quiz_title." 正解者早押しランキング"); ?></div><br>

<div class="nameBox" id="boxHead">
<div class="rank">順位</div>
<div class="team">チーム</div>
<div class="name">氏名(敬称略)</div>
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
<div class="time" id="<?php printf("row_t".$i); ?>">　</div>
<div class="clear"></div>
</div>
<?php
    }
    ?>
</div><!-- containerRank -->
</div><!-- main -->
<div id="footer">
<input type='button' id="start" value='スタート' onclick='show_start();'>
<!-- <input type='button' id="stop_answer" value='トップ発表' onclick='showTOP();'> -->
<a href="../main.php?quiz_id=<?php printf($quiz_id+1); ?>">次へ</a>
</div><!-- footer -->
</div>
</div>
</body>
</html>
