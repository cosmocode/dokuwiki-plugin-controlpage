<?php

namespace dokuwiki\plugin\controlpage\test;

use dokuwiki\plugin\controlpage\ControlPage;
use DokuWikiTest;

/**
 * Tests for the controlpage plugin
 *
 * @group plugin_controlpage
 * @group plugins
 */
class ParsingTest extends DokuWikiTest
{
    public function testParsing()
    {
        saveWikiText('navigation', file_get_contents(__DIR__ . '/navigation.txt'), 'test');

        $control = new ControlPage('navigation');
        $top = $control->getTop();

        $this->assertEquals(4, count($top->getChildren()));
        $this->assertEquals(1, count($top->getChildren()[0]->getParents()));
        $this->assertEquals(4, count($top->getChildren()[1]->getSiblings()));
        $this->assertEquals(8, count($top->getChildren()[1]->getChildren()));

        $this->assertEquals(12, count($control->getAll()));
        $this->assertEquals(11, count($control->getLeaves()));
        $this->assertEquals(1, count($control->getBranches()));
    }
}
