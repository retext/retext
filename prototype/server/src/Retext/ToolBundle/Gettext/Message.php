<?php

namespace Retext\ToolBundle\Gettext;

class Message
{
    public $msgid = '';
    public $comment = '';
    public $texts = array();

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