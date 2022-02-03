<?php

namespace Idea\Debug;


class Queue
{
    public function fire($job, $s)
    {
        sleep($s);

        \Log::info(time());

        $job->delete();
    }

}
