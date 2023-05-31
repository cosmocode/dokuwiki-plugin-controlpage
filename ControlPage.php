<?php

namespace dokuwiki\plugin\controlpage;

use dokuwiki\File\PageResolver;

class ControlPage implements \JsonSerializable
{
    /** @var int do not include internal links */
    const FLAG_NOINTERNAL = 1;
    /** @var int do not include external links */
    const FLAG_NOEXTERNAL = 2;

    /** @var Page[] */
    protected $pages = [];
    /** @var Top */
    protected $top;

    /**
     * Parse the control page
     *
     * Check the flag constants on how to influence the behaviour
     *
     * @param string $controlPage
     * @param int $flags
     * @param callable|null $propertyBuilder A callback to set additional properties on the pages
     */
    public function __construct($controlPage, $flags = 0, $propertyBuilder = null)
    {
        $this->top = new Top();
        $instructions = p_cached_instructions(wikiFN($controlPage));
        if (!$instructions) {
            throw new \RuntimeException('No instructions for control page found');
        }

        $parents = [];
        $lastpage = '';

        $resolver = new PageResolver($controlPage);

        foreach ($instructions as $instruction) {
            switch ($instruction[0]) {
                case 'listu_open':
                    if (!$parents) $parents = [$this->top]; // always start at the top element
                    if ($lastpage) array_unshift($parents, $lastpage); // last used page is added as parent
                    break;
                case 'listu_close':
                    array_shift($parents); // remove one parent level
                    break;
                case 'internallink':
                case 'externallink':
                    if ($instruction[0] == 'internallink') {
                        if ($flags & self::FLAG_NOINTERNAL) break;

                        $newpage = new InternalPage(
                            $resolver->resolveId($instruction[1][0]),
                            $instruction[1][1]
                        );
                    } else {
                        if ($flags & self::FLAG_NOEXTERNAL) break;

                        $newpage = new ExternalPage(
                            $instruction[1][0],
                            $instruction[1][1]
                        );
                    }
                    if (!$parents) $parents = [$this->top]; // all dangling links go to the top
                    $newpage->setParents($parents);
                    $parents[0]->addChild($newpage);
                    $this->pages[$newpage->getId()] = $newpage;
                    if (is_callable($propertyBuilder)) $propertyBuilder($newpage);
                    $lastpage = $newpage;
                    break;
            }
        }

        // sort pages so children can be processed before parents (or other way round)
        uasort($this->pages, [$this, 'sortByDepth']);
    }

    /**
     * Access the top element
     *
     * Use it's children to iterate over the page hierarchy
     *
     * @return Top
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * Get a flat list of all linked pages
     *
     * @return Page[]
     */
    public function getAll()
    {
        return $this->pages;
    }

    /**
     * Get a flat list of all linked pages that do NOT have children
     *
     * @return Page[]
     */
    public function getLeaves()
    {
        return array_filter($this->pages, function ($page) {
            return !$page->getChildren();
        });
    }

    /**
     * Get a flat list of all linked pages that DO have children
     *
     * @return Page[]
     */
    public function getBranches()
    {
        return array_filter($this->pages, function ($page) {
            return !!$page->getChildren();
        });
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

    /**
     * Custom sorter to sort pages by their depth and then alphabetically
     *
     * @param Page $a
     * @param Page $b
     * @return int
     */
    protected function sortByDepth($a, $b)
    {

        $res = count($a->getParents()) - count($b->getParents());
        if ($res === 0) {
            $res = strcmp($a->getId(), $b->getId());
        }
        return $res;
    }

    /**
     * @inheritdoc
     *
     * Gets all pages as a flat list, with the top pseudo element as empty string key. It's children property
     * can be used to iterate over the page hierarchy. All other pages can be looked up by their ID.
     */
    public function jsonSerialize()
    {
        return array_merge(['' => $this->getTop()], $this->getAll());
    }
}
