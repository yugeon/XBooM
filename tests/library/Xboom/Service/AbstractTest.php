<?php
require_once 'PHPUnit/Framework.php';

require_once APPLICATION_PATH . '/../library/Xboom/Service/AbstractService.php';

class TestService extends Xboom_Service_AbstractService
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
