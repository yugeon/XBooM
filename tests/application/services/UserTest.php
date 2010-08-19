<?php
use \Mockery as m;

require_once 'PHPUnit/Framework.php';
require_once 'Mockery.php';

require_once APPLICATION_PATH . '/services/User.php';

/**
 * Description of User
 *
 * @author yugeon
 */
class App_Service_UserTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var App_Service_User
     */
    protected $object = null;

    public function setUp()
    {
        parent::setUp();
        $result = array(
            new Application_Model_Domain_User(),
            new Application_Model_Domain_User()
        );

        $em = m::mock('\\Doctrine\\ORM\\EntityManager');
        $em->shouldReceive('createQuery')->once()->andReturn($em);
        $em->shouldReceive('getResult')->once()->andReturn($result);

        $this->object = new App_Service_User($em);
    }

    public function teardown()
    {
        m::close();
    }

    public function  testGetUserList()
    {
        $userList = $this->object->getUserList();
        $this->assertTrue(is_array($userList));
        $this->assertEquals(2, count($userList));
    }
}
