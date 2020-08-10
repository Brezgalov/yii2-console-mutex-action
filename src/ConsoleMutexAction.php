<?php

namespace brezgalov;

use yii\base\Action;
use yii\mutex\Mutex;

class ConsoleMutexAction extends Action
{
    /**
     * @var string
     */
    public $mutexComponentName = 'mutex';

    /**
     * @var string
     */
    public $mutexName;

    /**
     * @var callable
     */
    public $innerAction;

    /**
     * @var int
     */
    public $timeout = 0;

    /**
     * @return Mutex|null
     */
    public function getMutexComponent()
    {
        return \Yii::$app->has($this->mutexComponentName) ? \Yii::$app->get($this->mutexComponentName) : null;
    }

    /**
     * Запускаем действие
     */
    public function run()
    {
        $mutex = $this->getMutexComponent();
        if ($mutex) {
            if ($mutex->acquire($this->mutexName, $this->timeout)) {

                call_user_func($this->innerAction);

                $mutex->release($this->mutexName);
            }
        } else {
            call_user_func($this->innerAction);
        }
    }
}