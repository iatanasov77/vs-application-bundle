<?php namespace Vankosoft\ApplicationBundle\EventListener\Widgets;

use Vankosoft\ApplicationBundle\Component\Widget\Builder\Item;
use Vankosoft\ApplicationBundle\EventListener\Event\WidgetEvent;

/**
 * MANUAL: https://github.com/cesurapp/pd-widget
 */
class LocalesWidget
{
    public function builder( WidgetEvent $event )
    {
        // Get Widget Container
        $widgets    = $event->getWidgetContainer();
        
        // Create Widget Item
        $widgetItem = new Item( 'profile_menu_locales', 3600 );
        $widgetItem->setGroup( 'admin_profile_menu' )
                    ->setName( 'widget_user_info.name' )
                    ->setDescription( 'widget_user_info.description' )
                    ->setActive( true )
                    ->setTemplate( '@VSApplication/Widgets/locales.html.twig' );
                        
        // Add Widgets
        $widgets->addWidget( $widgetItem );
    }
}