<?php
$user_id = $_GET["user_id"];

?>
<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>情シスオールスター感謝クイズ</title>
</head>
<body>
<div id="main">
    <font color="blue"><center><h3>問題回答</h3></center></font>
    <hr>
    投影画面上で回答受付を開始してから『送信』ボタンを押して下さい。<br><br>
    
    <form action="./send_answer_i.php" method="post">
        <input type="hidden" name="user_id" value="<?php printf($user_id); ?>" />      
        選択肢<br>
        <select name="answer">
            <option value="1">　　①　　</option>
            <option value="2">　　②　　</option>
            <option value="3">　　③　　</option>
            <option value="4">　　④　　</option>
        </select><br><br>
        <input type="submit" value="送信" >
    </form>
    
</div><!-- main -->
</body>
</html>   