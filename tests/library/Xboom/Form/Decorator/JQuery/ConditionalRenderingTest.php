<?php
/**
 *  CMF for web applications based on Zend Framework 1 and Doctrine 2
 *  Copyright (C) 2010  Eugene Gruzdev aka yugeon
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright  Copyright (c) 2010 yugeon <yugeon.ru@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html  GNU GPLv3
 */

/**
 * Description of ConditionalRenderingTest
 *
 * @author yugeon
 */

namespace test\Xboom\Form\Decorator\JQuery;

use \Xboom\Form\Decorator\JQuery\ConditionalRendering,
 \Mockery as m;

class ConditionalRenderingTest extends \PHPUnit_Framework_TestCase
{

    protected $object;

    public function setUp()
    {
        $view = m::mock('Zend_View_Interface');
        $view->shouldReceive('getPluginLoader')->andReturn($view);
        $view->shouldReceive('getPaths')->andReturn(true);
        $view->shouldReceive('JQuery')->andReturn($view);
        $view->shouldReceive('addOnLoad')->andReturn($view);

        $element = m::mock('Zend_Form_DisplayGroup');
        $element->shouldReceive('getView')->andReturn($view);
        $element->shouldReceive('getId')->andReturn('id_of_group_element');

        $this->object = new ConditionalRendering;
        $this->object->setElement($element);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }


    public function testCanCreateTestObject()
    {
        $this->assertNotNull($this->object);
    }

    public function testRender()
    {
        $content = 'Some content';
        $options = array(
            'actuatorId' => 'actuator-id',
            'actuatorValue' => 'actuator-value',
            'id' => 'dependend-id'
        );
        $this->object->setOptions($options);

        $expected = '<div id="condRend-dependend-id">' . $content . '</div>';
        $this->assertEquals($expected, $this->object->render($content));
    }

    public function testInvalidOptionsShouldReturnContentWithoutChanges()
    {
        $content = 'Some content';

        $expected = $content;
        $this->assertEquals($expected, $this->object->render($content));
    }
}
