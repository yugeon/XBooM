<?php

namespace Core\Model\Domain;
/**
 * @Entity
 * @Table(name="users")
 */
class User
    extends \Xboom\Model\Domain\AbstractObject
    implements \Zend_Acl_Role_Interface
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @Column(type="string", length=50) */
    protected $name;

    /** @Column(type="string", length=16) */
    protected $login;

    /** @Column(type="string", length=32) */
    protected $password;

    protected $role = 'guest';


    public function __construct(array $data = null)
    {

        if (!is_null($data))
        {
            // Login field must be set
            if (empty($data['login']))
            {
                throw new \InvalidArgumentException('Login must be set.');
            }

            // If name not set, then name equals login
            if (empty($data['name']))
            {
                $data['name'] = $data['login'];
            }

            foreach ($data as $property => $value)
            {
                $accessor = 'set' . ucfirst($property);
                $this->{$accessor}($value);
            }
        }
    }

    /**
     * Returns the string identifier of the Role
     *
     * @return string
     */
    public function getRoleId()
    {
        return $this->role;
    }

}