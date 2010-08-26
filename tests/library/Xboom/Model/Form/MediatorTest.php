<?php

require_once 'PHPUnit/Framework.php';
require_once APPLICATION_PATH . '/../library/Xboom/Model/Form/Mediator.php';
/**
 * Description of MediatorTest
 *
 * @author yugeon
 */
class MediatorTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Xboom_Model_Form_Mediator
     */
    protected $_object = null;

    public function setUp()
    {
        parent::setUp();
        $this->_object = new Xboom_Model_Form_Mediator();
    }

    public function testIsValid()
    {

    }
}
