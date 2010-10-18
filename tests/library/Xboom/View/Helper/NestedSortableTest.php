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
 * Test case for NestedSortable view helper
 *
 * @author yugeon
 */

namespace test\Xboom\View\Helper;

require_once 'Xboom/View/Helper/NestedSortable.php';

use \Mockery as m;

class NestedSortableTest extends \PHPUnit_Framework_TestCase
{

    protected $object;
    /**
     *
     * @var RecursiveIterator
     */
    protected $items;

    public function setUp()
    {
        $view = m::mock('Zend_View_Abstract');
        $view->shouldReceive('getPluginLoader')->andReturn($view);
        $view->shouldReceive('getPaths')->andReturn(true);
        $view->shouldReceive('JQuery', 'enable', 'uiEnable', 'addOnLoad',
                'headScript', 'appendFile')->andReturn($view);
        $view->shouldReceive('escape')->andReturnUsing(function($p){return $p;});

        $this->items = new \Zend_Navigation(array(
            array(
                'id' => 34,
                'label' => 'Page 1',
                'uri' => '#'
            ),
            array(
                'label' => 'Page 2',
                'uri' => '#',
                'pages' =>
                    array(
                        array(
                            'label' => 'Page 2.1',
                            'uri' => '#'
                        ),
                        array(
                            'label' => 'Page 2.2',
                            'uri' => '#'
                        )
                    )
            ),
            array(
                'label' => 'Page 3',
                'uri' => '#'
            )
        ));

        $this->object = new \Xboom_View_Helper_NestedSortable();
        $this->object->setView($view);
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

    public function testCanInit()
    {
        $items = m::mock('RecursiveIterator');
        $ulClass = 'customUlClass';
        $liClass = 'customLiClass';

        $this->object->nestedSortable($items, $ulClass, $liClass);

        $this->assertEquals($items, $this->object->getItems());
        $this->assertEquals($ulClass, $this->object->getUlClass());
        $this->assertEquals($liClass, $this->object->getLiClass());
    }

    public function testCanSetIndentAsInteger()
    {
        $this->object->setIndent(4);
        $this->assertEquals('    ', $this->object->getIndent());
    }

    public function testCanSetIndentAsString()
    {
        $this->object->setIndent('    ');
        $this->assertEquals('    ', $this->object->getIndent());
    }

    public function testCanSetScritpPathToNestesSorterJQueryPlugin()
    {
        $myPath = '/js/jquery/nestesSorter.js';
        $this->object->setScriptPath($myPath);
        $this->assertEquals($myPath, $this->object->getScriptPath());
    }

    public function testEmptyItemsShouldReturnEmptyString()
    {
        $this->assertEquals('', $this->object->render());
    }

    public function testCanRender()
    {
        $this->object->nestedSortable($this->items, 'customUlCss', 'customLiCss');
        $result = $this->object->render();
        $this->assertContains('<ul class="customUlCss">', $result);
        $this->assertContains('<li id="customLiCss_34">', $result);
        $this->assertContains('<li>', $result);
        $this->assertContains('<div class="ui-state-default">Page 1</div>', $result);
        $this->assertContains('<div class="ui-state-default">Page 2</div>', $result);
        $this->assertContains('<div class="ui-state-default">Page 2.1</div>', $result);
        $this->assertContains('<div class="ui-state-default">Page 2.2</div>', $result);
        $this->assertContains('<div class="ui-state-default">Page 3</div>', $result);
    }
}
