<?php
require_once __DIR__ . '/ConsumableResponsibleHistory.php';
require_once __DIR__ . '/../connection.php';

class ConsumableResponsibleHistoryContext {
  public static function add(int $cid,int $uid,string $comment): void {
    $pdo=OpenConnection();
    $pdo->prepare("INSERT INTO ConsumableResponsibleHistory (consumable_id,user_id,comment) VALUES(?,?,?)")
        ->execute([$cid,$uid,$comment]);
  }

  /** @return ConsumableResponsibleHistory[] */
  public static function getByConsumable(int $cid): array {
    $pdo=OpenConnection();
    $stmt=$pdo->prepare(
      "SELECT h.*,u.last_name,u.first_name,u.middle_name
       FROM ConsumableResponsibleHistory h
       JOIN `User` u ON u.id=h.user_id
       WHERE h.consumable_id=? ORDER BY h.changed_at DESC"
    );
    $stmt->execute([$cid]); $out=[];
    while($r=$stmt->fetch(PDO::FETCH_ASSOC)){
      $name="{$r['last_name']} {$r['first_name']}" .($r['middle_name']?" {$r['middle_name']}":'');
      $out[]= new ConsumableResponsibleHistory(
        $r['id'],$r['consumable_id'],$r['user_id'],"{$name}: {$r['comment']}", $r['changed_at']
      );
    }
    return $out;
  }

  public static function getUserName(int $uid): string {
    return OpenConnection()->prepare("SELECT CONCAT_WS(' ',last_name,first_name,middle_name) FROM `User` WHERE id=?")
      ->execute([$uid]) && ($n=OpenConnection()->fetchColumn()) ? $n : '';
  }
}