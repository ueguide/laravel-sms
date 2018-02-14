<?php

use TheLHC\SMS\Tests\TestCase;

class SMSTest extends TestCase
{
    public function testItRegisters()
    {
        SMS::send('hello sir', '+1-828-301-9460');

        $this->assertTrue(true);
    }
}
