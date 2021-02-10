<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testLogin() {
        $client = static::createClient();

        $crawler = $client->request('GET', '');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $submit = $crawler->selectButton('Sign in');
        $form = $submit->form();

        $client->submit($form, [
            'email' => 'thibault@fidcar.com',
            'password' => 'VerySecretPassword'
        ]);

        $client->followRedirect();

        $this->assertStringContainsString('Dashboard', $client->getResponse()->getContent());
    }

    public function testLoginFailed() {
        $client = static::createClient();

        $crawler = $client->request('GET', '');

        $submit = $crawler->selectButton('Sign in');
        $form = $submit->form();

        $client->submit($form, [
            'email' => 'ramzi@fidcar.com',
            'password' => 'VerySecretPassword'
        ]);

        $client->followRedirect();

        $this->assertStringNotContainsString('Thibault Henry', $client->getResponse()->getContent());
    }

    public function testPasswordFailed() {
        $client = static::createClient();

        $crawler = $client->request('GET', '');

        $submit = $crawler->selectButton('Sign in');
        $form = $submit->form();

        $client->submit($form, [
            'email' => 'thibault@fidcar.com',
            'password' => 'NotTheRightPassword'
        ]);

        $client->followRedirect();

        $this->assertStringNotContainsString('Thibault Henry', $client->getResponse()->getContent());
    }
}