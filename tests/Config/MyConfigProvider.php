<?php

namespace Tests\Config;

class MyConfigProvider
{

    public function __invoke(): array
    {
        return [
            "MCP-key" => "value from MyConfigProvider"
        ];
    }
}
