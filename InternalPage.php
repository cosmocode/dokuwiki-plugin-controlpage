<?php

namespace dokuwiki\plugin\controlpage;

class InternalPage extends Page
{
    /** @inheritDoc */
    public function getHtmlLink()
    {
        return html_wikilink($this->getId(), $this->getTitle());
    }
}
