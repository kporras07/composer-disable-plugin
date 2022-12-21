<?php

namespace Kporras07\ComposerDisablePlugin\Plugin;

use Composer\Plugin\PluginInterface;
use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PrePoolCreateEvent;
use Composer\Script\Event;
use Kporras07\ComposerDisablePlugin\RulesEvaluator;

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
    private $events;

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

        $this->config = $composer->getConfig()->get('extra')['composer-disable-plugin'] ?? [];
        $this->rulesEvaluator = new RulesEvaluator();

        $this->events = [
            // Command events.
            'pre-install-cmd' => false,
            'post-install-cmd' => false,
            'pre-update-cmd' => false,
            'post-update-cmd' => false,
            'pre-status-cmd' => false,
            'post-status-cmd' => false,
            'pre-archive-cmd' => false,
            'post-archive-cmd' => false,
            'pre-autoload-dump' => false,
            'post-autoload-dump' => false,
            'post-root-package-install' => false,
            'post-create-project-cmd' => false,

            // Package events.
            'pre-package-install' => false,
            'post-package-install' => false,
            'pre-package-update' => false,
            'post-package-update' => false,
            'pre-package-uninstall' => false,
            'post-package-uninstall' => false,

            // Plugin events.
            'init' => false,
            'command' => false,
            'pre-file-download' => false,
            'post-file-download' => false,
            'pre-command-run' => false,
            'pre-pool-create' => 'prePoolCreate',
        ];
    }

    /**
     * Attach package installation events:.
     *
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        $events = [];
        foreach ($this->events as $event => $method) {
            if ($method) {
                $events[$event] = [$method, 100];
            }
        }
        return $events;
    }

    /**
     * prePoolCreate event handler.
     */
    public function prePoolCreate(PrePoolCreateEvent $event)
    {
        if ($packagesToDisable = $this->shouldDisablePackages('pre-pool-create')) {
            $packages = $event->getPackages();
            foreach ($packages as $index => $package) {
                if (in_array($package->getName(), $packagesToDisable)) {
                    $this->io->write('ComposerDisablePlugin: Disabling plugin: ' . $plugin_name);
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
    public function shouldDisablePackages(string $eventName)
    {
        $packagesToDisable = [];
        foreach ($this->config['disable-plugins'] as $plugin_name => $config) {
            $disablePlugin = false;
            if (in_array($eventName, $config['events']) || in_array('all', $config['events'])) {
                $disablePlugin = true;
            }
            if ($disablePlugin) {
                $rules = $config['rules'] ?? [];
                $rulesConjunction = $config['rules_conjunction'] ?? 'and';
                $result = $this->rulesEvaluator->evaluate($rules, $rulesConjunction);
                if ($result) {
                    $packagesToDisable[] = $plugin_name;
                }
            }
        }
        return $packagesToDisable;
    }
}
