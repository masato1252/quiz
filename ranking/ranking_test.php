<?php
    
require_once("connect_db.inc");

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
    <link rel="stylesheet" href="./css/origin.css">
    <link rel="stylesheet" href="./css/fontsize.css">
    <script src="./js/jquery-min.js" type="text/javascript"></script>
    <script src="./js/jquery.periodicalupdater.js" type="text/javascript"></script>
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

     setTimeout('showUP('+data_count+','+1+')', 0);
}

function showOne(n) {

    $("#row_d"+n).text(array[n-1][0]);
    $("#row_n"+n).text(array[n-1][1]);
    $("#row_c"+n).text(array[n-1][2]);
    $("#row_t"+n).text(array[n-1][3]);
    
    if(num==n){
        bg_change(n,0);
    }
}

function showUP(length, num) {

    
    var now = length - (num-1);
    console.log("now="+now);

    $("#row_d"+now).text(array[now-1][0]);
    $("#row_n"+now).text(array[now-1][1]);
    $("#row_c"+now).text(array[now-1][2]);
    $("#row_t"+now).text(array[now-1][3]);
    $("#row_r"+now).text(array[now-1][4]);
    if(now<=1) return;
    setTimeout('showUP('+length+','+(num+1)+')', 0);
}

function showUP_NoTop(length, num) {

    
    var now = length - (num-1);
    console.log("now="+now);
    
    $("#row_d"+now).text(array[now-1][0]);
    $("#row_n"+now).text(array[now-1][1]);
    $("#row_c"+now).text(array[now-1][2]);
    $("#row_t"+now).text(array[now-1][3]);
    
    if(now<=1) return;
    
    setTimeout('showUP_NoTop('+length+','+(num+1)+')', 500);
}
        
function showTOP() {

    $("#row_d"+1).text(array[0][0]);
    $("#row_n"+1).text(array[0][1]);
    $("#row_c"+1).text(array[0][2]);
    $("#row_t"+1).text(array[0][3]);
    
    bg_change(1,0);
}

function bg_change(rank,s) {
    
    if(s>=6){
        $('tr#row'+rank).css('background-color', 'White');
        return;
    }else if(s%2==0){
        $('tr#row'+rank).css('background-color', 'Red');
    }else if(s%2==1){
        $('tr#row'+rank).css('background-color', 'White');
    }
    setTimeout('bg_change('+rank+','+(s+1)+')', 500);
    
}        
    </script>
</head>
<body>
    <div id="main">
    <div data-role="header">
            <h1>情シスオールスター感謝クイズ</h1>
    </div>
    <div id="container_rank">

<div id="title_box"><?php printf("総合ランキング"); ?></div>
<div left:1000px>
<table class="ranking">
<tr align="center">
<th class="ranking_" height="35" width="150" bgcolor="#d3d3d3" ><span class="font30">順位</span></th>
<th class="ranking_" height="35" width="175" bgcolor="#d3d3d3" ><span class="font30">担当</span></th>
<th class="ranking_" height="35" width="300" bgcolor="#d3d3d3" ><span class="font30">氏名（敬称略）</span></th>
<th class="ranking_" height="35" width="150" bgcolor="#d3d3d3" ><span class="font30">正解数</span></th>
<th class="ranking_" height="35" bgcolor="#d3d3d3" ><span class="font30">回答時間</span></th>
<?php
    $c=1;
    for($i=1; $i<=$count; $i++){
?>
<tr align="center" id="<?php printf("row".$c); ?>">
<th height="35" class="ranking_" width="150" ><span class="font40" id="<?php printf("row_r".$c); ?>"></span></th>
<th height="35" class="ranking_" width="175" ><span class="font30" id="<?php printf("row_d".$c); ?>"></span></th>
<th height="35" class="ranking_" width="300" ><span class="font40" id="<?php printf("row_n".$c); ?>"></span></th>
<th height="35" class="ranking_" ><span class="font40" id="<?php printf("row_c".$c); ?>"></span><span class="font20">問</span></th>
<th height="35" class="ranking_" ><span class="font24" id="<?php printf("row_t".$c); ?>"></span><span class="font20"><pan></th>
<?php
    $c++;
    }
?>
</tr>

</table>

</div>
</div>
</div>
<div id="footer">
<input type='button' id="start" value='スタート' onclick='show_start();'>  
<input type='button' id="show_top" value='トップ発表' onclick='showTOP();'>　　
<a href="<?php printf($next_link); ?>">次へ</a>
</div><!-- footer -->
</body>
</html>