<?php

class KbAmazonDropShipManagerController extends KbAmzAdminController
{
    
    public function __construct()
    {
        add_action('wp_ajax_KbAmazonDropShipManagerrefreshAction', array($this, 'refreshAction'));
        add_action('wp_ajax_KbAmazonDropShipManagerProductDataAction', array($this, 'productDataAction'));
        add_action('wp_ajax_KbAmazonDropShipManagerRemoveAction', array($this, 'removeAction'));
        
        
    }
    
    public function managerAction()
    {
        return new KbView(array());
    }

    public function logsAction()
    {
        return new KbView(array('logs' => getKbAmz()->getOption('DropShipManagerErrors')));
    }

    public function settingsGeneralAction()
    {
        $view = parent::settingsGeneralAction();
        $view->user = wp_get_current_user();
        $view->setTemplate(KbAmazonDropShipManagerPluginPath . '/template/admin/settingsGeneral');
        return $view;
    }

    public function productDataAction()
    {
        $view = new KbView($_POST);
        $view->setTemplate($this->getTemplatePath('productData'));
        echo $view;
        exit;
    }
    
    public function refreshAction()
    {
        try {
            $importer = new KbAmazonImporter;
            $importer->import($_POST['asin']);
            $row = null;
            if (isset($_POST['postId'])) {
                $row = getDropShipManagerTableRow(get_post($_POST['postId']));
            }
            echo json_encode(array('success' => true, 'row' => $row));
        } catch (Exception $e) {
            echo json_encode(array('success' => false));
        }
        exit;
    }

    public function removeAction()
    {
        try {
            getKbAmz()->clearProduct($_POST['post']);
            echo json_encode(array('success' => true));
        } catch (Exception $e) {
            echo json_encode(array('success' => false));
        }
        exit;
    }
    
    public function importByAsinAction()
    {
        add_filter('kbAmzFilterAttributes', array($this, 'addProductMeta'));
        
        $view = parent::importByAsinAction();
        $view->setTemplate(KbAmazonStorePluginPath . '/template/admin/importByAsin');
        return $view;
    }
    
    public function addProductMeta($meta)
    {
        $meta['KbAmzDropShipManager'] = true;
        return $meta;
    }
    
    public function supportAction()
    {
        $view = parent::supportAction();
        $view->setTemplate(parent::getTemplatePath('support'));
        return $view;
    }

    public function reportsAction()
    {
        return new KbView(array());
    }

    protected function getActions() {
        return array(
            array(
                'action' => 'home',
                'icon' => 'glyphicon-th',
                'label' => __('Dashboard')
            ),
            array(
                'action' => 'manager',
                'icon' => 'glyphicon-calendar',
                'label' => __('Manager')
            ),
//            array(
//                'action' => 'reports',
//                'icon' => 'glyphicon-folder-open',
//                'label' => __('Reports')
//            ),
            array(
                'action' => 'importByAsin',
                'icon' => 'glyphicon-import',
                'label' => __('Import Item'),
            ),
            array(
                'action' => 'settingsGeneral',
                'icon' => 'glyphicon-wrench',
                'label' => __('Settings')
            ),
            array(
                'action' => 'logs',
                'icon' => 'glyphicon-flag',
                'label' => __('Logs')
            ),
            array(
                'action' => 'support',
                'icon' => 'glyphicon-question-sign',
                'label' => __('Feedback')
            ),
        );
    }
    
    protected function getTemplatePath($addup) {
        return KbAmazonDropShipManagerPluginPath . '/template/admin/' . $addup;
    }
}
// do it
KbAmazonDropShipManagerGetContoller();
function KbAmazonDropShipManagerGetContoller()
{
    static $KbAmazonDropShipManagerContoller;
    if (!$KbAmazonDropShipManagerContoller) {
        $KbAmazonDropShipManagerContoller = new KbAmazonDropShipManagerController;
    }
    return $KbAmazonDropShipManagerContoller;
}