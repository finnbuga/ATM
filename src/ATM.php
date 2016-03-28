<?php

class ATM
{
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

    private function extractSessions($sessions)
    {
    }
}
