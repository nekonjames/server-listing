<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \App\Controller\ServerListController
 * @covers \App\Form\ServerType
 *
 */
class ServerListControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndex(): void
    {
        $url = static::getContainer()->get('router')->generate('server_index');
        $this->client->request('GET', $url);
        $this->assertTrue($this->client->getResponse()->isOk());

        $this->client->request('POST', $url);
        $this->client->submitForm('Filter', [
            'server[storage]' => '5000',
            'server[diskType]' => 'SATA',
            'server[location]' => 'Dallas',
            'server[ram][5]' => '24',
            'server[ram][6]' => '32',
        ]);


        $this->assertTrue($this->client->getResponse()->isSuccessful());
        self::assertStringContainsString(
            'IBM X3650M42x Intel Xeon E5-2620',
            $this->client->getResponse()->getContent()
        );
        self::assertStringContainsString(
            'DallasDAL-10',
            $this->client->getResponse()->getContent()
        );
        self::assertStringContainsString(
            '$220.99',
            $this->client->getResponse()->getContent()
        );
        self::assertStringContainsString(
            '32GBDDR3',
            $this->client->getResponse()->getContent()
        );
    }
}
