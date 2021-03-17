<?php 
// Ajax以外からのアクセスを遮断(※ajaxならヘッダー情報に"HTTP_X_REQUESTED_WITH"がついてくる)
$request = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
if($request !== 'xmlhttprequest') exit;
 
$text = filter_input(INPUT_GET, 'text');
// $text = $_GET['text'] でもok;

header('Content-type: application/json; charset=utf-8');
// 以下のjsonがリクエスト元に返される
echo json_encode(['text' => $text . ', World!']);
?>