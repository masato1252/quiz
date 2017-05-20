<?php
//require_once('../../core/admin_auth.inc');
require_once("../core/connect_db.inc");

$dir = "../img/";  //アップロード先ディレクトリ

?>
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
<?php

if(empty($_POST['img_name'])){
    print("任意の画像IDを入力してください<br><br><a href='javascript:history.back();'>戻る</a>");
    exit();
}else{
    $img_name = $_POST['img_name'];
    if (!preg_match("/^[a-zA-Z0-9]+$/", $img_name)) {
        print("画像IDは全て半角英数字で指定してください<br><br><a href='javascript:history.back();'>戻る</a>");
        exit();
    }

}

try{
    $db = getDB();

    //ID重複チェック
    $stt = $db->prepare('SELECT * FROM ta_img_table WHERE img_name=:img_name');
    $stt->bindValue(':img_name', $img_name);
    $stt->execute();
    if($stt->rowCount()!=0){
        printf("この画像IDはすでに使用されています<br><br><a href=javascript:history.back()>戻る</a>");
        die();
    }

}catch(PDOException $e){
    die("接続エラー".$e);
}


if (isset($_FILES['upfile']['error']) && is_int($_FILES['upfile']['error'])) {

    try {

        // $_FILES['upfile']['error'] の値を確認
        switch ($_FILES['upfile']['error']) {
            case UPLOAD_ERR_OK: // OK
                break;
            case UPLOAD_ERR_NO_FILE:   // ファイル未選択
                throw new RuntimeException('ファイルが選択されていません');
            case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
            case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過
                throw new RuntimeException('ファイルサイズが大きすぎます');
            default:
                throw new RuntimeException('その他のエラーが発生しました');
        }
$type = @exif_imagetype($_FILES['upfile']['tmp_name']);
/*
        // $_FILES['upfile']['mime']の値はブラウザ側で偽装可能なので、MIMEタイプを自前でチェックする
        $type = @exif_imagetype($_FILES['upfile']['tmp_name']);
        $arr = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);
        if (!in_array($type, $array, true)) {
            throw new RuntimeException('画像形式が未対応です');
        }
 * */

        if(!exif_imagetype($_FILES['upfile']['tmp_name'])){
            throw new RuntimeException('画像形式が未対応です');
        }

        // ファイルデータからSHA-1ハッシュを取ってファイル名を決定し、ファイルを保存する
        $img_id = date("YmdHis");
        $file_name = $img_id.image_type_to_extension($type);
        $path = sprintf($dir.'%s%s', $img_id, image_type_to_extension($type));
        if (!move_uploaded_file($_FILES['upfile']['tmp_name'], $path)) {
            throw new RuntimeException('ファイル保存時にエラーが発生しました');
        }
        chmod($path, 0644);

        //$msg = ['green', 'ファイルは正常にアップロードされました'];

    } catch (RuntimeException $e) {

        //$msg = ['red', $e->getMessage()];
        print($e->getMessage()."<br><br><a href='javascript:history.back();'>戻る</a>");
        die();
    }


    try{
        //DB登録
        $stt = $db->prepare('INSERT INTO ta_img_table (img_id, img_name, file_name) VALUES (:img_id, :img_name, :file_name)');
        $stt->bindValue(':img_id', $img_id);
        $stt->bindValue(':img_name', $img_name);
        $stt->bindValue(':file_name', $file_name);
        $stt->execute();

    }catch(PDOException $e){
        unlink($path);
        die("DBへ登録できませんでした<br><br><a href=./show_img.php>戻る</a>".$e);
    }

    print("アップロード完了しました<br><br><a href=./show_img.php>戻る</a>");

}

?>

</div><!-- main -->
<!--wrapper--></div>
</body>
</html>
