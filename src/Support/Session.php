<?php

namespace PragmaRX\Google2FALaravel\Support;

trait Session
{
    /**
     * Make a session var name for.
     *
     * @param null $name
     *
     * @return mixed
     */
    protected function makeSessionVarName($name = null)
    {
        return $this->config('session_var').(is_null($name) || empty($name) ? '' : '.'.$name);
    }

    /**
     * Get a session var value.
     *
     * @param null $var
     *
     * @return mixed
     */
    public function sessionGet($var = null, $default = null)
    {
        return $this->getSessionProvider()->get(
            $this->makeSessionVarName($var),
            $default
        );
    }

    /**
     * Put a var value to the current session.
     *
     * @param $var
     * @param $value
     *
     * @return mixed
     */
    protected function sessionPut($var, $value)
    {
        $this->getSessionProvider()->put(
            $this->makeSessionVarName($var),
            $value
        );

        return $value;
    }

    /**
     * Forget a session var.
     *
     * @param null $var
     */
    protected function sessionForget($var = null)
    {
        $this->getSessionProvider()->forget(
            $this->makeSessionVarName($var)
        );
    }

    /**
     * @return \Illuminate\Contracts\Session\Session
     */
    protected function getSessionProvider()
    {
        if ($this->getRequest()->hasSession()) {
            return $this->getRequest()->session();
        }

        return app('session.store');
    }

    abstract protected function config($string, $children = []);

    abstract public function getRequest();
}
