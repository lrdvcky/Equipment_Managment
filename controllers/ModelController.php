<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../connection.php';

if(($_GET['action']??'')==='get'){
  $db = OpenConnection();
  $rows = $db->query("SELECT id,name FROM `Model` ORDER BY name")
             ->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($rows, JSON_UNESCAPED_UNICODE);
  exit;
}
http_response_code(400);
echo json_encode(['error'=>'Invalid request']);
