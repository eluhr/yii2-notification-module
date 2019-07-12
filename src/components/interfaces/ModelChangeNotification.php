<?php


namespace eluhr\notification\components\interfaces;


/**
 * @package eluhr\notification\components\interfaces
 * @author Elias Luhr <elias.luhr@gmail.com>
 */
interface ModelChangeNotification
{
    public function subject();

    public function text();
}