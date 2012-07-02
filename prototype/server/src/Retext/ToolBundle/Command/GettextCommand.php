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
class GettextCommand extends Command
{
    /**
     * @var \Retext\ApiBundle\ApiClient
     */
    private $client;

    protected function configure()
    {
        $this
            ->setName('retext:tools:gettext')
            ->addArgument('project', InputArgument::REQUIRED, 'API-URL des Projects (@subject)')
            ->addArgument('file', InputArgument::REQUIRED, 'Gettext-Datei')
            ->addArgument('email', InputArgument::OPTIONAL, 'E-Mail-Adresse des Nutzers', 'm@tckr.cc')
            ->setDescription('Befüllt ein Projekt mit Daten aus einer gettext-Datei');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
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

        $messages = Parser::parse(new \SplFileInfo($file));
        $textRelation = $this->client->getRelationHref($root, 'http://jsonld.retext.it/Text', true, 'http://jsonld.retext.it/ontology/children');
        $output->write("Creating texts ");
        foreach ($messages as $message) {
            parse_str(parse_url($textRelation, PHP_URL_QUERY), $textRelationParams); // Convert GET to POST
            $this->client->POST($textRelation, array_merge($textRelationParams, array('identifier' => $message->msgid, 'text' => $message->msgstr, 'name' => $message->comment)));
            $output->write(".");
        }
        $output->writeln('');
        $output->writeln("<info>OK</info>");
    }
}