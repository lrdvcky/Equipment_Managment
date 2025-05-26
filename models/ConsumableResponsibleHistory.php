<?php 
declare(strict_types=1);

class ConsumableResponsibleHistory {
    public int $id;
    public int $consumable_id;
    public int $user_id;
    public string $comment;
    public string $changed_at;

    /**
     * @param int    $id
     * @param int    $consumable_id
     * @param int    $user_id
     * @param string $comment
     * @param string $changed_at
     */
    public function __construct(
        int $id,
        int $consumable_id,
        int $user_id,
        string $comment,
        string $changed_at
    ) {
        $this->id = $id;
        $this->consumable_id = $consumable_id;
        $this->user_id = $user_id;
        $this->comment = $comment;
        $this->changed_at = $changed_at;
    }
}
?>