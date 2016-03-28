<?php

class ATM
{
    const BALANCE_INQUIRY = 'B';
    const WITHDRAWAL = 'W';

    const INVALID_ACCOUNT_ERR = 'ACCOUNT_ERR';
    const UNAVAILABLE_FUNDS_ERR = 'FUNDS_ERR';
    const ATM_OUT_OF_CASH_ERR = 'ATM_ERR';

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
            $this->sessions = $this->extractSessions($lines[2]);
        }
    }

    private function extractSessions($input)
    {
        $extractedSessions = array();

        $sessions = explode("\n\n", $input);
        foreach ($sessions as $session) {
            $lines = explode("\n", $session, 3);
            if (count($lines) < 3) {
                throw new \InvalidArgumentException('Wrong argument. Each session consists of at least 3 lines');
            }

            $extractedSessions[] = array_merge(
                $this->extractAccountNumberAndPINs($lines[0]),
                $this->extractBalanceAndOverdraft($lines[1]),
                $this->extractTransactions($lines[2])
            );
        }

        return $extractedSessions;
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
            $transactions[] = $this->extractTransaction($line);
        }

        return array(
          'transactions' => $transactions
        );
    }

    private function extractTransaction($line)
    {
        $words = explode(" ", $line);

        if (count($words) == 1 && $words[0] == self::BALANCE_INQUIRY) {
            return array(
              'type' => self::BALANCE_INQUIRY
            );
        } elseif (count($words) == 2 && $words[0] == self::WITHDRAWAL && is_numeric($words[1])) {
            return array(
              'type'   => self::WITHDRAWAL,
              'amount' => $words[1],
            );
        } else {
            throw new \InvalidArgumentException("Wrong argument. Invalid transaction");
        }
    }

    public function getOutput()
    {
        $output = array();

        foreach ($this->sessions as $session) {
            if (!$this->isValidAccount($session)) {
                $output[] = self::INVALID_ACCOUNT_ERR;
                continue;
            }

            foreach ($session['transactions'] as $transaction) {
                switch ($transaction['type']) {
                    case self::BALANCE_INQUIRY:
                        $output[] = $session['balance'];
                        break;

                    case self::WITHDRAWAL:
                        if (!$this->areFundsAvailable($session, $transaction)) {
                            $output[] = self::UNAVAILABLE_FUNDS_ERR;
                        } elseif ($this->isATMOutOfCash($transaction)) {
                            $output[] = self::ATM_OUT_OF_CASH_ERR;
                        } else {
                            $session['balance'] -= $transaction['amount'];
                            $output[] = $session['balance'];
                        }
                }
            }
        }

        return implode("\n", $output);
    }

    private function isValidAccount($session)
    {
        return $session['correctPIN'] == $session['introducedPIN'];
    }

    private function areFundsAvailable($session, $transaction)
    {
        return $session['balance'] + $session['overdraft'] >= $transaction['amount'];
    }

    private function isATMOutOfCash($transaction)
    {
        return $this->totalCash < $transaction['amount'];
    }
}
