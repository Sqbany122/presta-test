<?php

/**
 * Class AdminOrdersController Responsible for orders list management
 */
class AdminOrdersController extends AdminOrdersControllerCore
{
    /**
     * AdminOrdersController class constructor
     */
	public function __construct()
	{
		parent::__construct();

		if (!$this->bulk_actions) {
			$this->bulk_actions = array();
		}

		$this->bulk_actions['print_a4'] = array(
			'text' => $this->l('Print A4 format labels'),
			'confirm' => $this->l('Print A4 format labels for selected orders?'),
			'icon' => 'icon-download dpd-a4'
		);

		$this->bulk_actions['print_label'] = array(
			'text' => $this->l('Print Label format labels'),
			'confirm' => $this->l('Print Label format labels for selected orders?'),
			'icon' => 'icon-download dpd-label'
		);

		if (!$this->actions) {
			$this->actions = array();
		}

		$this->actions[] = 'printa4';
		$this->actions[] = 'printlabel';
	}

    /**
     * Displays a button used to print A4 format labels in orders list
     *
     * @param string $token Orders list page token
     * @param int $row_id Identifier
     * @return string Button HTML code
     */
	public function displayPrinta4Link($token, $row_id)
	{
		$button_title = $this->l('Print A4 format label');
		$template = 'list_action.tpl';
		$action = 'printA4FormatLabel';
		$target = 'id_order';

		return $this->displayActionButton($row_id, $template, $action, $target, $button_title);
	}

    /**
     * Displays a button used to print label format labels in orders list
     *
     * @param string $token Orders list page token
     * @param int $row_id Identifier
     * @return string Button HTML code
     */
	public function displayPrintlabelLink($token, $row_id)
	{
		$button_title = $this->l('Print label format label');
		$template = 'list_action.tpl';
		$action = 'printLabelFormatLabel';
		$target = 'id_order';

		return $this->displayActionButton($row_id, $template, $action, $target, $button_title);
	}

    /**
     * Displays action button in orders list
     *
     * @param int $row_id Identifier
     * @param string $template_name Template name
     * @param $action
     * @param $target
     * @param $button_title
     * @return string
     */
	private function displayActionButton($row_id, $template_name, $action, $target, $button_title)
	{
		Module::getInstanceByName('dpdpoland');
		$current_index = $this->context->link->getAdminLink('AdminOrders');
		$helper = new HelperList();
		$helper->base_folder = _DPDPOLAND_TPL_DIR_.'admin/';
		$tpl = $helper->createTemplate($template_name);

		$tpl->assign(array(
			'href' => $current_index.'&'.$target.'='.$row_id.'&'.$action,
			'action' => $button_title,
			'icon' => 'icon-download',
			'ps_15' => version_compare(_PS_VERSION_, '1.6', '<')
		));

		return $tpl->fetch();
	}

    /**
     * Main controller function used to manage actions
     */
	public function postProcess()
	{
		parent::postProcess();

		if (Tools::isSubmit('submitBulkprint_a4order') && Module::isEnabled('dpdpoland')) {
			$module_instance = Module::getInstanceByName('dpdpoland');
			$module_instance->printMultipleLabels(DpdPolandConfiguration::PRINTOUT_FORMAT_A4);
		}

		if (Tools::isSubmit('submitBulkprint_labelorder') && Module::isEnabled('dpdpoland')) {
			$module_instance = Module::getInstanceByName('dpdpoland');
			$module_instance->printMultipleLabels(DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL);
		}

		if (Tools::isSubmit('printA4FormatLabel') && Module::isEnabled('dpdpoland')) {
			$module_instance = Module::getInstanceByName('dpdpoland');
			$module_instance->printSingleLabel(DpdPolandConfiguration::PRINTOUT_FORMAT_A4);
		}

		if (Tools::isSubmit('printLabelFormatLabel') && Module::isEnabled('dpdpoland')) {
			$module_instance = Module::getInstanceByName('dpdpoland');
			$module_instance->printSingleLabel(DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL);
		}
	}
}

