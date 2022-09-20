<?php

namespace dokuwiki\plugin\controlpage;

use dokuwiki\File\PageResolver;

class ControlPage
{

    protected $pages = [];
    protected $top;

    /**
     * Parse the control page
     *
     * @param string $controlPage
     */
    public function __construct($controlPage)
    {
        $this->top = new Top();
        $instructions = p_cached_instructions(wikiFN($controlPage));
        if (!$instructions) {
            throw new \RuntimeException('No instructions for control page found');
        }

        $parents = [];
        $lastpage = $this->top;

        $resolver = new PageResolver($controlPage);

        foreach ($instructions as $instruction) {
            switch ($instruction[0]) {
                case 'listu_open':
                    array_unshift($parents, $lastpage); // last used page is added as parent
                    break;
                case 'listu_close':
                    array_shift($parents); // remove one parent level
                    break;
                case 'internallink':
                case 'externallink':
                    if ($instruction[0] == 'internallink') {
                        $newpage = new InternalPage(
                            $resolver->resolveId($instruction[1][0]),
                            $instruction[1][1]
                        );
                    } else {
                        $newpage = new ExternalPage(
                            $instruction[1][0],
                            $instruction[1][1]
                        );
                    }
                    $newpage->setParents($parents);
                    $parents[0]->addChild($newpage);
                    $this->pages[$newpage->getId()] = $newpage;
                    $lastpage = $newpage;
                    break;
            }
        }
    }

    /**
     * Access the top element
     *
     * Get it's children to iterate over the page hierarchy
     *
     * @return Top
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * Find the given page in the hierarchy
     *
     * If found, the page can be used to set the open/close state in nested lists
     *
     * @param string $id
     * @return Page|null
     * @todo add second parameter to try upper NS start pages
     */
    public function findPage($id)
    {
        if (isset($this->pages[$id])) return $this->pages[$id];
        return null;
    }
}
