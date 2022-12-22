<?php

namespace Kporras07\ComposerDisablePlugin\Rules;

class IsGithubActionsRule extends RuleBase
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'isGithubActions';
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate(array $config = []): bool
    {
        return isset($_SERVER['GITHUB_ACTIONS']) || isset($_ENV['GITHUB_ACTIONS']);
    }
}
