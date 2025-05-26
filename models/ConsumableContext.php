<?php
require_once __DIR__ . '/Consumable.php';
require_once __DIR__ . '/../connection.php';

class ConsumableContext {
  /** @return Consumable[] */
  public static function getAll(): array {
    $pdo  = OpenConnection();
    $stmt = $pdo->query("SELECT * FROM `Consumable`");
    $out  = [];
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $out[] = new Consumable(
        (int)$r['id'],
        $r['name'],
        $r['description'],
        $r['arrival_date'],
        $r['image'],
        (int)$r['quantity'],
        (int)$r['responsible_user_id'],
        $r['temporary_responsible_user_id'] !== null ? (int)$r['temporary_responsible_user_id'] : null,
        (int)$r['consumable_type_id']
      );
    }
    return $out;
  }

  /** @return Consumable */
  public static function getById(int $id): Consumable {
    $pdo = OpenConnection();
    $stmt = $pdo->prepare("SELECT * FROM `Consumable` WHERE id = ?");
    $stmt->execute([$id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$r) throw new Exception("Consumable #{$id} not found");
    return new Consumable(
      (int)$r['id'],
      $r['name'],
      $r['description'],
      $r['arrival_date'],
      $r['image'],
      (int)$r['quantity'],
      (int)$r['responsible_user_id'],
      $r['temporary_responsible_user_id'] !== null ? (int)$r['temporary_responsible_user_id'] : null,
      (int)$r['consumable_type_id']
    );
  }

  /** Валидирует $_POST + $_FILES и возвращает связанный массив */
  public static function parseRequest(bool $allowNoPhoto=false): array {
    // базовые поля
    $fields = ['name','description','arrival_date','quantity','consumable_type_id','responsible_user_id','temporary_responsible_user_id'];
    $data = [];
    foreach ($fields as $f) {
      $val = $_POST[$f] ?? '';
      $data[$f] = trim($val) !== '' ? trim($val) : null;
    }
    // проверка даты ДД.MM.ГГГГ
    if (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $data['arrival_date'] ?? '')) {
      throw new Exception('Дата должна быть в формате ДД.MM.ГГГГ');
    }
    // проверка количества
    if (!ctype_digit((string)$data['quantity'])) {
      throw new Exception('Количество — только цифры');
    }
    // фото
    if (isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
      $data['image'] = file_get_contents($_FILES['photo']['tmp_name']);
    } elseif (!$allowNoPhoto) {
      $data['image'] = null;
    }
    return $data;
  }

  /** @return int */
  public static function create(array $d): int {
    $pdo = OpenConnection();
    $sql = "INSERT INTO `Consumable`
      (name,description,arrival_date,image,quantity,responsible_user_id,temporary_responsible_user_id,consumable_type_id)
      VALUES (?,?,?,?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      $d['name'],
      $d['description'],
      $d['arrival_date'],
      $d['image'],
      $d['quantity'],
      $d['responsible_user_id'],
      $d['temporary_responsible_user_id'],
      $d['consumable_type_id']
    ]);
    return (int)$pdo->lastInsertId();
  }

  /** @return void */
  public static function update(int $id, array $d): void {
    $pdo = OpenConnection();
    // базовый UPDATE
    $sql = "UPDATE `Consumable` SET
      name = ?, description = ?, arrival_date = ?, quantity = ?,
      responsible_user_id = ?, temporary_responsible_user_id = ?, consumable_type_id = ?";
    $params = [
      $d['name'], $d['description'], $d['arrival_date'], $d['quantity'],
      $d['responsible_user_id'], $d['temporary_responsible_user_id'], $d['consumable_type_id']
    ];
    // если передан новый файл
    if (array_key_exists('image', $d)) {
      $sql .= ", image = ?";
      $params[] = $d['image'];
    }
    $sql .= " WHERE id = ?";
    $params[] = $id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
  }

  /** @return void */
  public static function delete(int $id): void {
    $pdo = OpenConnection();
    $stmt = $pdo->prepare("DELETE FROM `Consumable` WHERE id = ?");
    $stmt->execute([$id]);
  }

  /** вспомогательные поля для JS */
  public static function extraFields(Consumable $c): array {
    $pdo = OpenConnection();
    // тип
    $t = $pdo->prepare("SELECT name FROM `ConsumableType` WHERE id = ?");
    $t->execute([$c->consumable_type_id]);
    $type_name = $t->fetchColumn() ?: '';
    // свойства
    $p = $pdo->prepare("SELECT property_name,property_value FROM `ConsumableProperty` WHERE consumable_id=?");
    $p->execute([$c->id]);
    $props = [];
    while ($r = $p->fetch(PDO::FETCH_ASSOC)) {
      $props[] = "{$r['property_name']}: {$r['property_value']}";
    }
    // ответственные
    $u = $pdo->prepare("SELECT CONCAT_WS(' ',last_name,first_name,middle_name) FROM `User` WHERE id=?");
    $u->execute([$c->responsible_user_id]);
    $resp = $u->fetchColumn() ?: '';
    $u->execute([$c->temporary_responsible_user_id]);
    $temp = $u->fetchColumn() ?: '';

    return [
      'type_name'                  => $type_name,
      'properties'                 => implode(', ', $props),
      'responsible_name'           => $resp,
      'temporary_responsible_name' => $temp
    ];
  }

  /** для контроллера: список типов */
  public static function getTypes(): array {
    return OpenConnection()
      ->query("SELECT id,name FROM `ConsumableType`")
      ->fetchAll(PDO::FETCH_ASSOC);
  }

  /** для контроллера: список пользователей */
  public static function getUsers(): array {
    return OpenConnection()
      ->query("SELECT id,CONCAT_WS(' ',last_name,first_name,middle_name) AS name FROM `User`")
      ->fetchAll(PDO::FETCH_ASSOC);
  }
}
