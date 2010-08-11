<?php

/**
 * @Entity
 * @Table(name="users")
 */
class Application_Model_Domain_User
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @Column(type="string", length=50) */
    private $name;

    public function getId()
    {
        return $this->id;
    }
    public function setName($string) {
        $this->name = $string;
        return true;
    }
    public function getName()
    {
        return $this->name;
    }
}