<?php

use PHPUnit\Framework\TestCase;

/**
 * Class CodeGeneratorTest
 *
 * @example php vendor/bin/phpunit tests/CodeGeneratorTest.php
 */
class CodeGeneratorTest extends TestCase
{
    protected $code;

    protected function setUp()
    {
        parent::setUp();

        $generator = new CodeGenerator();
        $generator->setMask([
            [0,15,0,5],
            [17,0,11,0],
            [0,0,0,0],
            [14,9,0,0],
        ]);
        $generator->generate();
        $generator->printCode();
        $this->code = $generator->getCode();
    }

    public function testColumnsSuccess()
    {
        // rows

        foreach ($this->code as $row) {
            $this->assertEquals(array_sum($row), 50);
        }

        // columns

        for ($i = 0; $i < count($this->code[0]); $i++) {
            $column = array_column($this->code, $i);
            $this->assertEquals(array_sum($column), 50);
        }

        // diagonal

        for ($i = 0; $i < 4; $i++) {
            $d1[] = $this->code[$i][$i];
            $d2[] = $this->code[3-$i][$i];
        }

        $this->assertEquals(array_sum($d1), 50);
        $this->assertEquals(array_sum($d2), 50);
    }
}
