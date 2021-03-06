<?php namespace VS\ApplicationBundle\Component\Settings;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;

use VS\ApplicationBundle\Component\Exception\SettingsException;

class Settings
{
    /**
     * @var ContainerInterface $container
     */
    private $container;
    
    /**
     * @var PhpArrayAdapter $cache
     */
    private $cache;
    
    /**
     * @var PropertyAccessor $propertyAccessor
     */
    private $propertyAccessor;
    
    /**
     * @var array $settingsKeys
     */
    private $settingsKeys;
    
    public function __construct( ContainerInterface $container )
    {
        $this->container        = $container;
        
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->settingsKeys     = ['maintenanceMode', 'maintenancePage', 'theme'];
        
        // https://symfony.com/doc/current/components/cache/adapters/php_array_cache_adapter.html
        //==========================================================================================
        // This adapter requires turning on the opcache.enable php.ini setting.
        /////////////////////////////////////////////////////////////////////////////////////////////
        $cacheDir              = isset( $_ENV['DIR_VAR'] ) ? 
                                    $_ENV['DIR_VAR'] . '/cache' : 
                                    $this->container->getParameter( 'kernel.cache_dir' );
        $this->cache            = new PhpArrayAdapter(
            // single file where values are cached
            $cacheDir . '/vankosoft_settings.cache',
            // a backup adapter, if you set values after warmup
            new FilesystemAdapter()
        );
    }
    
    public function getSettings( $siteId )
    {
        $cacheId    = $siteId ? "settings_site_{$siteId}" : 'settings_general';
        
        $settingsCache  = $this->cache->getItem( $cacheId );
        if ( ! $settingsCache->isHit() ) {
            $settings   = $siteId ? $this->generalizeSettings( $siteId ) : $this->generalSettings();
            
            $this->cache->warmUp( [$cacheId => json_encode( $settings )] );
        } else {
            $settings   = json_decode( $settingsCache->get(), true );
        }
        
        return $settings;
    }
    
    public function saveSettings( $siteId )
    {
        $allSettings    = [];
        
        // Sites Settings
        $sites  = $this->getSiteRepository()->findAll();
        foreach ( $sites as $site ) {
            $settings   = ( $siteId == $site->getId() ) ? $this->generalizeSettings( $siteId ) : $this->getSettings( $site->getId() );
            $allSettings["settings_site_{$site->getId()}"]  = json_encode( $settings );
        }
        
        // General Settings
        $settings   = ( $siteId == null ) ? $this->generalSettings() : $this->getSettings( null );
        $allSettings['settings_general']    = json_encode( $settings );
        
        $this->cache->warmUp( $allSettings );
    }
    
    public function clearCache( $siteId, $all = false )
    {
        if ( $all ) {
            $sites  = $this->getSiteRepository()->findAll();
            foreach ( $sites as $site ) {
                $this->cache->deleteItem( "settings_site_{$site->getId()}" );
            }
            
            $this->cache->deleteItem( 'settings_general' );
        } else {
            $cacheId    = $siteId ? "settings_site_{$siteId}" : 'settings_general';
            
            $this->cache->deleteItem( $cacheId );
        }
    }
    
    public function forceMaintenanceMode( bool $maintenanceMode )
    {
        $allSettings    = [];
        
        // Sites Settings
        $sites  = $this->getSiteRepository()->findAll();
        foreach ( $sites as $site ) {
            $settings                                       = $this->getSettings( $site->getId() );
            $settings['maintenanceMode']                    = $maintenanceMode;
            
            $allSettings["settings_site_{$site->getId()}"]  = json_encode( $settings );
        }
        
        // General Settings
        $settings                           = $this->getSettings( null );
        $settings['maintenanceMode']        = $maintenanceMode;
        $allSettings['settings_general']    = json_encode( $settings );
        
        $this->cache->warmUp( $allSettings );
    }
    
    // Used For Dump/Debug
    public function getAllSettings()
    {
        $sites      = $this->getSiteRepository()->findAll();
        $settings   = [];
        foreach ( $sites as $site ) {
            $settings["settings_site_{$site->getId()}"]   = $this->getSettings( $site->getId() );
        }
        $settings['settings_general']   = $this->getSettings( null );
        
        return $settings;
    }
    
    private function generalizeSettings( $siteId ) : array
    {
        $site   = $this->getSiteRepository()->find( $siteId );
        if ( ! $site ) {
            throw new SettingsException( "Site With ID:{$siteId} Not Exists!" );
        }
        
        $generalSettings    = $this->getSettingsRepository()->getSettings();
        $siteSettings       = $this->getSettingsRepository()->getSettings( $site );
        //var_dump( $generalSettings ); die;
        
        $generalizedSettings    = [];
        foreach( $this->settingsKeys as $key ) {
            $value  = $siteSettings ? $this->propertyAccessor->getValue( $siteSettings, $key ) : null;
            if ( $value === null ) {
                $value  = $this->propertyAccessor->getValue( $generalSettings, $key );
            }
            
            $generalizedSettings[$key]  = is_object( $value ) ? $value->getId() : $value;
        }
  
        return $generalizedSettings;
    }
    
    private function generalSettings() : array
    {
        $generalSettings    = $this->getSettingsRepository()->getSettings();
        //var_dump( $generalSettings ); die;
        
        $settings    = [];
        foreach( $this->settingsKeys as $key ) {
            $value          = $this->propertyAccessor->getValue( $generalSettings, $key );
            $settings[$key] = is_object( $value ) ? $value->getId() : $value;
        }
        
        return $settings;
    }
    
    private function getSiteRepository()
    {
        return $this->container->get( 'vs_application.repository.site' );
    }
    
    private function getSettingsRepository()
    {
        return $this->container->get( 'vs_application.repository.settings' );
    }
}
