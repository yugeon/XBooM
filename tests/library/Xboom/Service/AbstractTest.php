<?php
require_once 'PHPUnit/Framework.php';

class TestService extends Xboom\Service\AbstractService
{

}
/**
 * @author yugeon
 */
class Xboom_Service_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateChildFromAbstractClass()
    {
        $this->assertNotNull(new TestService());
    }
    
}
