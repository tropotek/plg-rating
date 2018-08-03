<?php
namespace Rate;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Plugin extends \App\Plugin\Iface
{

    /**
     * A helper method to get the Plugin instance globally
     *
     * @return Plugin|\Tk\Plugin\Iface
     * @throws \Exception
     */
    static function getInstance()
    {
        return \Uni\Config::getInstance()->getPluginFactory()->getPlugin('plg-rating');
    }

    /**
     * Init the plugin
     *
     * This is called when the session first registers the plugin to the queue
     * So it is the first called method after the constructor...
     */
    function doInit()
    {
        include dirname(__FILE__) . '/config.php';

        // Register the plugin for the different client areas if they are to be enabled/disabled/configured by those roles.
        //$this->getPluginFactory()->registerZonePlugin($this, self::ZONE_INSTITUTION);
        $this->getPluginFactory()->registerZonePlugin($this, self::ZONE_SUBJECT_PROFILE);
        //$this->getPluginFactory()->registerZonePlugin($this, self::ZONE_SUBJECT);

        \App\Config::getInstance()->getEventDispatcher()->addSubscriber(new \Rate\Listener\SetupHandler());
    }

    /**
     * Activate the plugin, essentially
     * installing any DB and settings required to run
     * Will only be called when activating the plugin in the
     * plugin control panel
     *
     * @throws \Exception
     */
    function doActivate()
    {
        // Init Plugin Settings
        $db = $this->getConfig()->getDb();

        $migrate = new \Tk\Util\SqlMigrate($db);
        $migrate->setTempPath($this->getConfig()->getTempPath());
        $migrate->migrate(dirname(__FILE__) . '/sql');

    }

    /**
     * Deactivate the plugin removing any DB data and settings
     * Will only be called when deactivating the plugin in the
     * plugin control panel
     */
    function doDeactivate()
    {
        // TODO: Implement doDeactivate() method.
        $db = $this->getConfig()->getDb();



    }

    /**
     * @param $profileId
     * @return bool
     * @throws \Exception
     */
    function isProfileActive($profileId)
    {
        $b = $this->isZonePluginEnabled(\Rate\Plugin::ZONE_SUBJECT_PROFILE, $profileId);
        if (!\Rate\Db\QuestionMap::create()->findFiltered(array('profileId' => $profileId))->count()) {
            $b = false;
        }
        return $b;
    }


}