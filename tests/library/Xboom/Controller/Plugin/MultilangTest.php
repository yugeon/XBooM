<?php
/**
 * Description of Multilang
 *
 * @author yugeon
 */
class Xboom_Controller_Plugin_MultilangTest extends ControllerTestCase
{
    protected $_options;

    public function setUp()
    {
        parent::setUp();
        $this->_options = $this->application->getOption('resources');
    }
    public function testRuLanguageInUrl()
    {
        $_SERVER['HTTP_HOST'] = 'xboom.local';
        $this->dispatch('/ru');
        $this->assertTrue(Zend_Registry::get('lang') === 'ru');
        $this->assertResponseCode(200);
    }

    public function testNoLanguageInUrl()
    {
        $_SERVER['HTTP_HOST'] = 'xboom.local';
        // this test break dispatch loop if GET-request
        $this->getRequest()->setMethod('POST');
        $this->dispatch('');
        $this->assertTrue(Zend_Registry::get('lang') 
                === $this->_options['multilang']['default']);
        $this->assertResponseCode(200);
    }
}
?>
