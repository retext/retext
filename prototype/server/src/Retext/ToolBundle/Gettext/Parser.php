<?php

namespace Retext\ToolBundle\Gettext;

/**
 * Einfacher Parser für Gettext-Dateien
 *
 * @author Markus Tacker <m@tckr.cc>
 */
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
        $parts = explode(DIRECTORY_SEPARATOR, $file->getPath());
        $lang = $parts[count($parts) - 2];

        foreach (file($file->getPathname()) as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            if (substr($line, 0, 1) == '#') {
                if (!empty($this->currentMessage->msgid)) {
                    $this->messages[$this->currentMessage->msgid] = $this->currentMessage;
                    $this->currentMessage = new Message();
                }
                $this->currentMessage->addComment(trim(substr($line, 1)));
            } else if (preg_match('/^msgid ?"([^"]+)"/', $line, $match)) {

                $this->currentMessage->msgid = $match[1];
            } else if (preg_match('/^msgstr ?"([^"]+)"/', $line, $match)) {
                $this->currentMessage->addString($lang, $match[1]);
            }
        }
        $this->messages[$this->currentMessage->msgid] = $this->currentMessage;

        // Parse other gettext files
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($file->getPath() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR)) as $otherFile) {
            if (!$otherFile->isFile()) continue;
            if ($otherFile->isDir()) continue;
            if (!$otherFile->isFile() && $otherFile->isDot()) continue;
            if ($otherFile->getBaseName() !== $file->getBasename()) continue;
            if (realpath($otherFile->getPathName()) == realpath($file->getPathName())) continue;
            $parts = explode(DIRECTORY_SEPARATOR, $otherFile->getPath());
            $lang = $parts[count($parts) - 2];

            $msgid = null;
            foreach (file($otherFile->getPathname()) as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                if (preg_match('/^msgid ?"([^"]+)"/', $line, $match)) {
                    $msgid = $match[1];
                } else if (preg_match('/^msgstr ?"([^"]+)"/', $line, $match)) {
                    $this->messages[$msgid]->addString($lang, $match[1]);
                }
            }
        }

        return $this->messages;
    }
}