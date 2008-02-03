<?php
class ValidatorTest_DateModel extends Doctrine_Record {
    public static function initMetadata($class) {
        $class->setColumn('birthday', 'date', null, array('past'));
        $class->setColumn('death', 'date', null, array('future'));
    }
}
