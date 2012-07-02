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
        $blindText = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.';
        $texte = array(
            array("Titel", $titel, "Titel", "mi.info"),
            array("Untertitel", $titel, "Titel - Subline", "11.12"),
            array("Überschrift", $vorwort, "Überschrift", "Unendliche Weiten – wir schreiben das Jahr 2011"),
            array("Text", $vorwort, "default", "»Computer, sag mir alles über mein Studium«, so oder so ähnlich hätte Captain Kirk sich sicherlich informiert, bevor er seine Crew in das Abenteuer der Medieninformatik geführt häitte. Alles nur ScienceFiction? Viele Dinge unserer täglichen Mediennutzung waren vor kurzem noch Entwürfe von Science-Fiction Romanen. Ob es der Begriff Cyberspace ist, der 1984 durch den Roman Neuromancer von William Gibson gepräigt wurde, oder das iPad, das sein Vorbild im PADD genannten Device des Raumschiffs Enterprise hat, man findet viele Anleihen, die Schritt für Schritt in unser tägliches Leben Einzug gehalten haben.\nWie wir in 50 Jahren mit Medien umgehen warden, kann sicherlich niemand voraussagen. Somit ist es wichtig, dass Sie neben den aktuellen Trends auch immer die dahinterliegenden Konzepte betrachten. Sie werden in Ihrem Studium daher neben den aktuellen Technologien viele dieser grundlegenden Konzepte kennen lernen: Wie kann ich Anwendungen strukturiert programmieren? Wie findet Kommunikation und lnteraktion mit neuen Medien statt? Wie kann ich die Usability von Anwendungen verbessern? Wie werden Daten ausgetauscht und verwaltet? Wie kann ich virtuelle Welten erstellen und programmieren? Wie …?\nViele Fragestellungen, die Sie im Laufe lhres Studiums klären können. Experimentieren Sie dabei auch mit unterschiedlichen Ansätzen und setzen Sie Technologien kreativ ein. Erst dadurch kann wieder etwas Neues entstehen.\nIn diesem Sinne wünsche ich Ihnen im Namen des gesamten Ml-Teams einen guten Start und eine spannende Raise durch die unendlichen Weiten der Medieninformatik."),
            array("Signatur", $vorwort, "kursiv", "Prof. Dr. Jörg Berdux"),
            array("Signatur Untertitel", $vorwort, "klein", "Studiengangsleiter Medieninformatik"),
            array("Thema 1", $trennblatt, "Thema", "Kurzinfo"),
            array("Thema 2", $trennblatt, "Thema", "Termine & Öffnungszeiten"),
            array("Thema 3", $trennblatt, "Thema", "Personen"),
            array("Thema 4", $trennblatt, "Thema", "Orientierung"),
            array("Steckbrief - Titel", $studiengang, "Überschrift", "Studiengang"),
            array("Steckbrief", $studiengang, "default", "GRÜNDUNG 2001 STUDIENORT Wiesbaden STANDORT Campus Unter den Eichen STUDIERENDE 200 ZULASSUNG MIT Allgemeiner Hochschulreife, Fachhochschulreife, Numerus Clausus (2.7 im WS 2011/12) AUFNAHME 80 Studierende/Jahr PRAKTIKUM keine Zulassungsvoraussetzung BEWERBUNG jährlich his zum 15. Juli an der Hochschule RheinMain STUDIENBEGINN nur zum Wintersemester REGELSTUDIENZEIT 6 Semester ABSCHLUSS Bachelor of Science (B.Sc.)"),
            array("Anschrift", $studiengang, "default", "Hochschule RheinMain\nFachbereich Design Informatik Medien\nStudiengang Medieninformatik\n\nHaus D\nUnter den Eichen 5\n65195 Wieshaden\n\nTelefon 0611 / 94 95 1241\nTelefax 0611 / 94 95 1240\n\nmedieninformatik@hs-rm.de\nwww.hs-rm.de/medieninformatik\n\nÖffnungszeiten Sekretariat\nMontag bis Freitag: 8.30 bis 11.45 Uhr\nNachmittags nach Rücksprache"),
            array("Titel", $mi, "Überschrift", "Medieninformatik"),
            array("Titel Zielgruppe", $mi, "Untertitel", "Wer sollte Medieninformatik studieren?"),
            array("Text Zielgruppe", $mi, "default", $blindText),
            array("Titel Ausbildung", $mi, "Überschrift", "Interdisziplinäre Ausbildung"),
            array("Text Ausbildung", $mi, "default", $blindText),
            array("Titel Studium", $mi, "Untertitel", "Praxisorientiertes Studium"),
            array("Text Studium", $mi, "default", $blindText),
            array("Titel Chancen", $mi, "Überschrift", "Gute Chancen"),
            array("Text Chancen", $mi, "default", $blindText),
            array("Abschluss", $mi, "default", "Viel Erfolg!"),
            array("Titel", $p13, "Überschrift", "Studienprogramm Medieninformatik 1–3"),
            array("Titel", $p46, "Überschrift", "Studienprogramm Medieninformatik 4–6"),
            array("Titel", $wpf, "Überschrift", "Studienprogramm Medieninformatik Wahlpflichtfächer"),
            array("Untertitel MI", $wpf, "Untertitel", "Liste MI (Medieninformatik)"),
            array("Fächer MI", $wpf, "default", $blindText),
            array("Untertitel GI", $wpf, "Untertitel", "Liste GI (Gestaltung · Informatik)"),
            array("Fächer GI", $wpf, "default", $blindText),
            array("Titel", $agb, "Überschrift", "Das Kleingedruckte"),
            array("Titel Rückmeldung", $agb, "Untertitel", "Rückmeldung"),
            array("Abschnitt Rückmeldung", $agb, "default", $blindText),
            array("Titel Belegung", $agb, "Untertitel", "Belegung und Prüfungsanmeldung (PO2010)"),
            array("Abschnitt Belegung", $agb, "default", $blindText),
            array("Titel Leistungsnachweise", $agb, "Untertitel", "Leistungsnachweise"),
            array("Abschnitt Leistungsnachweise", $agb, "default", $blindText),
            array("Titel Anerkennung", $agb, "Untertitel", "Anerkennung von Fremdleistungen"),
            array("Abschnitt Anerkennung", $agb, "default", $blindText),
            array("Titel Praxisprojekt", $agb, "Untertitel", "Praxisprojekt"),
            array("Abschnitt Praxisprojekt", $agb, "default", $blindText),
            array("Titel Labor", $agb, "Untertitel", "Benutzung der Rechner- und Laborräume"),
            array("Abschnitt Labor", $agb, "default", $blindText),
            array("Titel Internet", $agb, "Untertitel", "Nutzung des Internet"),
            array("Abschnitt Internet", $agb, "default", $blindText),
        );
        $output->write("Texte anlegen ");
        foreach ($texte as $text) {
            list($name, $parent, $type, $content) = $text;
            $text = $this->client->POST('/api/text', array('name' => $name, 'type' => $type, 'parent' => $parent->id, 'text' => $content));
            // Random status
            $spellingApproved = (bool)rand(0, 1);
            $contentApproved = (bool)rand(0, 1);
            $approved = $spellingApproved && $contentApproved ? (bool)rand(0, 1) : false;
            $this->client->PUT($text->{'@subject'}, array('spellingApproved' => $spellingApproved, 'contentApproved' => $contentApproved, 'approved' => $approved));
            $output->write(".");
        }
        $output->writeln(" <info>OK</info>");

        // Update Types
        $output->write("Text-Typen aktualisieren ");
        $texttypes = $this->client->GET($this->client->getRelationHref($project, 'http://jsonld.retext.it/TextType', true));
        $output->write(".");
        $typeConfig = array(
            'default' => array("Flama Book", 100, true),
            'Titel' => array("Flama Medium", 200, false),
            'Titel - Subline' => array("Flama Book", 100, false),
            'Überschrift' => array("Flama Book", 125, false),
            'kursiv' => array("Flama Book", 100, false),
            'klein' => array("Flama Book", 85, false),
            'Thema' => array("Flama Medium", 100, false),
            'Untertitel' => array("Flama Book", 100, false),
        );
        foreach ($texttypes as $texttype) {
            list($fontname, $fontsize, $multiline) = $typeConfig[$texttype->name];
            $this->client->PUT($texttype->{'@subject'}, array('fontname' => $fontname, 'fontsize' => $fontsize, 'multiline' => $multiline));
            $output->write(".");
        }
        $output->writeln(" <info>OK</info>");
    }
}