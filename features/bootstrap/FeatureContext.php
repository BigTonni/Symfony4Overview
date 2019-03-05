<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
class FeatureContext extends RawMinkContext implements Context
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Response|null
     */
    private $response;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @When() a demo scenario sends a request to :path
     * @param string $path
     */
    public function aDemoScenarioSendsARequestTo(string $path)
    {
        $this->response = $this->kernel->handle(Request::create($path, 'GET'));
    }

    /**
     * @Then() the response should be received
     */
    public function theResponseShouldBeReceived()
    {
        if ($this->response === null) {
            throw new \RuntimeException('No response received');
        }
    }

    /**
     * @When() I add :arg1 header equal to :arg2
     * @param mixed $arg1
     * @param mixed $arg2
     */
    public function iAddHeaderEqualTo($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @When() I send a :arg1 request to :arg2 with body:
     * @param mixed $arg1
     * @param mixed $arg2
     * @param PyStringNode $string
     */
    public function iSendARequestToWithBody($arg1, $arg2, PyStringNode $string)
    {
        throw new PendingException();
    }

    /**
     * @Then() the response status code should be :arg1
     * @param mixed $arg1
     */
    public function theResponseStatusCodeShouldBe($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then() the response should be in JSON
     */
    public function theResponseShouldBeInJson()
    {
        throw new PendingException();
    }

    /**
     * @Then() the header :arg1 should be equal to :arg2
     * @param mixed $arg1
     * @param mixed $arg2
     */
    public function theHeaderShouldBeEqualTo($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Given() I am on the homepage
     */
    public function iAmOnTheHomepage()
    {
        throw new PendingException();
    }

    /**
     * @When() I go to :arg1
     * @param mixed $arg1
     */
    public function iGoTo($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When() I fill in :arg1 with :arg2
     * @param mixed $arg1
     * @param mixed $arg2
     */
    public function iFillInWith($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @When() I press :arg1
     * @param mixed $arg1
     */
    public function iPress($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then() I should go to the page :arg1
     * @param mixed $arg1
     */
    public function iShouldGoToThePage2($arg1)
    {
        throw new PendingException();
    }
}
