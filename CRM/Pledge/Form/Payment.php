<?php
/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 */

/**
 * This class generates form components for processing a pledge payment.
 */
class CRM_Pledge_Form_Payment extends CRM_Core_Form {

  /**
   * The id of the pledge payment that we are proceessing.
   *
   * @var int
   */
  public $_id;

  /**
   * Explicitly declare the entity api name.
   */
  public function getDefaultEntity() {
    return 'PledgePayment';
  }

  /**
   * Explicitly declare the form context.
   */
  public function getDefaultContext() {
    return 'create';
  }

  /**
   * Set variables up before form is built.
   *
   * @throws \CRM_Core_Exception
   */
  public function preProcess() {
    // check for edit permission
    if (!CRM_Core_Permission::check('edit pledges')) {
      CRM_Core_Error::statusBounce(ts('You do not have permission to access this page.'));
    }

    $this->_id = CRM_Utils_Request::retrieve('ppId', 'Positive', $this);

    $this->setTitle(ts('Edit Scheduled Pledge Payment'));
  }

  /**
   * Set default values for the form.
   * the default values are retrieved from the database.
   */
  public function setDefaultValues() {
    $defaults = [];
    if ($this->_id) {
      $params['id'] = $this->_id;
      CRM_Pledge_BAO_PledgePayment::retrieve($params, $defaults);
      if (isset($defaults['contribution_id'])) {
        $this->assign('pledgePayment', TRUE);
      }
      $status = CRM_Core_PseudoConstant::getName('CRM_Pledge_BAO_Pledge', 'status_id', $defaults['status_id']);
      $this->assign('status', $status);
    }
    $defaults['option_type'] = 1;
    return $defaults;
  }

  /**
   * Build the form object.
   */
  public function buildQuickForm() {
    // add various dates
    $this->addField('scheduled_date', [], TRUE, FALSE);

    $this->addMoney('scheduled_amount',
      ts('Scheduled Amount'), TRUE,
      ['readonly' => TRUE],
      TRUE,
      'currency',
      NULL,
      TRUE
    );

    $optionTypes = [
      '1' => ts('Adjust Pledge Payment Schedule?'),
      '2' => ts('Adjust Total Pledge Amount?'),
    ];
    $element = $this->addRadio('option_type',
      NULL,
      $optionTypes,
      [], '<br/>'
    );

    $this->addButtons([
        [
          'type' => 'next',
          'name' => ts('Save'),
          'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
          'isDefault' => TRUE,
        ],
        [
          'type' => 'cancel',
          'name' => ts('Cancel'),
        ],
    ]);
  }

  /**
   * Process the form submission.
   */
  public function postProcess() {
    $formValues = $this->controller->exportValues($this->_name);
    $params = [
      'id' => $this->_id,
      'scheduled_date' => $formValues['scheduled_date'],
      'currency' => $formValues['currency'],
    ];

    if (CRM_Utils_Date::overdue($params['scheduled_date'])) {
      $params['status_id'] = CRM_Core_PseudoConstant::getKey('CRM_Pledge_BAO_Pledge', 'status_id', 'Overdue');
    }
    else {
      $params['status_id'] = CRM_Core_PseudoConstant::getKey('CRM_Pledge_BAO_Pledge', 'status_id', 'Pending');
    }

    $pledgeId = CRM_Core_DAO::getFieldValue('CRM_Pledge_DAO_PledgePayment', $params['id'], 'pledge_id');

    CRM_Pledge_BAO_PledgePayment::add($params);
    $adjustTotalAmount = (($formValues['option_type'] ?? NULL) == 2);

    $pledgeScheduledAmount = CRM_Core_DAO::getFieldValue('CRM_Pledge_DAO_PledgePayment',
      $params['id'],
      'scheduled_amount',
      'id'
    );

    $oldestPaymentAmount = CRM_Pledge_BAO_PledgePayment::getOldestPledgePayment($pledgeId, 2);
    if (($oldestPaymentAmount['count'] != 1) && ($oldestPaymentAmount['id'] == $params['id'])) {
      $oldestPaymentAmount = CRM_Pledge_BAO_PledgePayment::getOldestPledgePayment($pledgeId);
    }
    if (($formValues['scheduled_amount'] - $pledgeScheduledAmount) >= $oldestPaymentAmount['amount']) {
      $adjustTotalAmount = TRUE;
    }
    // update pledge status
    CRM_Pledge_BAO_PledgePayment::updatePledgePaymentStatus($pledgeId,
      [$params['id']],
      $params['status_id'],
      NULL,
      $formValues['scheduled_amount'],
      $adjustTotalAmount
    );

    $statusMsg = ts('Pledge Payment Schedule has been updated.');
    CRM_Core_Session::setStatus($statusMsg, ts('Saved'), 'success');
  }

}
