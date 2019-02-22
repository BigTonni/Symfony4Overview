<?php

namespace App\Tests\Controller\Web;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    /**
     * @dataProvider getUrlsForAnonymousUsers
     */
    public function testAccessDeniedForAnonymousUsers(string $httpMethod, string $url)
    {
        $client = static::createClient();
        $client->request($httpMethod, $url);
        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame(
            'http://127.0.0.1:8000/en/login',
            $response->getTargetUrl(),
            sprintf('The %s secure URL redirects to the login form.', $url)
        );
    }

    public function testEditUser()
    {
        $newUserEmail = 'test@author_new.com';
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test@author1.com',
            'PHP_AUTH_PW' => 'test',
        ]);
        $crawler = $client->request('GET', '/en/profile/edit');
        $form = $crawler->selectButton('Save')->form([
            'user[email]' => $newUserEmail,
        ]);
        $client->submit($form);
        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy([
            'email' => $newUserEmail,
        ]);
        $this->assertNotNull($user);
        $this->assertSame($newUserEmail, $user->getEmail());
    }

    public function testChangePassword()
    {
        $newUserPassword = 'new-password';
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test@author_new.com',
            'PHP_AUTH_PW' => 'test',
        ]);
        $crawler = $client->request('GET', '/en/profile/change-password');
        $form = $crawler->selectButton('Save')->form([
            'change_password[currentPassword]' => 'test',
            'change_password[newPassword][first]' => $newUserPassword,
            'change_password[newPassword][second]' => $newUserPassword,
        ]);
        $client->submit($form);
        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame(
            '/en/logout',
            $response->getTargetUrl(),
            'Changing password logout the user.'
        );
    }

    public function getUrlsForAnonymousUsers()
    {
        yield ['GET', '/en/profile/edit'];
        yield ['GET', '/en/profile/change-password'];
    }
}