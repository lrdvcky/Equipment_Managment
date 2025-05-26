<?php
// models/EquipmentResponsibleHistory.php

class EquipmentResponsibleHistory {
    public int    $id;
    public int    $equipment_id;
    public int    $user_id;
    public string $comment;
    public string $changed_at;

    public function __construct(
        int $id,
        int $equipment_id,
        int $user_id,
        string $comment,
        string $changed_at
    ) {
        $this->id            = $id;
        $this->equipment_id  = $equipment_id;
        $this->user_id       = $user_id;
        $this->comment       = $comment;
        $this->changed_at    = $changed_at;
    }
}
