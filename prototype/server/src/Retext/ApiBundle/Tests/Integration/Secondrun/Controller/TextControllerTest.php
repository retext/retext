<?php

namespace Retext\ApiBundle\Tests\Integration\Secondrun\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TextControllerTest extends Base
{
    private static $project;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$client->CREATE('/api/user', array('email' => 'phpunit+text@retext.it'));
        self::$client->POST('/api/login', array('email' => 'phpunit+text@retext.it', 'password' => 'phpunit+text@retext.it'));
        self::$project = self::$client->CREATE('/api/project', array('name' => 'Text-Test-Project'));
    }

    /**
     * @group secondrun
     * @group integration
     */
    public function testCreateText()
    {
        $hl = self::$client->CREATE('/api/text', array('parent' => self::$project->rootContainer, 'name' => 'Dies ist eine Überschrift', 'type' => 'Überschrift'));
        $this->checkText($hl);
        $sl = self::$client->CREATE('/api/text', array('parent' => self::$project->rootContainer, 'name' => 'Dies ist eine Unter-Überschrift', 'type' => 'Unter-Überschrift'));
        $this->checkText($sl);
        $text1 = self::$client->CREATE('/api/text', array('parent' => self::$project->rootContainer, 'name' => 'Lorem Ipsum', 'type' => 'Fließtext'));
        $text2 = self::$client->CREATE('/api/text', array('parent' => self::$project->rootContainer, 'name' => 'Lorem Ipsum 2', 'type' => 'Fließtext'));
        $this->checkText($text1);
        $this->checkText($text2);
    }

    /**
     * @group secondrun
     * @group integration
     * @depends testCreateText
     */
    public function testTextTypes()
    {
        $textTypes = self::$client->GET($this->getRelationHref(self::$project, 'http://jsonld.retext.it/TextType', true));
        $this->assertInternalType('array', $textTypes);
        $this->assertEquals(3, count($textTypes));
    }

    /**
     * @group secondrun
     * @group integration
     * @depends testCreateText
     */
    public function testUpdateText()
    {
        $text = self::$client->CREATE('/api/text', array('parent' => self::$project->rootContainer, 'name' => 'Copy 1', 'text' => array('de' => 'LOREM IPSUM!'), 'type' => 'Fließtext'));
        $text = self::$client->UPDATE($text->{'@subject'}, array('text' => array('de' => 'Lorem Ipsum!')));
        $this->checkText($text);
        $this->assertObjectHasAttribute('text', $text);
        $this->assertEquals('Lorem Ipsum!', $text->text->de);
        return $text;
    }

    /**
     * @group secondrun
     * @group integration
     * @depends testUpdateText
     */
    public function testTextHasUpdateHistory(\stdClass $text)
    {
        self::$client->UPDATE($text->{'@subject'}, array('text' => array('de' => 'Lorem Ipsum')));
        $history = self::$client->GET($this->getRelationHref($text, 'http://jsonld.retext.it/TextVersion', true));
        $this->assertInternalType('array', $history);
        $this->assertEquals(3, count($history));
        $this->assertEquals('Lorem Ipsum', $history[0]->text->de);
        $this->assertEquals('Lorem Ipsum!', $history[1]->text->de);
        $this->assertEquals('LOREM IPSUM!', $history[2]->text->de);
    }

    /**
     * @group secondrun
     * @group integration
     * @depends testUpdateText
     */
    public function testTextComments(\stdClass $text)
    {
        $this->assertEquals(0, $text->commentCount);
        $commentsRel = $this->getRelationHref($text, 'http://jsonld.retext.it/Comment', true);
        $comment = self::$client->CREATE($commentsRel, array('text' => $text->id, 'comment' => 'First!'));
        $this->assertEquals('First!', $comment->comment);
        $this->assertEquals('phpunit+text@retext.it', $comment->user->email);
        $this->assertNotNull($comment->createdAt);
        $text = self::$client->GET($text->{'@subject'});
        $this->assertEquals(1, $text->commentCount);
        self::$client->CREATE($commentsRel, array('text' => $text->id, 'comment' => 'Second!'));
        self::$client->CREATE($commentsRel, array('text' => $text->id, 'comment' => 'Third!'));
        $comments = self::$client->GET($commentsRel);
        $this->assertInternalType('array', $comments);
        $this->assertEquals(3, count($comments));
        $this->assertEquals('Third!', $comments[0]->comment);
        $this->assertEquals('Second!', $comments[1]->comment);
        $this->assertEquals('First!', $comments[2]->comment);
    }


    /**
     * @group secondrun
     * @group integration
     */
    public function testProjectProgress()
    {
        $project = self::$client->CREATE('/api/project', array('name' => 'Test-Project für Progress'));
        $t1 = self::$client->CREATE('/api/text', array('parent' => $project->rootContainer, 'name' => 'Text1'));
        $t2 = self::$client->CREATE('/api/text', array('parent' => $project->rootContainer, 'name' => 'Text2'));
        $t3 = self::$client->CREATE('/api/text', array('parent' => $project->rootContainer, 'name' => 'Text3'));
        self::$client->CREATE('/api/text', array('parent' => $project->rootContainer, 'name' => 'Text4'));
        self::$client->CREATE('/api/text', array('parent' => $project->rootContainer, 'name' => 'Text4'));
        $progress = self::$client->GET($this->getRelationHref($project, 'http://jsonld.retext.it/ProjectProgress'));
        foreach (array('total', 'approved', 'contentApproved', 'spellingApproved') as $type) {
            $this->assertEquals(0, $progress->$type->yes);
            $this->assertEquals(5, $progress->$type->no);
            $this->assertEquals(0.0, $progress->$type->progress);
        }
        $this->assertEquals(0, $t1->approvedProgress);

        self::$client->UPDATE($t1->{'@subject'}, array('approved' => 'true'));
        self::$client->UPDATE($t1->{'@subject'}, array('spellingApproved' => 'true'));
        self::$client->UPDATE($t2->{'@subject'}, array('spellingApproved' => 'true'));
        $t1 = self::$client->UPDATE($t1->{'@subject'}, array('contentApproved' => 'true'));
        $t2 = self::$client->UPDATE($t2->{'@subject'}, array('contentApproved' => 'true'));
        $t3 = self::$client->UPDATE($t3->{'@subject'}, array('contentApproved' => 'true'));
        $progress = self::$client->GET($this->getRelationHref($project, 'http://jsonld.retext.it/ProjectProgress'));
        $this->assertEquals(1, $progress->total->yes);
        $this->assertEquals(4, $progress->total->no);
        $this->assertEquals(1, $progress->approved->yes);
        $this->assertEquals(4, $progress->approved->no);
        $this->assertEquals(2, $progress->spellingApproved->yes);
        $this->assertEquals(3, $progress->spellingApproved->no);
        $this->assertEquals(3, $progress->contentApproved->yes);
        $this->assertEquals(2, $progress->contentApproved->no);
        $this->assertEquals(0.6, $progress->contentApproved->progress);
        $this->assertEquals(1.0, $t1->approvedProgress);
        $this->assertEquals(2 / 3, $t2->approvedProgress);
        $this->assertEquals(1 / 3, $t3->approvedProgress);

    }

    private function checkText(\stdClass $text)
    {
        $this->assertObjectHasAttribute('@context', $text);
        $this->assertEquals('http://jsonld.retext.it/Text', $text->{'@context'});
        $this->assertObjectHasAttribute('@subject', $text);
        $this->assertNotNull($text->{'@subject'});
        $this->assertObjectHasAttribute('project', $text);
        $this->assertNotNull($text->project);
        $this->assertInternalType('string', $text->project);
        $this->assertObjectHasAttribute('parent', $text);
        $this->assertNotNull($text->parent);
        $this->assertInternalType('string', $text->parent);
        $this->assertFalse($text->spellingApproved);
        $this->assertFalse($text->contentApproved);
        $this->assertFalse($text->approved);
    }

}
