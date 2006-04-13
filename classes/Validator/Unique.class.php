<?php
class Doctrine_Validator_Unique {
    /**
     * @param Doctrine_Record $record
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function validate(Doctrine_Record $record, $key, $value) {
        $table = $record->getTable();
        $sql   = "SELECT id FROM ".$table->getTableName()." WHERE ".$key." = ?";
        $stmt  = $table->getSession()->getDBH()->prepare($sql);
        $stmt->execute(array($value));
        return ( ! is_array($stmt->fetch()));
    }
}
?>
