<?php


namespace Idea\Logging\Parser;


class Parser
{
    protected $logPath;


    const LOG_DATE_PATTERN = "\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]";
    const LOG_ENVIRONMENT_PATTERN = "(\w+)";
    const LOG_LEVEL_PATTERN = "([A-Z]+)";

    public function get()
    {
        try {
            if (!file_exists($this->logPath)) {
                throw new \Exception("Log Parser: file '" . $this->logPath . "' does not exist.");
            }


            return $this->parse($this->logPath);
        } catch (\Exception $e) {
            \Log::error($e);
        }

        return false;
    }


    public function setLogPath($path)
    {
        $this->logPath = $path;

        return $this;
    }

    private function parse($logPath)
    {
        return $this->getLogEntries(file_get_contents($logPath));
    }

    private function getLogEntries($logContent)
    {
        $headerSet = $dateSet = $envSet = $levelSet = $bodySet = [];
        $logEntries = [];

        $pattern = "/^" . self::LOG_DATE_PATTERN . "\s" . self::LOG_ENVIRONMENT_PATTERN. "\." . self::LOG_LEVEL_PATTERN . "\:|Next/m";

        preg_match_all($pattern, $logContent, $matches);

        if (is_array($matches)) {
            $bodySet = array_map('ltrim', preg_split($pattern, $logContent));

            if (empty($bodySet[0]) && count($bodySet) > count($matches[0])) {
                array_shift($bodySet);
            }

            $headerSet = $matches[0];
            $dateSet = $matches[1];
            $envSet = $matches[2];
            $levelSet = $matches[3];
        }

        $ordNum = 0;

        foreach ($headerSet as $key => $header) {
            $ordNum++;
            $isChildEntry = false;

            if (empty($dateSet[$key])) {
                $isChildEntry = $this->startsWith($header, "Next");

                $dateSet[$key] = $dateSet[$key-1];
                $envSet[$key] = $envSet[$key-1];
                $levelSet[$key] = $levelSet[$key-1];
                $header = str_replace("Next", $headerSet[$key-1], $header);
            }

            $logEntry[] = [
                'environment' => $envSet[$key],
                'level' => $levelSet[$key],
                'date' => $dateSet[$key],
                'header' => $header,
                'is_child_entry' => $isChildEntry,
                'body' => $bodySet[$key],
                'children' => [],
                'ord_num' => $ordNum
            ];

            $logEntries[] = $logEntry;
        }

        return $logEntries;
    }

    private function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0) {
                return true;
            }
        }

        return false;
    }
}