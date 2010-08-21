<?php
/**
 * @Entity
 * @Table(name="users")
 */
class App_Model_Domain_User extends Xboom_Model_Domain_AbstractObject
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

    public function __construct(array $data = null)
    {
        if (!is_null($data))
        {
            foreach ($data as $property => $value)
            {
                $this->{$property} = $value;
            }
        }
    }
}