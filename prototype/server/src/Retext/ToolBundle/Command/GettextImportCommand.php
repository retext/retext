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
 * Mit diesem Command kann von der Komandozeile aus ein Demo-Projekt angelegt werden.
 */
class GettextImportCommand extends Command
{
    /**
     * @var \Retext\ApiBundle\ApiClient
     */
    private $client;

    protected function configure()
    {
        $this
            ->setName('retext:tools:gettext:import')
            ->addArgument('project', InputArgument::REQUIRED, 'API-URL des Projects (@subject)')
            ->addArgument('dir', InputArgument::REQUIRED, 'Verzeichnis mit Gettext-Dateien')
            ->addArgument('domain', InputArgument::OPTIONAL, 'Gettext-Domain', 'messages')
            ->addArgument('email', InputArgument::OPTIONAL, 'E-Mail-Adresse des Nutzers', 'm@tckr.cc')
            ->setDescription('Befüllt ein Projekt mit Daten aus einer gettext-Datei');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('dir');
        if (substr($dir, -1) !== DIRECTORY_SEPARATOR) $dir .= DIRECTORY_SEPARATOR;
        $domain = $input->getArgument('domain');
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

        $output->write("Fetching root container … ");
        $root = $this->client->GET($this->client->getRelationHref($project, 'http://jsonld.retext.it/Container', false, 'http://jsonld.retext.it/ontology/root'));
        $output->writeln("<info>OK</info>");

        $file = $dir . $project->defaultLanguage .  DIRECTORY_SEPARATOR . 'LC_MESSAGES' . DIRECTORY_SEPARATOR . $domain . '.po';

        $messages = Parser::parse(new \SplFileInfo($file));

        $output->write("Creating texts ");
        foreach ($messages as $message) {

            // Lege übergeordnete Container an
            $hierarchy = array_map(function($el)
            {
                return trim($el);
            }, explode('/', $message->comment));
            $parent = $root;
            while (count($hierarchy) > 1) {
                $childName = array_shift($hierarchy);
                $childRel = $this->client->getRelationHref($parent, 'http://jsonld.retext.it/Container', true, 'http://jsonld.retext.it/ontology/child');
                $childContainers = $this->client->GET($childRel);
                $existingChildContainer = array_filter($childContainers, function($childContainer) use($childName)
                {
                    return $childContainer->name == $childName;
                });
                if (empty($existingChildContainer)) {
                    parse_str(parse_url($childRel, PHP_URL_QUERY), $containerRelationParams); // Convert GET to POST
                    $parent = $this->client->POST($childRel, array_merge($containerRelationParams, array('name' => $childName)));
                } else {
                    $parent = array_shift($existingChildContainer);
                }
            }

            // Text anlegen
            $textRelation = $this->client->getRelationHref($parent, 'http://jsonld.retext.it/Text', true, 'http://jsonld.retext.it/ontology/child');
            parse_str(parse_url($textRelation, PHP_URL_QUERY), $textRelationParams); // Convert GET to POST
            $this->client->POST($textRelation, array_merge($textRelationParams, array('identifier' => $message->msgid, 'text' => $message->texts, 'name' => array_shift($hierarchy))));
            $output->write(".");
        }
        $output->writeln(" <info>OK</info>");
    }
}