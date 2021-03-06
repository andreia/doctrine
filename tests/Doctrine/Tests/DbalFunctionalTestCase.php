<?php

namespace Doctrine\Tests;

class DbalFunctionalTestCase extends DbalTestCase
{
    /* Shared connection when a TestCase is run alone (outside of it's functional suite) */
    private static $_sharedConn;
    protected $_conn;

    protected function setUp()
    {
        if (isset($this->sharedFixture['conn'])) {
            $this->_conn = $this->sharedFixture['conn'];
        } else {
            if ( ! isset(self::$_sharedConn)) {
                self::$_sharedConn = TestUtil::getConnection();
            }
            $this->_conn = self::$_sharedConn;
        }
    }
}