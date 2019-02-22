<?php

namespace App\Tests\Controller\Web;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


class DefaultControllerTest extends WebTestCase
{
    /**
     * @dataProvider getPublicUrls
     */
    public function testPublicUrls(string $url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertSame(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode(),
            sprintf('The %s public URL loads correctly.', $url)
        );
    }

    public function testPublicArticle()
    {
        $client = static::createClient();
        // the service container is always available via the test client
        $article = $client->getContainer()->get('doctrine')->getRepository(Article::class)->find(1);
        $client->request('GET', sprintf('/en/article/edit/%s', $article->getSlug()));
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider getSecureUrls
    */
    public function testSecureUrls(string $url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame(
            'http://127.0.0.1:8000/en/login',
            $response->getTargetUrl(),
            sprintf('The %s secure URL redirects to the login form.', $url)
        );
    }

    public function getPublicUrls()
    {
        yield ['/'];
        yield ['/en/article/'];
        yield ['/en/login'];
    }

    public function getSecureUrls()
    {
        yield ['/en/article/list-articles'];
        yield ['/en/article/new'];
        yield ['/en/article/1'];
        yield ['/en/article/1/edit'];
    }
}