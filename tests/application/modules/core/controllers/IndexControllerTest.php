<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StatsTest
 *
 * @group functional
 * @author yugeon
 */
class Core_IndexControllerTest extends ControllerTestCase
{
    public function testCanGetDefaultPage()
    {
        $_SERVER['HTTP_HOST'] = 'xboom.local';
        $this->dispatch('/ru');
        $this->assertTrue(Zend_Registry::get('lang') === 'ru');
        $this->assertModule('core');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertResponseCode(200);
    }
}
?>
