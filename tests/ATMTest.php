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
500 100
B
EOT;
        $this->setExpectedException('InvalidArgumentException');
        $atm = new ATM($input);
    }

    public function testInputWithInvalidSessionFirstLine_Type()
    {
        $input = <<<'EOT'
8000

aaa bbb ccc
500 100
B
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
B
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
B
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

    public function testBalance()
    {
        $input = <<<'EOT'
8000

12345678 1234 1234
500 100
B
EOT;
        $atm = new ATM($input);

        $output = <<<'EOT'
500
EOT;
        $this->assertEquals($output, $atm->getOutput());
    }

    public function testWithdrawal()
    {
        $input = <<<'EOT'
8000

12345678 1234 1234
500 100
W 550
EOT;
        $atm = new ATM($input);

        $output = <<<'EOT'
-50
EOT;
        $this->assertEquals($output, $atm->getOutput());
    }

    public function testRepeatedWithdrawal()
    {
        $input = <<<'EOT'
8000

12345678 1234 1234
500 100
W 550
W 10
W 100
W 10
EOT;
        $atm = new ATM($input);

        $output = <<<'EOT'
-50
-60
FUNDS_ERR
-70
EOT;
        $this->assertEquals($output, $atm->getOutput());
    }

    public function testBalanceAndWithdrawal()
    {
        $input = <<<'EOT'
8000

12345678 1234 1234
500 100
W 100
B
W 200
B
B
W 500
B
EOT;
        $atm = new ATM($input);

        $output = <<<'EOT'
400
400
200
200
200
FUNDS_ERR
200
EOT;
        $this->assertEquals($output, $atm->getOutput());
    }

    public function testAMTOutOfCash()
    {
        $input = <<<'EOT'
8000

12345678 1234 1234
50000 1000
W 40000
EOT;
        $atm = new ATM($input);

        $output = <<<'EOT'
ATM_ERR
EOT;
        $this->assertEquals($output, $atm->getOutput());
    }

    public function testGivenExample()
    {
        $input = <<<'EOT'
8000

12345678 1234 1234
500 100
B
W 100

87654321 4321 4321
100 0
W 10

87654321 4321 4321
0 0
W 10
B
EOT;
        $atm = new ATM($input);

        $output = <<<'EOT'
500
400
90
FUNDS_ERR
0
EOT;
        $this->assertEquals($output, $atm->getOutput());
    }

    public function testInvalidPIN()
    {
        $input = <<<'EOT'
8000

12345678 1234 9999
500 100
B
W 100

87654321 4321 4321
100 0
W 10

87654321 4321 4321
0 0
W 10
B
EOT;
        $atm = new ATM($input);

        $output = <<<'EOT'
ACCOUNT_ERR
90
FUNDS_ERR
0
EOT;
        $this->assertEquals($output, $atm->getOutput());
    }
}

