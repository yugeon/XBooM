<?php
/**
 * Resource for initializing front controller plugin.
 *
 * @category   Multilang
 * @package    Multilang_Application
 * @subpackage Resources
 *
 * @author  yugeon
 * @version    SVN: $Id$
 */
class Xboom_Application_Resource_Multilang extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $options = $this->getOptions();
        $front = $this->getBootstrap()->bootstrap('frontcontroller')->getResource('frontcontroller');
        $plugin = new \Xboom\Controller\Plugin\Multilang(
                isset($options['default'])? $options['default'] : '',
                isset($options['locales'])? $options['locales'] : array(),
                isset($options['redirectIfLangNotPresent'])? $options['redirectIfLangNotPresent'] : false
                );
        // TODO stackIndex value from config?
        $stackIndex = null;
        $front->registerPlugin($plugin, $stackIndex);
    }
}