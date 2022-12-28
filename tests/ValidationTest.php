<?php

// validation manager uses $_SESSION...
session_start();

// tests use framework classes...
use Framework\Testing\TestCase;
use Framework\Validation\Manager;
use Framework\Validation\Rule\EmailRule;
use Framework\Validation\ValidationException;


class ValidationTest extends TestCase
{
    protected Manager $manager;

    /**
     * @throws Exception
     */
    public function testInvalidEmailValuesFail(): void
    {
        $expected = ['email' => ['email should be an email']];

        [$exception] = $this->assertExceptionThrown(
            fn() => $this->manager->validate(['email' => 'foo'], ['email' => ['email']]),
            ValidationException::class,
        );

        $this->assertEquals($expected, $exception->getErrors());
    }

    /**
     * @throws Exception
     */
    public function testValidEmailValuesPass(): void
    {
        $data = $this->manager->validate(['email' => 'foo@bar.com'], ['email' => ['email']]);
        $this->assertEquals('foo@bar.com', $data['email']);
    }
}
