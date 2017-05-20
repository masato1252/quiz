<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
    <?php
        require_once './admin_header.php';
    ?>
    <script type="text/javascript">

    </script>

</head>
<body>
<div data-role="page" id="index" data-theme="a">
    <div data-role="header">
        <h1>管理メニュー</h1>
    </div>
    <div data-role="main" class="ui-content">
        <a href="./set_answer.php">答えの設定</a><br><br>
        <a href="./edit_quiz.php">問題編集・登録</a><br><br>
        <a href="./edit_div.php">チーム名編集・登録</a><br><br>
        <a href="./quiz_img.php">選択肢画像の管理</a><br><br>
        <br><br>
        <a href="./responder.php">参加者数モニター</a><br><br>
        <a href="./monitor.php">DB状態モニター</a><br><br>
        <a href="./debug_tools.php">デバッグ用機能(取扱注意)</a>
    </div>
</div>
</body>
</html>   
