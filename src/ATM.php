<?php

class ATM
{
    const BALANCE_INQUIRY_SYMBOL = 'B';
    const BALANCE_INQUIRY = 1;
    const WITHDRAWAL_SYMBOL = 'W';
    const WITHDRAWAL = 2;

    private $totalCash;
    private $sessions = array();

    public function __construct($input)
    {
        $lines = explode("\n", $input, 3);

        if (empty($lines) || !is_numeric($lines[0]) || !empty($lines[1])) {
            throw new \InvalidArgumentException('Wrong argument. The input should contain the total cash on the first line followed by a blank line');
        }

        $this->totalCash = $lines[0];

        if (!empty($lines[2])) {
            $this->extractSessions($lines[2]);
        }
    }

    private function extractSessions($input)
    {
        $sessions = explode("\n\n", $input);
        foreach ($sessions as $session) {
            $lines = explode("\n", $session, 3);
            if (count($lines) < 3) {
                throw new \InvalidArgumentException('Wrong argument. Each session consists of at least 3 lines');
            }

            $this->sessions[] = array_merge(
                $this->extractAccountNumberAndPINs($lines[0]),
                $this->extractBalanceAndOverdraft($lines[1]),
                $this->extractTransactions($lines[2])
            );
        }
    }

    private function extractAccountNumberAndPINs($line)
    {
        $words = explode(" ", $line);
        if (count($words) != 3 || !is_numeric($words[0]) || !is_numeric($words[1]) || !is_numeric($words[2])) {
            throw new \InvalidArgumentException("Wrong argument. The first line of a session should contain the user's account number, correct PIN and the PIN they actually entered.");
        }

        return array(
          'accountNumber' => $words[0],
          'correctPIN'    => $words[1],
          'introducedPIN' => $words[2],
        );
    }

    private function extractBalanceAndOverdraft($line)
    {
        $words = explode(" ", $line);
        if (count($words) != 2 || !is_numeric($words[0]) || !is_numeric($words[1])) {
            throw new \InvalidArgumentException("Wrong argument. The second line of a session should contain the user's balance and overdraft facility.");
        }
        return array(
          'balance'   => $words[0],
          'overdraft' => $words[1],
        );
    }

    private function extractTransactions($input)
    {
        $transactions = array();

        $lines = explode("\n", $input);
        foreach ($lines as $line) {
            $words = explode(" ", $line);

            if (count($words) == 1 && $words[0] == self::BALANCE_INQUIRY_SYMBOL) {
                $transactions[] = array(
                  'type' => self::BALANCE_INQUIRY
                );
            } elseif (count($words) == 2 && $words[0] == self::WITHDRAWAL_SYMBOL && is_numeric($words[1])) {
                $transactions[] = array(
                  'type' => self::WITHDRAWAL,
                  'amount' => $words[1],
                );
            } else {
                throw new \InvalidArgumentException("Wrong argument. Invalid transaction");
            }
        }

        return array(
          'transactions' => $transactions
        );
    }
}
