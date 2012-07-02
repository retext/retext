<?php

namespace Retext\ToolBundle\Gettext;

class Message
{
    public $msgid = '';
    public $msgstr = '';
    public $comment = '';

    public function addComment($comment)
    {
        if (!empty($this->comment)) $this->comment .= "\n";
        $this->comment .= $comment;
    }

    public function addString($msgstr)
    {
        if (!empty($this->msgstr)) $this->msgstr .= "\n";
        $this->msgstr .= str_replace('\n', "\n", $msgstr);
    }

}