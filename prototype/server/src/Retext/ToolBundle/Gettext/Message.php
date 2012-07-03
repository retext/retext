<?php

namespace Retext\ToolBundle\Gettext;

/**
 * Repräsentiert eine Gettex-Message
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class Message
{
    public $msgid = '';
    public $comment = '';
    public $texts = array();

    /**
     * Fügt der Message einen Kommentar hinzu
     *
     * @param $comment
     */
    public function addComment($comment)
    {
        if (!empty($this->comment)) $this->comment .= "\n";
        $this->comment .= $comment;
    }

    public function addString($lang, $msgstr)
    {
        if (!isset($this->texts[$lang])) $this->texts[$lang] = '';
        if (!empty($this->texts[$lang])) $this->texts[$lang] .= "\n";
        $this->texts[$lang] .= str_replace('\n', "\n", $msgstr);
    }
}