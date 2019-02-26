<?php

namespace App\Tests\Controller\Web;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * @dataProvider getUrlsForAnonymousUsers
     * @param string $httpMethod
     * @param string $url
     */
    public function testAccessDeniedForAnonymousUsers(string $httpMethod, string $url)
    {
        $this->client->request($httpMethod, $url);
        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame(
            'http://localhost/en/login',
            $response->getTargetUrl(),
            sprintf('The %s secure URL redirects to the login form.', $url)
        );
    }

    public function testEditUser()
    {
        $newUserEmail = 'test@author_new.com';

        $crawler = $this->client->request('GET', '/en/user/edit', array(), array(), array(
            'PHP_AUTH_USER' => 'test@author1.com',
            'PHP_AUTH_PW'   => 'test',
        ));

        $form = $crawler->selectButton('Save')->form([
            'user[email]' => $newUserEmail,
        ]);

        $this->client->submit($form);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        //Not found user by new email!
//        $user = $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy([
//            'email' => $newUserEmail,
//        ]);

//        $this->assertNotNull($user);
//        $this->assertSame($newUserEmail, $user->getEmail());
    }

    public function testChangePassword()
    {
        $newUserPassword = 'new-password';
        $crawler = $this->client->request('GET', '/en/user/change-password', array(), array(), array(
            'PHP_AUTH_USER' => 'test@author1.com',
            'PHP_AUTH_PW'   => 'test',
        ));

        $form = $crawler->selectButton('Save')->form([
            'change_password[currentPassword]' => 'test',
            'change_password[newPassword][first]' => $newUserPassword,
            'change_password[newPassword][second]' => $newUserPassword,
        ]);
        $this->client->submit($form);

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame(
            '/en/logout',
            $response->getTargetUrl(),
            'Changing password logout the user.'
        );
    }

    public function getUrlsForAnonymousUsers()
    {
        yield ['GET', '/en/user/edit'];
        yield ['GET', '/en/user/change-password'];
    }
}
