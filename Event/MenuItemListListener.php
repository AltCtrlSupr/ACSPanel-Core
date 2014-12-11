<?php
namespace ACS\ACSPanelBundle\Event;

// ...

//use Ilimit\DomainCheckBundle\Model\MenuItemModel;
use Avanzu\AdminThemeBundle\Model\MenuItemModel;
use Avanzu\AdminThemeBundle\Event\SidebarMenuEvent;
use Symfony\Component\HttpFoundation\Request;

class MenuItemListListener {

    private $menu_builder;

    public function onSetupMenu(SidebarMenuEvent $event) {

        $request = $event->getRequest();

        foreach ($this->getMenu($request) as $item) {
            $event->addItem($item);
        }

    }

    protected function getMenu(Request $request) {
        // retrieve your menuItem models/entities here
        $menuItems = array();

        $earg      = array();

	//ldd($this->menu_builder);
	//$superadminMenu = $this->menu_builder->superadminMenu();
	//ldd($superadminMenu);

        $menuItems[] = new MenuItemModel('domain_index','Dominis', 'domain', $earg, 'fa fa-dashboard');
        $menuItems[] = new MenuItemModel('csv_upload','Importar CSV', 'httpdhost', $earg, 'fa fa-dashboard');
        $menuItems[] = new MenuItemModel('log_index','Registres', 'db', $earg, 'fa fa-dashboard');

        return $this->activateByRoute($request->get('_route'), $menuItems);
    }

    protected function activateByRoute($route, $items) {

        foreach($items as $item) {
            if($item->hasChildren()) {
                $this->activateByRoute($route, $item->getChildren());
            }
            else {
                if($item->getRoute() == $route) {
                    $item->setIsActive(true);
                }
            }
        }

        return $items;
    }

    public function setMenuBuilder($builder)
    {
	    $this->menu_builder = $builder;
    }
}

