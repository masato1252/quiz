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
    <script src="./js/main.js" type="text/javascript"></script>
    <!-- <script src="./js/footerFixed.js" type="text/javascript"></script> -->
    <script type="text/javascript">
        /*
          functionはmain.jsに定義
        */

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
            </div><!-- title_box -->

            <div id="timer_box">
                <div id="nokori">　</div>
                <div id="timer">　</div>
            </div><!-- timer_box -->
        </div><!-- top -->
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
                                    </div><!-- gauge-wrapper -->
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
                </div><!-- sel_numBox -->
                <div class="sel_nameBox" id="sel_nameBox2">
                    <?php printf($row['select2']); ?>
                </div><!-- sel_nameBox -->

                <div class="sel_guageBox" id="sel_guageBox2">
                    <table width="300px">
                        <tr>
                            <td class="gaugeLabel">回答者数</td>
                        </tr>
                        <tr>
                            <td height="30px">
                                <div class="gauge-wrapper">
                                    <div class="gauge-type1 gauge-inner gauge2"><div id="sel2">0</div></div>
                                </div><!-- gauge-wrapper -->
                            </td>
                        </tr>
                    </table>

                </div><!-- sel_guageBox -->

                <div class="clear"></div>
            </div><!-- sel_all -->
        </div><!-- select2 -->

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
                                </div><!-- gauge-wrapper -->
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
                                </div><!-- gauge-wrapper -->
                            </td>
                        </tr>
                    </table>
                </div><!-- sel_guageBox -->
                <div class="clear"></div>
            </div><!-- sel_all -->
        </div><!-- select4 -->

    </div><!-- container -->

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

</div><!-- layerTransparent -->
</div><!-- layerImage -->

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

                </div><!-- title_box -->

                <div id="timer_box">
                    <div id="nokori">　</div>
                    <div id="timer">　</div>
                </div><!-- timer_box -->
            </div><!-- top -->
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
