<?php

namespace Retext\ToolBundle\Gettext;

class Parser
{
    /**
     * @var \Retext\ToolBundle\Gettext\Message[]
     */
    private $messages = array();

    /**
     * @var \Retext\ToolBundle\Gettext\Message
     */
    private $currentMessage;

    /**
     * @var\SplFileInfo $file
     */
    private $file;

    /**
     * @static
     * @param \SplFileInfo $file
     * @return \Retext\ToolBundle\Gettext\Message[]
     */
    public static function parse(\SplFileInfo $file)
    {
        $p = new Parser();
        return $p->getMessagesFromFile($file);
    }

    /**
     * @param \SplFileInfo $file
     * @return \Retext\ToolBundle\Gettext\Message[]
     */
    public function getMessagesFromFile(\SplFileInfo $file)
    {
        $this->file = $file;
        $this->currentMessage = new Message();
        foreach (file($file->getPathname()) as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            if (substr($line, 0, 1) == '#') {
                if (!empty($this->currentMessage->msgid)) {
                    $this->messages[] = $this->currentMessage;
                    $this->currentMessage = new Message();
                }
                $this->currentMessage->addComment(trim(substr($line, 1)));
            } else if (preg_match('/^msgid ?"([^"]+)"/', $line, $match)) {
                $this->currentMessage->msgid = $match[1];
            } else if (preg_match('/^msgstr ?"([^"]+)"/', $line, $match)) {
                $this->currentMessage->addString($match[1]);
            }
        }
        $this->messages[] = $this->currentMessage;
        return $this->messages;
    }
}