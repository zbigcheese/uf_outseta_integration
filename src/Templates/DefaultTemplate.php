<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\Templates;

use UserFrosting\Sprinkle\Core\Templates\TemplatePathBuilder;

class DefaultTemplate extends TemplatePathBuilder
{
    public function getPath(): string
    {
        return __DIR__ . '/../../templates';
    }
}
