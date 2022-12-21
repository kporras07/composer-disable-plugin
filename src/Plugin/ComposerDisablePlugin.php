<?php

namespace Kporras07\ComposerDisablePlugin\Plugin;

use Composer\Plugin\PluginInterface;
use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PrePoolCreateEvent;
use Kporras07\ComposerDisablePlugin\RulesEvaluator;
use Composer\Plugin\PluginEvents;

class ComposerDisablePlugin implements PluginInterface, EventSubscriberInterface
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
    }

    /**
     * {@inheritdoc}
     */
    public function deactivate(Composer $composer, IOInterface $io): void {}

    /**
     * {@inheritdoc}
     */
    public function uninstall(Composer $composer, IOInterface $io): void {}

    /**
     * Attach package installation events:.
     *
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PluginEvents::PRE_POOL_CREATE => ['prePoolCreate', 100],
        ];
    }

    /**
     * prePoolCreate event handler.
     */
    public function prePoolCreate(PrePoolCreateEvent $event)
    {
        if ($packagesToDisable = $this->shouldDisablePackages()) {
            $packages = $event->getPackages();
            foreach ($packages as $index => $package) {
                if (in_array($package->getName(), $packagesToDisable)) {
                    $this->io->write('ComposerDisablePlugin: Disabling plugin: ' . $package->getName());
                    unset($packages[$index]);
                }
            }
            // Re-hash the array.
            $packages = array_values($packages);
            $event->setPackages($packages);
        }
    }

    /**
     * prePoolCreate event handler.
     */
    public function shouldDisablePackages()
    {
        $packagesToDisable = [];
        var_dump($this->config);
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
