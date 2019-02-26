<?php

namespace App\Tests\Controller\Web;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/article/');
        $this->assertCount(
            Article::NUM_ITEMS,
            $crawler->filter('div.article-container'),
            'The homepage displays the right number of articles.'
        );
    }

    public function testNewComment()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test@author1.com',
            'PHP_AUTH_PW' => 'test',
        ]);
        $client->followRedirects();

        // Find first article
        $crawler = $client->request('GET', '/en/article/');
        $articleLink = $crawler->filter('div.article-container > a')->link();

        $crawler = $client->click($articleLink);
        $form = $crawler->selectButton('Create Comment')->form([
            'comment[content]' => 'Hi, Symfony!',
        ]);

        $crawler = $client->submit($form);
        if ($crawler->filter('.article-comment')->count() > 0) {
            $newComment = $crawler->filter('.article-comment')->first()->filter('.comment-container span.comment')->text();
            $this->assertSame('Hi, Symfony!', $newComment);
        }
    }
}
