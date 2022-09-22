<?php

namespace dokuwiki\plugin\controlpage;

class Top extends Page
{
    public function __construct()
    {
        parent::__construct('', '');
    }

    /**
     * Always returns an empty array
     * @inheritdoc
     */
    public function getSiblings()
    {
        return [];
    }

    /**
     * Always returns an empty array
     * @inheritdoc
     */
    public function getParents()
    {
        return [];
    }

    /** @inheritdoc */
    public function getHtmlLink()
    {
        return '';
    }
}
