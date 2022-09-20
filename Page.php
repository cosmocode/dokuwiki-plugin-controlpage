<?php

namespace dokuwiki\plugin\controlpage;

abstract class Page
{
    protected $id = '';
    protected $title = '';

    protected $parents = [];
    protected $children = [];

    /**
     * @param string $id The pageID or the external URL
     * @param string $title The title as given in the link
     */
    public function __construct($id, $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return a HTML link for this page
     *
     * @return string
     */
    abstract public function getHtmlLink();

    /**
     * Get all Pages on the same level
     * @return Page[]
     */
    public function getSiblings()
    {
        return $this->parents[0]->getChildren();
    }

    /**
     * Get all sub pages, may return an empty array
     *
     * @return Page[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get all parent pages in reverse order
     *
     * @return Page[]
     * @todo currently returns at least Top
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * @param Page[] $parents
     * @return void
     */
    public function setParents($parents)
    {
        $this->parents = $parents;
    }

    /**
     * @param Page $child
     * @return void
     */
    public function addChild($child)
    {
        $this->children[] = $child;
    }

}
