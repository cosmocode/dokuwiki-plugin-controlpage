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
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        saveWikiText('simple', file_get_contents(__DIR__ . '/simple.txt'), 'test');
        saveWikiText('foo:complex', file_get_contents(__DIR__ . '/complex.txt'), 'test');
    }

    public function testSimpleParsing()
    {
        $control = new ControlPage('simple');
        $top = $control->getTop();

        $this->assertEquals(4, count($top->getChildren()));
        $this->assertEquals(1, count($top->getChildren()[0]->getParents()));
        $this->assertEquals(4, count($top->getChildren()[1]->getSiblings()));
        $this->assertEquals(8, count($top->getChildren()[1]->getChildren()));

        $this->assertEquals(12, count($control->getAll()));
        $this->assertEquals(11, count($control->getLeaves()));
        $this->assertEquals(1, count($control->getBranches()));
    }


    /**
     * Parse the complex example with different flags
     *
     * @return array[]
     * @see testComplexParsing
     */
    public function complexProvider()
    {
        return [
            [0, // no flags
                [
                    'content',
                    'foo:this',
                    'foo:bar',
                    'foo:another_link',
                    'https://www.google.com',
                    'relativeup',
                    'foo2:this',
                    'foo:blarg:down',
                    'toplevel',
                    'foo:link',
                ]
            ],
            [ControlPage::FLAG_NOEXTERNAL,
                [
                    'content',
                    'foo:this',
                    'foo:bar',
                    'foo:another_link',
                    'relativeup',
                    'foo2:this',
                    'foo:blarg:down',
                    'toplevel',
                    'foo:link',
                ]
            ],
            [ControlPage::FLAG_NOINTERNAL,
                [
                    'https://www.google.com',
                ]
            ],
        ];
    }

    /**
     * @dataProvider complexProvider
     * @param int $flags
     * @param array $expect
     * @return void
     */
    public function testComplexParsing($flags, $expect)
    {
        $control = new ControlPage('foo:complex', $flags);
        $this->assertEquals($expect, array_keys($control->getAll()));
    }

    public function testNonExisting()
    {
        $this->expectException(\RuntimeException::class);
        $control = new ControlPage('does:not:exist');
        $foo = $control->getAll();
    }
}
