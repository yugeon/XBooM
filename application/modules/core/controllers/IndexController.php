<?php

class Core_IndexController extends Zend_Controller_Action
{
    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected  $em;

    public function init()
    {
        $sc = $this->getInvokeArg('bootstrap')->getContainer();
        $this->em = $sc->getService('doctrine.orm.entitymanager');
    }

    public function indexAction()
    {
        $results = $this->em->createQuery('SELECT u FROM Application_Model_Domain_User u')
                            ->getResult();
        $date = new Zend_Date();
        var_dump($date->get(Zend_Date::YEAR));
        $this->view->users = $results;
    }

    public function addAction()
    {
        $user = new Application_Model_Domain_User();
        $user->setName('TestName' . rand(1, 100));

        $this->em->persist($user);
        $this->em->flush();

        $this->_helper->getHelper('Redirector')
             ->gotoUrl('/');
    }

    public function deleteAction()
    {
        $user = $this->em->find('Application_Model_Domain_User', (int) $this->_getParam('id'));

        $this->em->remove($user);
        $this->em->flush();

        $this->_helper->getHelper('Redirector')
             ->gotoUrl('/');
    }
}

