<?php

namespace App\Tests\Service;

use App\Service\InviteCodeGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InviteCodeGeneratorTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

public function testMakeCode()
    {
        $generator = new InviteCodeGenerator();
        $code = $generator->makeCode(4);
        $this->assertEquals(4, strlen($code));
    }

    public function testMakeInviteCodes()
    {
        $generator = new InviteCodeGenerator();
        $inviteCodes = $generator->makeInviteCodes(1, 4, $this->entityManager);

        $this->assertEquals(1, count($inviteCodes));
        $this->assertEquals(4, strlen($inviteCodes[0]->getCode()));
    }
}
