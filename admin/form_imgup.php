<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/XHTML1/DTD/xhtml1-transitional.dtd">
<html xmls="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<?php
    require_once './admin_header.php';
?>
</head>
<body>
<div data-role="page" id="index" data-theme="a">
<div data-role="header">
    <h1>画像アップロード</h1>
</div>
<div data-role="main" class="ui-content">
<a href=./show_img.php>戻る</a>
<form enctype="multipart/form-data" method="post" action="./add_imgup.php">
    <div class="text">
      <h3>画像ID (半角英数字)</h3>
      <input type="text" name="img_name" size="25" maxlength="25">
    </div>
    <div class="text">
      <h3>画像ファイルを選択 (GIF, JPEG, PNG形式)</h3>
      <input type="file" name="upfile" /><br />
      </div>

      <input type="submit" value="アップロード" />
</form>
</div><!-- main -->
<!--wrapper--></div>
</body>
</html>
