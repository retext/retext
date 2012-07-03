<?php

namespace Retext\ToolBundle\Command;

use Retext\ApiBundle\ApiClient;
use Retext\ToolBundle\Gettext\Message;
use Retext\ToolBundle\Gettext\Parser;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Mit diesem Command kann von der Komandozeile ein nach Gettext exportiert werden
 */
class GettextExportCommand extends Command
{
    /**
     * @var \Retext\ApiBundle\ApiClient
     */
    private $client;

    private $dir;
    private $domain;
    private $fps = array();

    protected function configure()
    {
        $this
            ->setName('retext:tools:gettext:export')
            ->addArgument('project', InputArgument::REQUIRED, 'API-URL des Projects (@subject)')
            ->addArgument('dir', InputArgument::REQUIRED, 'Verzeichnis mit Gettext-Dateien')
            ->addArgument('domain', InputArgument::OPTIONAL, 'Gettext-Domain', 'messages')
            ->addArgument('email', InputArgument::OPTIONAL, 'E-Mail-Adresse des Nutzers', 'm@tckr.cc')
            ->setDescription('Exportiert ein Projekt nach gettext');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dir = $input->getArgument('dir');
        if (substr($this->dir, -1) !== DIRECTORY_SEPARATOR) $this->dir .= DIRECTORY_SEPARATOR;
        $this->domain = $input->getArgument('domain');
        $projectSubject = $input->getArgument('project');
        $email = $input->getArgument('email');

        $this->client = new ApiClient();
        $urlparts = parse_url($projectSubject);
        $this->client->setApiHost($urlparts['scheme'] . '://' . $urlparts['host']);

        $output->write("Login … ");
        $this->client->POST('/api/login', array('email' => $email, 'password' => $email));
        $output->writeln("<info>OK</info>");

        $projectSubjectPath = str_replace($this->client->getApiHost(), "", $projectSubject);
        $output->write("Fetching project from $projectSubjectPath … ");
        $project = $this->client->GET($projectSubjectPath);
        $output->writeln("<info>OK</info>");

        $output->write("Fetching project tree … ");
        $tree = $this->client->GET($this->client->getRelationHref($project, 'http://jsonld.retext.it/Element', true, 'http://jsonld.retext.it/ontology/tree'));
        $output->writeln("<info>OK</info>");

        $this->writeTree($tree);

        array_map(function($fp)
        {
            fclose($fp);
        }, $this->fps);
    }

    protected function writeTree(array $trunk, $parent = null)
    {
        $messages = array();
        foreach ($trunk as $leaf) {
            if ($leaf->data->{'@context'} == 'http://jsonld.retext.it/Container') {
                $this->writeTree($leaf->children, $parent . $leaf->data->name . ' / ');
            } else {
                $message = new Message();
                $message->msgid = $leaf->data->identifier;
                $message->comment = $parent . $leaf->data->name;
                foreach ($leaf->data->text as $lang => $t) $message->addString($lang, $t);
                $messages[] = $message;
            }
        }
        foreach ($messages as $message) {
            foreach ($message->texts as $lang => $text) {
                if (!isset($this->fps[$lang])) {
                    $this->fps[$lang] = fopen($this->dir . $lang . DIRECTORY_SEPARATOR . 'LC_MESSAGES' . DIRECTORY_SEPARATOR . $this->domain . '.po', 'w+');
                }
                fputs($this->fps[$lang], sprintf("# %s\nmsgid \"%s\"\nmsgstr \"%s\"\n\n", $message->comment, $message->msgid, str_replace("\n", '\n', $text)));
            }
        }

    }
}