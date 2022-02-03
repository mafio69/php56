<?php

namespace Idea\Logging\Custom;



use Auth;
use Config;
use Mail;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Session;
use URL;

class CustomLog {

    private $logger;

    /**
     * CustomLog constructor.
     */
    public function __construct()
    {
        $this->logger = new Logger('Custom log');
    }

    public function info($log, $message, array $context = array())
    {
        $log .= '-'.date('Y-m');
        $this->logger->pushHandler(new StreamHandler(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER').'/'.$log.'.log', Logger::INFO));

        $context['user'] = \Auth::user()->id.' - '.\Auth::user()->name;
        $this->logger->info($message, $context);
    }

    public function warning($log, $message, array $context = array())
    {
        $log .= '-'.date('Y-m');
        $this->logger->pushHandler(new StreamHandler(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER').'/'.$log.'.log', Logger::WARNING));

        $context['user'] = \Auth::user()->id.' - '.\Auth::user()->name;
        $this->logger->warning($message, $context);
    }


    public function error($log, $message, $context = array())
    {
        $log .= '-'.date('Y-m');
        $this->logger->pushHandler(new StreamHandler(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER').'/'.$log.'.log', Logger::ERROR));

        $context['user'] = \Auth::user()->id.' - '.\Auth::user()->name;
        $this->logger->error($message, $context);

        $data = ['context' => $context];

        Mail::send('emails.errors.error_simple', $data, function($msg)
        {
            $msg->to(Config::get('webconfig.WEBCONFIG_SETTINGS_errors'))->subject('[IdeaLeasing] Error notification.');
        });

    }

    public function alert($log, $message, array $context = array())
    {
        $log .= '-'.date('Y-m');
        $this->logger->pushHandler(new StreamHandler(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER').'/'.$log.'.log', Logger::ALERT));

        $context['user'] = \Auth::user()->id.' - '.\Auth::user()->name;
        $this->logger->alert($message, $context);
    }

}