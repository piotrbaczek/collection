<?php

declare(strict_types=1);

namespace Ramsey\Collection\Test\Tool;

use Ramsey\Collection\Exception\ValueExtractionException;
use Ramsey\Collection\Test\TestCase;
use Ramsey\Collection\Tool\ValueExtractorTrait;

/**
 * Cover up all possible outcomes of the ValueExtractorTrait.
 */
class ValueExtractorTraitTest extends TestCase
{
    public function testShouldRaiseExceptionWhenPropertyOrMethodNotExist(): void
    {
        $test = new class {
            use ValueExtractorTrait;

            /**
             * @return mixed
             */
            public function __invoke(string $propertyOrMethod)
            {
                return $this->extractValue($this, $propertyOrMethod);
            }
        };

        $this->expectException(ValueExtractionException::class);
        $this->expectExceptionMessage('Method or property "undefinedMethod" not defined in');

        $test('undefinedMethod');
    }

    public function testShouldExtractValueByMethod(): void
    {
        $test = new class {
            use ValueExtractorTrait;

            /**
             * @return mixed
             */
            public function __invoke(string $propertyOrMethod)
            {
                return $this->extractValue($this, $propertyOrMethod);
            }

            public function testMethod(): string
            {
                return 'works!';
            }
        };

        $this->assertSame('works!', $test('testMethod'), 'Could not extract value by method');
    }

    public function testShouldExtractValueByProperty(): void
    {
        $test = new class {
            use ValueExtractorTrait;

            /**
             * @var string
             */
            public $testProperty = 'works!';

            /**
             * @return mixed
             */
            public function __invoke(string $propertyOrMethod)
            {
                return $this->extractValue($this, $propertyOrMethod);
            }
        };

        $this->assertSame('works!', $test('testProperty'), 'Could not extract value by property');
    }
}
