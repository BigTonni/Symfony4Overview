<?php

//namespace App\Tests\Controller\Api;
//
//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//
//class ArticleControllerTest extends WebTestCase
//{
//    public function testListArticles()
//    {
//        $client = static::createClient();
//
//        $client->request('GET', '/api/articles');
//
//        $this->assertSame(200, $client->getResponse()->getStatusCode());
//    }
//
//    /**
//     * @dataProvider provideUrls
//     * @param mixed $url
//     */
//    public function testPageIsSuccessful($url)
//    {
//        $client = static::createClient();
//        $client->request('GET', $url);
//
//        $this->assertTrue($client->getResponse()->isSuccessful());
//    }
//
//    public function provideUrls()
//    {
//        return [
//            ['/'],
//            ['/api/doc'],
//            ['/login'],
//        ];
//    }
//}
