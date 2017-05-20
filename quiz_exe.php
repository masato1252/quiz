<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
<?php
require_once("./core/setting.php");
require_once("./core/connect_db.inc");
require_once("./mobile_header.php");
?>
<script src="./js/quiz_exe.js" type="text/javascript"></script>

</head>
<body>
<div data-role="page" id="index" data-theme="a">
    <div data-role="header">
        <h1 id="quiz_title"><?php printf($TITLE); ?></h1>
    </div><!-- header -->
    <div data-role="main" class="ui-content">
        <div id="question"></div><br>
        <div id="condition">回答開始までお待ち下さい。</div>
    	<div id="timer"></div><br>
        <div id="ctr" style="display:none">
            <input type="button" id="sel1" value="①" data-theme="a" onclick="tap(1);" /><br>
            <input type="button" id="sel2" value="②" data-theme="a" onclick='tap(2);' /><br>
            <input type="button" id="sel3" value="③" data-theme="a" onclick='tap(3);' /><br>
            <input type="button" id="sel4" value="④" data-theme="a" onclick='tap(4);' />
        </div><!-- ctr -->
        <div id="navi"></div>
    </div><!-- main -->
</div><!-- index -->
</body>
</html>   
