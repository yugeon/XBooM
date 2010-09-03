<?php
require_once 'PHPUnit/Framework.php';

use \Xboom\Model\Service\AbstractService;

class TestService extends AbstractService
{

}
/**
 * @author yugeon
 */
class AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateChildFromAbstractClass()
    {
        $this->assertNotNull(new TestService());
    }
    
}
