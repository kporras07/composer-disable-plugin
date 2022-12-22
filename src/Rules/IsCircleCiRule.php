<?php

namespace Kporras07\ComposerDisablePlugin\Rules;

class IsCircleCiRule extends RuleBase
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'isCircleCi';
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate(array $config = []): bool
    {
        return isset($_SERVER['CIRCLECI']) || isset($_ENV['CIRCLECI']);
    }
}
