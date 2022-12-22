<?php

namespace Kporras07\ComposerDisablePlugin\Plugin;

use Composer\Plugin\PluginInterface;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PrePoolCreateEvent;
use Kporras07\ComposerDisablePlugin\RulesEvaluator;
use Composer\Plugin\PluginEvents;

class ComposerDisablePlugin implements PluginInterface
{
    /**
     * @var Composer\Composer;
     */
    private $composer;

    /**
     * @var Composer\IO\IOInterface;
     */
    private $io;

    /**
     * @var array
     */
    private $config;

    /**
     * @var RulesEvaluator
     */
    private $rulesEvaluator;

    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;

        $this->config = $composer->getPackage()->getExtra()['composer-disable-plugin'] ?? [];
        $this->rulesEvaluator = new RulesEvaluator();

        $repo = $this->composer->getRepositoryManager()->getLocalRepository();
        $packages = $repo->getPackages();
        $packagesToDisable = $this->getPackagesToDisable();

        foreach ($packages as $package) {
            if (in_array($package->getName(), $packagesToDisable)) {
                $this->io->write('ComposerDisablePlugin: Disabling plugin: ' . $package->getName());
                $this->composer->getPluginManager()->deactivatePackage($package);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * Get a list of packages to disable.
     */
    public function getPackagesToDisable()
    {
        $packagesToDisable = [];
        foreach ($this->config['disablePlugins'] ?? [] as $config) {
            $packageName = $config['packageName'];
            $rules = $config['rules'] ?? [];
            $rulesConjunction = $config['rulesConjunction'] ?? 'and';
            $result = $this->rulesEvaluator->evaluate($rules, $rulesConjunction);
            if ($result) {
                $packagesToDisable[] = $packageName;
            }
        }
        return $packagesToDisable;
    }
}
