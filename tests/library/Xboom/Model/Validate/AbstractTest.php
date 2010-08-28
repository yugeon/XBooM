<?php
require_once 'PHPUnit/Framework.php';

class Xboom_Model_Validate_TestModel implements Xboom\Model\Validate\ValidateInterface
{

    public function  addElement($element, $name = null, $options = null)
    {
    }

    public function  isValid($value)
    {
    }

    public function getMessages()
    {
    }
}
/**
 * Description of AbstractTest
 *
 * @author yugeon
 */
class Xboom_Model_Validate_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testfunctionName()
    {
        $this->markTestSkipped();
    }
}
