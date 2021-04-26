<?php

namespace App\Tests\Service;

use App\Entity\Nomination;
use App\Repository\NominationRepository;
use App\Service\PercentagesCalculator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PercentagesCalculatorTest extends KernelTestCase
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

    public function testSetPercentages()
    {
        $now = new \DateTimeImmutable();
        $monthNumber = $now->format('n');
        $year = $now->format('Y');
        $nominationRepository = $this->entityManager->getRepository(Nomination::class);
        $nominations = $nominationRepository->findAllForMonth($monthNumber, $year);

        $percentagesCalculator = new PercentagesCalculator();
        $percentagesCalculator->setPercentages($nominations, $this->entityManager);

        $this->assertEquals(0, $nominations[0]->getPercentage());
        $this->assertEquals(1, $nominations[1]->getPercentage());
        $this->assertEquals(0, $nominations[2]->getPercentage());
    }
}
