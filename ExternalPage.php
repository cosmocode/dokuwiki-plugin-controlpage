<?php

namespace dokuwiki\plugin\controlpage;

class ExternalPage extends Page
{
    /** @inheritDoc */
    public function getHtmlLink()
    {
        $attr = buildAttributes([
            'href' => $this->getId(),
            'title' => $this->getId(),
            'class' => 'external',
        ]);
        return "<a $attr>" . hsc($this->getTitle()) . '</a>';
    }
}
