<?php

namespace BalajiDharma\LaravelCategory\Traits;

if (class_exists(\Plank\Mediable\MediableServiceProvider::class)) {
    trait LaravelMediable
    {
        use \Plank\Mediable\Mediable;

        public $hasLaravelMediable = true;
    }
} else {
    trait LaravelMediable
    {
        public $hasLaravelMediable = false;
    }
}
