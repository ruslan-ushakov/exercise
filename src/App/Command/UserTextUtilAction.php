<?php

namespace App\Command;

interface UserTextUtilAction
{
    /**
     * Выполняет полезные действия
     *
     * @return bool
     */
    public function __invoke();

    /**
     * Возвращает результат выполнения
     *
     * @return string
     */
    public function output();
}