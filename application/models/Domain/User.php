<?php
// FIXME: решить вопрос с подгрузкой для PHPUnit
require_once APPLICATION_PATH . '/../library/Xboom/Model/Domain/AbstractObject.php';
/**
 * @Entity
 * @Table(name="users")
 */
class Application_Model_Domain_User extends Xboom_Model_Domain_AbstractObject
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @Column(type="string", length=50) */
    protected $name;
}