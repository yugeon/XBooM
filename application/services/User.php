<?php
use \App_Model_Domain_User as User;
/**
 * Description of User
 *
 * @author yugeon
 */
class App_Service_User extends Xboom_Service_AbstractService
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
     * Get all users as array of objects.
     *
     * @return array
     */
    public function getUsersList()
    {
        return $this->em->createQuery('SELECT u FROM App_Model_Domain_User u')
                        ->getResult();

    }

    /**
     * Return user by id. All relations not loaded!
     *
     * @param int $userId
     * @return App_Model_Domain_User 
     */
    public function getUserById($userId)
    {
        return $this->em->find('App_Model_Domain_User', (int)$userId);
    }

    /**
     * Register new user. Login must be present in $data.
     *
     * @param array $data
     * @return User
     */
    public function registerNewUser(array $data)
    {
        // TODO: ACL        !!!
        // TODO: Validation !!!
        if (empty ($data['login']))
        {
            throw new InvalidArgumentException('Login field must be set.');
        }

        if (empty($data['name']))
        {
            $data['name'] = $data['login'];
        }
        
        $user = new User($data);
        $this->em->persist($user);

        // FIXME: Надо ли делать сброс тут?
        $this->em->flush();
        return $user;
    }
}
