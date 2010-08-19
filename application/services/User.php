<?php
// FIXME: решить вопрос с подгрузкой для PHPUnit
require_once APPLICATION_PATH . '/../library/Xboom/Service/Abstract.php';
/**
 * Description of User
 *
 * @author yugeon
 */
class App_Service_User extends Xboom_Service_Abstract
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function  __construct(Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get all user as array of objects.
     *
     * @return array
     */
    public function getUserList()
    {
        return $this->em->createQuery('SELECT u FROM App_Model_Domain_User u')
                        ->getResult();

    }
}
