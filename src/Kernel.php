<?php

namespace Laradic\Console;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Bootstrap;


class Kernel extends \Illuminate\Foundation\Console\Kernel
{
    protected $artisanClass = Artisan::class;

    /**
     * Get the Artisan application instance.
     *
     * @return \Illuminate\Console\Application
     */
    protected function getArtisan()
    {
        if ( is_null($this->artisan) ) {
            return $this->artisan = (new $this->artisanClass($this->app, $this->events, $this->app->version()))
                ->resolveCommands($this->commands);
        }

        return $this->artisan;
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Exception $e
     *
     * @return void
     */
    protected function reportException(Exception $e)
    {
        $this->app->make(ExceptionHandler::class)->report($e);
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @param  \Exception                                        $e
     *
     * @return void
     */
    protected function renderException($output, Exception $e)
    {
        $this->app->make(ExceptionHandler::class)->renderForConsole($output, $e);
    }

    /**
     * @return mixed
     */
    public function getArtisanClass()
    {
        return $this->artisanClass;
    }

    /**
     * Set the artisanClass value
     *
     * @param mixed $artisanClass
     *
     * @return Kernel
     */
    public function setArtisanClass($artisanClass)
    {
        $this->artisanClass = $artisanClass;
        return $this;
    }



}
