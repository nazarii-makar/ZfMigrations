<?php
namespace ZfMigrations\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Console\Request as ConsoleRequest;
use ZfMigrations\Library\Migration;
use ZfMigrations\Library\MigrationException;
use ZfMigrations\Library\MigrationSkeletonGenerator;
use ZfMigrations\Library\OutputWriter;

/**
 * Migration commands controller
 */
class MigrateController extends AbstractActionController
{
    /**
     * @var \ZfMigrations\Library\Migration
     */
    protected $migration;

    public function onDispatch(MvcEvent $e)
    {
        if (!$this->getRequest() instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        return parent::onDispatch($e);
    }

    /**
     * Overridden only for PHPDoc return value for IDE code helpers
     *
     * @return ConsoleRequest
     */
    public function getRequest()
    {
        return parent::getRequest();
    }

    /**
     * Get current migration version
     *
     * @return int
     */
    public function versionAction()
    {
        return sprintf("Current version %s\n", $this->getMigration()->getCurrentVersion());
    }

    /**
     * List migrations - not applied by default, all with 'all' flag.
     *
     * @return string
     */
    public function listAction()
    {
        $migrations = $this->getMigration()->getMigrationClasses($this->getRequest()->getParam('all'));
        $list = array();
        foreach ($migrations as $m) {
            $list[] = sprintf("%s %s - %s", $m['applied'] ? '-' : '+', $m['version'], $m['description']);
        }
        return (empty($list) ? 'No migrations available.' : implode("\n", $list)) . "\n";
    }

    /**
     * Apply migration
     */
    public function applyAction()
    {
        $migrations = $this->getMigration()->getMigrationClasses();
        $currentMigrationVersion = $this->getMigration()->getCurrentVersion();

        $version = $this->getRequest()->getParam('version');
        $force = $this->getRequest()->getParam('force');
        $down = $this->getRequest()->getParam('down');
        $fake = $this->getRequest()->getParam('fake');

        if (is_null($version) && $force) {
            return "Can't force migration apply without migration version explicitly set.";
        }
        if (is_null($version) && $fake) {
            return "Can't fake migration apply without migration version explicitly set.";
        }
        if (!$force && is_null($version) && $currentMigrationVersion >= $this->getMigration()->getMaxMigrationVersion($migrations)) {
            return "No migrations to apply.\n";
        }

        $this->getMigration()->migrate($version, $force, $down, $fake);
        return "Migrations applied!\n";
    }

    /**
     * Returns ServiceManager
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected function getServiceManager()
    {
        return $this->getEvent()->getApplication()->getServiceManager();
    }

    /**
     * Generate new migration skeleton class
     */
    public function generateSkeletonAction()
    {
        $config = $this->getServiceManager()->get('Config');

        $generator = new MigrationSkeletonGenerator($config['migrations']['dir'], $config['migrations']['namespace']);
        $classPath = $generator->generate();

        return sprintf("Generated skeleton class @ %s\n", realpath($classPath));
    }

    /**
     * @return \ZfMigrations\Model\MigrationVersionTable
     */
    protected function getMigrationVersionTable()
    {
        return $this->getServiceManager()->get('ZfMigrations\Model\MigrationVersionTable');
    }

    /**
     * @return Migration
     */
    protected function getMigration()
    {
        if (!$this->migration) {
            /** @var $adapter \Zend\Db\Adapter\Adapter */
            $adapter = $this->getServiceManager()->get('Zend\Db\Adapter\Adapter');
            $config = $this->getServiceManager()->get('Configuration');

            $output = null;
            if ($config['migrations']['show_log']) {
                $console = $this->getServiceManager()->get('console');
                $output = new OutputWriter(function ($message) use ($console) {
                    $console->write($message . "\n");
                });
            }

            $this->migration = new Migration($adapter, $config['migrations'], $this->getMigrationVersionTable(), $output);

            $this->migration->setServiceLocator($this->getServiceManager());
        }
        return $this->migration;
    }
}
