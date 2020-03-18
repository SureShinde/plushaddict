<?php
/**
 * @author     DAPL TEAM
 * @package    DAPL_OverRide
 */

namespace DAPL\OverRide\Ui\Component;

class MassAction extends \Magento\Catalog\Ui\Component\Product\MassAction
{
    /**
     * @inheritDoc
     */
    public function prepare() : void
    {
        $config = $this->getConfiguration();

        foreach ($this->getChildComponents() as $actionComponent) {
            $actionType = $actionComponent->getConfiguration()['type'];
            if ($this->isActionAllowed($actionType)) {
                $config['actions'][] = $actionComponent->getConfiguration();
            }
        }
        $origConfig = $this->getConfiguration();
        if ($origConfig !== $config) {
            $config = array_replace_recursive($config, $origConfig);
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $authSession = $objectManager->create("Magento\Backend\Model\Auth\Session");
        $currentUser = $authSession->getUser();
        if ($currentUser->getEmail()!='jon@plushaddict.co.uk') {
            $allowedActions = [];
            foreach ($config['actions'] as $action) {
                if ($action['type']!='delete' ) {
                    $allowedActions[] = $action;
                }
            }
            $config['actions'] = $allowedActions;
        } 
        $this->setData('config', $config);
        $this->components = [];

        parent::prepare();
    }

}

