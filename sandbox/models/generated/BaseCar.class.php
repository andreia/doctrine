<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseCar extends Doctrine_Record
{

	public function setTableDefinition()
	{
		$this->setTableName('car');
		$this->hasColumn('id', 'integer', 11, array('primary' => true,
                                              'autoincrement' => true));
		$this->hasColumn('name', 'string', 255);





	}

	public function setUp()
	{
		$this->hasMany('User as Users', array('refClass' => 'UserCar',
                                        'local' => 'car_id',
                                        'foreign' => 'user_id'));

	}

}