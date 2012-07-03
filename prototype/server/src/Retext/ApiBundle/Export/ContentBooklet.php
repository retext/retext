<?php

namespace Retext\ApiBundle\Export;

use Retext\ApiBundle\Document\Project, Retext\ApiBundle\Document\Container;

use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Exportiert ein Projekt als Content-Booklet, das alle Texte strukturiert enthält.
 * Der Export ist als HTML oder PDF möglich.
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class ContentBooklet
{
    const CSS = "* { box-sizing: border-box; }
    div { border: 3px solid #000; margin: 1em; }
    div.rootLevel { border: 3px solid #000; margin: 2em 1em 1em 1em; position: relative; border-color: rgb(175, 175, 175); page-break-after: always; }
    h1, h2, h3, h4, h5, h6 { margin: 0.5em; padding: 0; }
    p, dl { margin: 1em; width: 100%; }
    blockquote { margin: 0; }
    blockquote p { margin: 0 0 1em 0.5em; }
    table { width: 98%; margin: 1%; page-break-inside:avoid !important; }
    th, td { padding: 0.25em; vertical-align: top; }
    th { text-align: right; }
    blockquote { border-left: 3mm solid #ddd; }
    hr { border: 0; border-bottom: 1px solid rgb(175, 175, 175); }
    .left { text-align: left; }
    ";
    private $body;

    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    private $dm;

    /**
     * @var \Retext\ApiBundle\Export\ContainerChildren
     */
    private $containerChildrenExporter;

    public function __construct(DocumentManager $dm, ContainerChildren $containerChildrenExporter)
    {
        $this->dm = $dm;
        $this->containerChildrenExporter = $containerChildrenExporter;
    }

    public function exportBookletAsHTML(Project $project)
    {
        $rootContainer = $project->getRootContainer();
        $this->body .= sprintf('<h1>Content-Booklet: %s</h1>', $project->getName());
        $now = new \DateTime();
        $this->body .= sprintf('<p>Datum: %s</d>', $now->format('d.m.Y'));
        $this->addContainer($rootContainer);
        return '<html lang="de"><head><meta charset="utf-8"><title>' . $project->getName() . '</title><style>' . file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'normalize.css') . "\n\n" . self::CSS . '</style><body>' . $this->body . '</body></html>';
    }


    protected function addContainer(Container $container, $parent = null, $level = null)
    {
        if ($level === null) $level = 0;
        $headline = true;
        $gray = min(255, 175 + $level * 15);
        if ($level === 0) $headline = false;
        if ($level == 1) {
            $this->body .= sprintf('<div class="rootlevel">');
        } else if ($level > 1) {
            $this->body .= sprintf('<div style="border-color: rgb(%d, %d, %d);">', $gray, $gray, $gray);
        }
        if ($headline) {
            $this->body .= sprintf('<h%d><em>Abschnitt:</em> %s</h%d>', min(6, $level), $container->getName(), min(6, $level));
            $this->body .= sprintf('<table><colgroup><col width="10%%"><col width="80%%"></colgroup><tbody><tr><th>ID</th><td>%s</td></tr><tr><th>Pfad</th><td>%s</td></tr><tr><th>Unterelemente</th><td>%d</td></tr></tbody></table>', $container->getId(), $parent . ' ' . $container->getName(), $container->getChildCount());
        }
        foreach ($this->containerChildrenExporter->getChildren($container) as $element) {
            if ($element instanceof \Retext\ApiBundle\Document\Text) {
                $text = $element->getText();
                if ($element->getType()->getMultiline()) {
                    $text = empty($text) ? array() : array_map(function($t)
                    {
                        return str_replace("\n", "</p><p>", $t);
                    }, $text);
                }
                $this->body .= sprintf('<hr style="border-color: rgb(%d, %d, %d);">', $gray, $gray, $gray);
                $this->body .= sprintf('<h%d><em>Text:</em> %s</h%d>', min(6, $level + 1), $element->getName(), min(6, $level + 1));
                $this->body .= sprintf('<table><colgroup><col width="10%%"><col width="20%%"><col width="70%%"></colgroup><tbody>
                <tr><th>ID</th><td>%s</td><td rowspan="3">{TEXT}</td></tr>
                <tr><th>Typ</th><td>%s (%s, %d%%, %s)</td></tr>
                </tbody></table>
                ', $element->getId(), $element->getType()->getName(), $element->getType()->getFontname(), $element->getType()->getFontsize(), $element->getType()->getMultiline() ? 'mehrzeilig' : 'einzeilig');
                $texts = '';
                if (count($text) < 2) {
                    $texts = sprintf('<blockquote><p>%s</p></blockquote>', empty($text) ? '' : array_shift($text));
                } else {
                    foreach($text as $lang => $t) {
                        $texts .= sprintf('<h%d>%s</h%d>', min(6, $level + 1), $lang, min(6, $level + 1));
                        $texts .= sprintf('<blockquote><p>%s</p></blockquote>', $t);
                    }
                }
                $this->body = str_replace('{TEXT}', $texts, $this->body);
            } else { // if ($element instanceof \Retext\ApiBundle\Document\Container) {
                $this->addContainer($element, $parent . ' ' . $container->getName() . ' /', $level + 1);
            }
        }
        $this->body .= sprintf('</div>');
    }
}
