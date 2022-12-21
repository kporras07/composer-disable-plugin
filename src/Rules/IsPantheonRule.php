<?php

namespace Kporras07\ComposerDisablePlugin\Rules;

class IsPantheonRule extends RuleBase
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'isPantheon';
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate(array $config = []): bool
    {
        return isset($_ENV['PANTHEON_ENVIRONMENT']);
    }
}
