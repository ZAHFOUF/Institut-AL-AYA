<?php

namespace AlAya\Tests;

use AlAya\Common\Entity\Agent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class AgentAutomationTest extends KernelTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testFindAvailableAgentsReturnsCorrectAgents(): void
    {
        // Sample input to test against
        $availability = ['Matin'];
        $days = ['All'];
        $timezones = 'Europe/Paris';      

        // Execute the method under test
        $repo = $this->em->getRepository(Agent::class);
        $agent = $repo->findAvailableAgents($availability, $days, $timezones);

        // Assertions
        $this->assertNotEmpty($agent, 'No agents returned, expected at least one.');
        $this->assertInstanceOf(Agent::class, $agent);
        dd($agent);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
    }
}
