<?php
    
    require_once("../core/connect_db.inc");
    require_once("../core/setting.php");
    
    $next_link = "./ranking_end.php";
    
    
    function convTime($str){
        $arr = explode(".",$str);
        $arr2 =explode(":",$arr[0]);
        return $arr2[1].":".$arr2[2].".".substr($arr[1],0,3);
    }
    
    try{
        $db = getDB();
        
        //---------------
        //   1〜3位
        //---------------
        $start=1; $end=3;
        $limit = $end-$start+1;
        $offset = $start-1;
        

        $stt = $db->prepare('SELECT * FROM ta_ranking AS qr, ta_user AS qu, ta_div_table AS dt WHERE qu.id=qr.user_id AND dt.div_num=qu.div_num ORDER BY qr.correct DESC, qr.time_valid DESC, qr.sum_time ASC LIMIT :limit OFFSET :offset;');
        $stt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stt->execute();
        $c=1;
        $data[0] = $mode;
        while($row = $stt->fetch()){
            $data[$c]["div_name"] = $row["div_s"];
            $data[$c]["name"] = $row["name"];
            $data[$c]["correct"] = $row["correct"];
            $data[$c]["sum_time"] = convTime($row["sum_time"]);
            $data[$c]["rank"] = $start+$c-1;
            $c++;
        }


        //---------------
        //   キリ番
        //---------------
        $stt = $db->prepare('SELECT rank FROM ta_rank_kiri ORDER BY rank ASC;');
        $stt->execute();
        
        while($row = $stt->fetch()){
            
            $limit = 1;
            $offset = $row['rank']-1;
            
            $stt2 = $db->prepare('SELECT * FROM ta_ranking AS qr, ta_user AS qu, ta_div_table AS dt WHERE qu.id=qr.user_id AND dt.div_num=qu.div_num ORDER BY qr.correct DESC, qr.time_valid DESC, qr.sum_time ASC LIMIT :limit OFFSET :offset;');
            $stt2->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stt2->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stt2->execute();
            $row2 = $stt2->fetch();
            
            $data[$c]["div_name"] = $row2["div_s"];
            $data[$c]["name"] = $row2["name"];
            $data[$c]["correct"] = $row2["correct"];
            $data[$c]["sum_time"] = convTime($row2["sum_time"]);
            $data[$c]["rank"] = $row["rank"];
            $c++;
        }
        $RANK_NUM = $c-1;
        
        //---------------
        //   チーム賞
        //---------------
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

<div id="r_title_box"><?php printf("表彰式"); ?></div><br><br>

<div class="nameBox" id="boxHead">
<div class="rank">順位</div>
<div class="team">チーム</div>
<div class="name">氏名(敬称略)</div>
<div class="num">正解数</div>
<div class="time">回答時間</div>
<div class="clear"></div>
</div>
<?php
    for($i=1; $i<=$RANK_NUM; $i++){
?>
<div class="nameBox" id="<?php printf("row".$i); ?>">
<div class="rank" id="<?php printf("row_r".$i); ?>"><?php printf($data[$i]["rank"]); ?></div>
<div class="team" id="<?php printf("row_d".$i); ?>"><?php printf($data[$i]["div_name"]); ?></div>
<div class="name" id="<?php printf("row_n".$i); ?>"><?php printf($data[$i]["name"]); ?></div>
<div class="num" id="<?php printf("row_c".$i); ?>"><?php printf($data[$i]["correct"]); ?></div>
<div class="time" id="<?php printf("row_t".$i); ?>"><?php printf($data[$i]["sum_time"]); ?></div>
<div class="clear"></div>
</div>
<?php
    }
?>
<br><br>
<div class="explain2"><?php printf("チーム賞：  チーム「".$TOP_TEAM."」"); ?></div>
</div><!-- containerRank -->
</div><!-- main -->
<div id="footer">
<a href="<?php printf($next_link); ?>">次へ</a>
</div><!-- footer -->
</div></div>
</body>
</html>
