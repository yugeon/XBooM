<?php

class DIContainer extends sfServiceContainer
{
  protected $shared = array();

  public function __construct()
  {
    parent::__construct($this->getDefaultParameters());
  }

  protected function getMail_TransportService()
  {
    $instance = new Zend_Mail_Transport_Smtp('smtp.gmail.com', array('auth' => 'login', 'username' => $this->getParameter('mailer.username'), 'password' => $this->getParameter('mailer.password'), 'ssl' => 'ssl', 'port' => true));

    return $instance;
  }

  protected function getMailerService()
  {
    if (isset($this->shared['mailer'])) return $this->shared['mailer'];

    $class = $this->getParameter('mailer.class');
    $instance = new $class();
    $instance->setDefaultTransport($this->getService('mail.transport'));

    return $this->shared['mailer'] = $instance;
  }

  protected function getDefaultParameters()
  {
    return array(
      'mailer.username' => 'foo',
      'mailer.password' => 'bar',
      'mailer.class' => 'Zend_Mail',
    );
  }
}
