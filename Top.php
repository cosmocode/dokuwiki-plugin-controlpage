<?php

namespace dokuwiki\plugin\controlpage;

class Top extends Page
{
    public function __construct()
    {
        parent::__construct('', '');
    }

    /**
     * @inheritdoc
     * @throws \Exception cannot be called for top element
     */
    public function getSiblings()
    {
        throw new \Exception('No siblings for top element');
    }

    /**
     * @inheritdoc
     * @throws \Exception cannot be called for top element
     */
    public function getParents()
    {
        throw new \Exception('No parents for top element');
    }

    /** @inheritdoc */
    public function getHtmlLink()
    {
        return '';
    }
}
