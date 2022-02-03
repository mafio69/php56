<?php


namespace Idea\Logging\Parser;


class Reader
{
    private $path = '';

    public function getLogFileDates($path = null)
    {
        $dates = [];
        if(!$path){
            $path = storage_path('logs');
        }
        $this->path = $path;
        $files = glob($path.'/laravel-*.log');
        foreach ($files as $path) {
            $fileName = basename($path);
            preg_match('/(?<=laravel-)(.*)(?=.log)/', $fileName, $dtMatch);
            $date = $dtMatch[0];
            array_push($dates, $date);
        }

        return $dates;
    }

    public function get($configDate)
    {
        $pattern = "/^\[(?<date>.*)\]\s(?<env>\w+)\.(?<type>\w+):(?<message>.*)/m";

        $fileName = 'laravel-' . $configDate . '.log';
        $content = file_get_contents($this->path.'/' . $fileName);
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER, 0);

        $logs = [];
        foreach ($matches as $match) {
            $logs[] = [
                'timestamp' => $match['date'],
                'env' => $match['env'],
                'type' => $match['type'],
                'message' => trim($match['message'])
            ];
        }

        return $logs;
    }
}