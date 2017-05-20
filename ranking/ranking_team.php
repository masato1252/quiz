<?php
    
    require_once("../core/connect_db.inc");
    require_once("../core/setting.php");
    
    $next_link = "./prize.php";
    
    try{
        $db = getDB();
        
        $stt = $db->prepare('SELECT * FROM ta_div_table ORDER BY div_num ASC;');
        $stt->execute();
        $c=0;
        while($row = $stt->fetch()){
            $name[$row["div_num"]] = $row["div_name"];
            $count[$row["div_num"]] = 0;
            $c++;
        }
        
        $start=1; $end=20;
        $limit = $end-$start+1;
        $offset = $start-1;
        $stt = $db->prepare('SELECT * FROM ta_ranking AS qr, ta_user AS qu WHERE qu.id=qr.user_id ORDER BY qr.correct DESC, qr.time_valid DESC, qr.sum_time ASC LIMIT :limit OFFSET :offset;');
        $stt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stt->execute();
        while($row = $stt->fetch()){
            $count[$row["div_num"]]++;
        }
        
        //最大値
        $MAX=0; $DIV_NUM=-1;
        for($i=0; $i<$c; $i++){
            
            if($MAX < $count[$i]){
                $MAX = $count[$i];
                $DIV_NUM = $i;
            }
            
        }
        
        $TOP_TEAM = $name[$DIV_NUM];
        
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

currentSound = null;
function sound(){
    currentSound = $("#win").get(0);
    currentSound.play();
}


//「スタート」押下
function show_start(){
    sound();
    $('div#team').css('display', 'block');
    bg_change(0,0);
}

function bg_change(rank,s) {
    
    if(s>=8){
        $('#team').css('background-color', 'White');
        return;
    }else if(s%2==0){
        $('#team').css('background-color', '#FA5858');
    }else if(s%2==1){
        $('#team').css('background-color', 'White');
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

<div id="r_title_box"><?php printf("チーム賞"); ?></div><br><br>
<div class="explain">1位〜20位にランクインした人数が最も多いチーム</div>
<div id="team" style="display: none;">
チーム「<?php printf($TOP_TEAM); ?>」<br>
<div class="extra">※<font color="red"><?php printf($MAX); ?>人</font>がランクイン</div>
</div>

</div><!-- containerRank -->
</div><!-- main -->
<div id="footer">
<input type='button' id="start" value='スタート' onclick='show_start();' />
<a href="<?php printf($next_link); ?>">次へ</a>
</div><!-- footer -->
</div></div>
</body>
</html>
