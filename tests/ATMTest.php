<?php

class ATMTest extends \PHPUnit_Framework_TestCase
{
    public function testInputWithNoSecondBlankLine()
    {
        $input = <<<'EOT'
8000
text
EOT;
        $this->setExpectedException('InvalidArgumentException');
        $atm = new ATM($input);
    }

    public function testInputWithInvalidTotalCash()
    {
        $input = <<<'EOT'
text

EOT;
        $this->setExpectedException('InvalidArgumentException');
        $atm = new ATM($input);
    }

    public function testInputWithInvalidSessionFirstLine_Number()
    {
        $input = <<<'EOT'
8000

123 123 123 123
EOT;
        $this->setExpectedException('InvalidArgumentException');
        $atm = new ATM($input);
    }

    public function testInputWithInvalidSessionFirstLine_Type()
    {
        $input = <<<'EOT'
8000

aaa bbb ccc
EOT;
        $this->setExpectedException('InvalidArgumentException');
        $atm = new ATM($input);
    }

    public function testInputWithInvalidSessionSecondLine_Number()
    {
        $input = <<<'EOT'
8000

12345678 1234 1234
aaa
EOT;
        $this->setExpectedException('InvalidArgumentException');
        $atm = new ATM($input);
    }

    public function testInputWithInvalidSessionSecondLine_Type()
    {
        $input = <<<'EOT'
8000

12345678 1234 1234
500 aaa
EOT;
        $this->setExpectedException('InvalidArgumentException');
        $atm = new ATM($input);
    }

    public function testInputWithInvalidSessionNoThirdLine()
    {
        $input = <<<'EOT'
8000

12345678 1234 1234
500 100
EOT;
        $this->setExpectedException('InvalidArgumentException');
        $atm = new ATM($input);
    }

    public function testInputWithInvalidTransactionType()
    {
        $input = <<<'EOT'
8000

12345678 1234 1234
500 100
C
EOT;
        $this->setExpectedException('InvalidArgumentException');
        $atm = new ATM($input);
    }

    public function testInputWithInvalidTransactionAmount()
    {
        $input = <<<'EOT'
8000

12345678 1234 1234
500 100
W aaa
EOT;
        $this->setExpectedException('InvalidArgumentException');
        $atm = new ATM($input);
    }
}
