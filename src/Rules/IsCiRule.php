<?php

namespace Kporras07\ComposerDisablePlugin\Rules;

class IsCiRule extends RuleBase
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'isCi';
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate(array $config = []): bool
    {
        return isset($_SERVER['CI']) || isset($_ENV['CI']);
    }
}
