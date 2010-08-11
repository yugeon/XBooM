<?php

class DIContainer extends sfServiceContainer
{
  protected $shared = array();

  protected function get_d9db1d24cac14f48e9621eee387e3a1b1Service()
  {
    if (isset($this->shared['_d9db1d24cac14f48e9621eee387e3a1b_1'])) return $this->shared['_d9db1d24cac14f48e9621eee387e3a1b_1'];

    $class = 'Doctrine\\Common\\Annotations\\AnnotationReader';
    $instance = new $class($this->getService('doctrine.common.cache'));
    $instance->setDefaultAnnotationNamespace('Doctrine\\ORM\\Mapping\\');

    return $this->shared['_d9db1d24cac14f48e9621eee387e3a1b_1'] = $instance;
  }

  protected function getDoctrine_Orm_MetadataDriver_XmlService()
  {
    if (isset($this->shared['doctrine.orm.metadata_driver.xml'])) return $this->shared['doctrine.orm.metadata_driver.xml'];

    $class = 'Doctrine\\ORM\\Mapping\\Driver\\XmlDriver';
    $instance = new $class($this->getParameter('doctrine.orm.path_to_mappings'));

    return $this->shared['doctrine.orm.metadata_driver.xml'] = $instance;
  }

  protected function getDoctrine_Orm_MetadataDriver_AnnotationService()
  {
    if (isset($this->shared['doctrine.orm.metadata_driver.annotation'])) return $this->shared['doctrine.orm.metadata_driver.annotation'];

    $class = 'Doctrine\\ORM\\Mapping\\Driver\\AnnotationDriver';
    $instance = new $class($this->getService('_d9db1d24cac14f48e9621eee387e3a1b_1'), $this->getParameter('doctrine.orm.path_to_entities'));

    return $this->shared['doctrine.orm.metadata_driver.annotation'] = $instance;
  }

  protected function getDoctrine_Common_CacheService()
  {
    if (isset($this->shared['doctrine.common.cache'])) return $this->shared['doctrine.common.cache'];

    $instance = call_user_func(array('Xboom_Cache_DoctrineFactory', 'getCache'), $this->getParameter('doctrine.common.cache_class'), $this->getParameter('doctrine.common.cache_options'));

    return $this->shared['doctrine.common.cache'] = $instance;
  }

  protected function getDoctrine_Orm_ConfigurationService()
  {
    if (isset($this->shared['doctrine.orm.configuration'])) return $this->shared['doctrine.orm.configuration'];

    $class = 'Doctrine\\ORM\\Configuration';
    $instance = new $class();
    $instance->setMetadataDriverImpl($this->getService('doctrine.orm.metadata_driver.annotation'));
    $instance->setProxyDir($this->getParameter('doctrine.orm.path_to_proxies'));
    $instance->setProxyNamespace($this->getParameter('doctrine.orm.proxy_namespace'));
    $instance->setAutoGenerateProxyClasses($this->getParameter('doctrine.orm.autogenerate_proxy_classes'));
    $instance->setMetadataCacheImpl($this->getService('doctrine.common.cache'));
    $instance->setQueryCacheImpl($this->getService('doctrine.common.cache'));

    return $this->shared['doctrine.orm.configuration'] = $instance;
  }

  protected function getDoctrine_Orm_EntitymanagerService()
  {
    if (isset($this->shared['doctrine.orm.entitymanager'])) return $this->shared['doctrine.orm.entitymanager'];

    $instance = call_user_func(array('Doctrine\\ORM\\EntityManager', 'create'), $this->getParameter('doctrine.connection.options'), $this->getService('doctrine.orm.configuration'));

    return $this->shared['doctrine.orm.entitymanager'] = $instance;
  }
}
