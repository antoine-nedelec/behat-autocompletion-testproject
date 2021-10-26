<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementTextException;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\HttpKernel\KernelInterface;

final class FeatureContext extends MinkContext implements Context
{
    /** @var KernelInterface */
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given I am logged in as :user with :password
     *
     * @param string $user
     * @param string $password
     */
    public function iAmLoggedInAsUser(string $user, string $password): void
    {
        $this->visit('/login');
        $this->fillField('username', $user);
        $this->fillField('password', $password);
        $this->pressButton('sign_in');
    }

    /**
     * @Then Content of :identifier is greater than 0
     *
     * @param string $identifier
     * @throws ElementTextException
     */
    public function contentHasValueGreaterThanZero(string $identifier): void
    {
        $element = $this->getSession()->getPage()->findById($identifier);
        if(!is_numeric($element->getText()) || intval($element->getText()) === 0) {
            throw new ElementTextException("Content is not > 0", $this->getSession()->getDriver(), $element);
        }
    }
}
