<?php

namespace Retext\ApiBundle\Command;

use Retext\ApiBundle\ApiClient;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Mit diesem Command kann von der Komandozeile aus ein Demo-Projekt angelegt werden.
 */
class DemoDataCommand extends Command
{
    /**
     * @var \Retext\ApiBundle\ApiClient
     */
    private $client;

    protected function configure()
    {
        $this
            ->setName('retext:api:demodata')
            ->addArgument('apihost', InputArgument::REQUIRED, 'API-URL')
            ->addArgument('email', InputArgument::OPTIONAL, 'E-Mail-Adresse des Nutzers', 'm@tckr.cc')
            ->setDescription('Erstellt ein Beispiel-Projekt');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->client = new ApiClient();
        $this->client->setApiHost($input->getArgument('apihost'));
        $email = $input->getArgument('email');

        $output->write("Prüfe Verbindung … ");
        $status = $this->client->GET('/api/status');
        assert('$status->version == 1;');
        $output->writeln("<info>OK</info>");

        // Create User
        $output->write("Lege Nutzer $email an … ");
        $this->client->POST('/api/user', array('email' => $email));
        $output->writeln("<info>OK</info>");

        // Login
        $output->write("Login … ");
        $this->client->POST('/api/login', array('email' => $email, 'password' => $email));
        $output->writeln("<info>OK</info>");

        // Create Project
        $output->write("Projekt anlegen … ");
        $project = $this->client->POST('/api/project', array("name" => "mi.info 11.12"));
        $output->writeln("<info>OK</info>");

        // Create Container
        $output->write("Container anlegen ");
        $titel = $this->client->POST('/api/container', array("name" => "Titel", 'parent' => $project->rootContainer));
        $output->write(".");
        $vorwort = $this->client->POST('/api/container', array("name" => "Vorwort", 'parent' => $project->rootContainer));
        $output->write(".");
        $kurzinfo = $this->client->POST('/api/container', array("name" => "Kurzinfo", 'parent' => $project->rootContainer));
        $output->write(".");
        $trennblatt = $this->client->POST('/api/container', array("name" => "Trennblatt", 'parent' => $kurzinfo->id));
        $output->write(".");
        $studiengang = $this->client->POST('/api/container', array("name" => "Studiengang", 'parent' => $kurzinfo->id));
        $output->write(".");
        $mi = $this->client->POST('/api/container', array("name" => "Medieninformatik", 'parent' => $kurzinfo->id));
        $output->write(".");
        $p13 = $this->client->POST('/api/container', array("name" => "Studienprogramm 1-3", 'parent' => $kurzinfo->id));
        $output->write(".");
        $p46 = $this->client->POST('/api/container', array("name" => "Studienprogramm 4-6", 'parent' => $kurzinfo->id));
        $output->write(".");
        $wpf = $this->client->POST('/api/container', array("name" => "Wahlpflichtfächer", 'parent' => $kurzinfo->id));
        $output->write(".");
        $agb = $this->client->POST('/api/container', array("name" => "Kleingedrucktes", 'parent' => $kurzinfo->id));
        $output->write(".");
        $output->writeln(" <info>OK</info>");
        $texte = array(
            array("Titel", $titel, "Titel"),
            array("Untertitel", $titel, "Titel - Subline"),
            array("Überschrift", $vorwort, "Überschrift"),
            array("Text", $vorwort, "default"),
            array("Signatur", $vorwort, "kursiv"),
            array("Signatur Untertitel", $vorwort, "klein"),
            array("Thema 1", $trennblatt, "Thema"),
            array("Thema 2", $trennblatt, "Thema"),
            array("Thema 4", $trennblatt, "Thema"),
            array("Thema 3", $trennblatt, "Thema"),
            array("Thema 5", $trennblatt, "Thema"),
            array("Steckbrief", $studiengang, "default"),
            array("Anschrift", $studiengang, "default"),
            array("Titel", $mi, "Überschrift"),
            array("Titel Zielgruppe", $mi, "Untertitel"),
            array("Text Zielgruppe", $mi, "default"),
            array("Titel Ausbildung", $mi, "Überschrift"),
            array("Text Ausbildung", $mi, "default"),
            array("Titel Studium", $mi, "Untertitel"),
            array("Text Studium", $mi, "default"),
            array("Titel Chancen", $mi, "Überschrift"),
            array("Text Chancen", $mi, "default"),
            array("Abschluss", $mi, "default"),
            array("Titel", $p13, "Überschrift"),
            array("Titel", $p46, "Überschrift"),
            array("Titel", $wpf, "Überschrift"),
            array("Untertitel MI", $wpf, "Untertitel"),
            array("Fächer MI", $wpf, "default"),
            array("Untertitel GI", $wpf, "Untertitel"),
            array("Fächer GI", $wpf, "default"),
            array("Titel", $agb, "Überschrift"),
            array("Titel Rückmeldung", $agb, "Untertitel"),
            array("Abschnitt Rückmeldung", $agb, "default"),
            array("Titel Belegung", $agb, "Untertitel"),
            array("Abschnitt Belegung", $agb, "default"),
            array("Titel Leistungsnachweise", $agb, "Untertitel"),
            array("Abschnitt Leistungsnachweise", $agb, "default"),
            array("Titel Anerkennung", $agb, "Untertitel"),
            array("Abschnitt Anerkennung", $agb, "default"),
            array("Titel Praxisprojekt", $agb, "Untertitel"),
            array("Abschnitt Praxisprojekt", $agb, "default"),
            array("Titel Labor", $agb, "Untertitel"),
            array("Abschnitt Labor", $agb, "default"),
            array("Titel Internet", $agb, "Untertitel"),
            array("Abschnitt Internet", $agb, "default"),
        );
        $output->write("Texte anlegen ");
        foreach ($texte as $text) {
            list($name, $parent, $type) = $text;
            $this->client->POST('/api/text', array('name' => $name, 'type' => $type, 'parent' => $parent->id));
            $output->write(".");
        }
        $output->writeln(" <info>OK</info>");

        // Update Types
        $output->write("Text-Typen aktualisieren ");
        $texttypes = $this->client->GET($this->client->getRelationHref($project, 'http://jsonld.retext.it/TextType', true));
        $output->write(".");
        $typeConfig = array(
            'default' => array("Flama Book", 100),
            'Titel' => array("Flama Medium", 200),
            'Titel - Subline' => array("Flama Book", 100),
            'Überschrift' => array("Flama Book", 125),
            'kursiv' => array("Flama Book", 100),
            'klein' => array("Flama Book", 85),
            'Thema' => array("Flama Medium", 100),
            'Untertitel' => array("Flama Book", 100),
        );
        foreach ($texttypes as $texttype) {
            list($fontname, $fontsize) = $typeConfig[$texttype->name];
            $this->client->PUT($texttype->{'@subject'}, array('fontname' => $fontname, 'fontsize' => $fontsize));
            $output->write(".");
        }
        $output->writeln(" <info>OK</info>");
    }
}