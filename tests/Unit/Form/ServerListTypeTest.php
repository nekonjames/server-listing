<?php

namespace App\Tests\Unit\Form;

use App\Dto\ServerDto;
use App\Form\ServerType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @covers \App\Form\ServerType
 */

class ServerListTypeTest extends TypeTestCase
{
    public function testFilterForm(): void
    {
        $formData = [
            'storage' => '250',
            'ram' => [],
            'diskType' => 'SSD',
            'location' => 'Dallas',
        ];

        $form = $this->factory->create(ServerType::class, $dto = new ServerDto());

        $expected = new ServerDto();
        $expected->storage = 250;
        $expected->ram = [];
        $expected->diskType = 'SSD';
        $expected->location = 'Dallas';

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $dto);
    }
}