<?php

namespace Borsch\Config;

interface ConfigAwareInterface
{

    public function setConfig(Config $config): void;
}
