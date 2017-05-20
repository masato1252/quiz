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
<h1>チーム新規追加</h1>
</div>
<div data-role="main" class="ui-content">
<a href="./edit_div.php">＜＜戻る</a><br><br>

<form action="./regist_div.php" method="post" id="edit_div">

順番：<input type="text" name="num" size="3" value=""><br>
チーム名：<input type="text" name="div_name" size="30" value=""><br>
ランキング表示名：<input type="text" name="div_s" size="30" value="">
<br><br>
<input type="submit" value="送信" >
</div>
</div>
</body>
</html>
