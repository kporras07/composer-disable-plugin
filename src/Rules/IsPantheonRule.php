<?php

namespace Kporras07\ComposerDisablePlugin\Rules;

class IsPantheonRule extends RuleEnvBase
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'isPantheon';
        $this->envName = 'PANTHEON_ENVIRONMENT';
    }
}
