<?php

namespace Vorterix\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VideoControllerTest extends WebTestCase
{
    public function testSave()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/save');
    }

}
