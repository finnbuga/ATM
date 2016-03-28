<?php

class ATMTest extends \PHPUnit_Framework_TestCase
{
    public function testInputWithNoSecondBlankLine()
    {
        $input = $text = <<<'EOT'
8000
text
EOT;
        $this->setExpectedException('InvalidArgumentException');
        $atm = new ATM($input);
    }

    public function testInputWithInvalidTotalCash()
    {
        $input = $text = <<<'EOT'
text

EOT;
        $this->setExpectedException('InvalidArgumentException');
        $atm = new ATM($input);
    }
}
