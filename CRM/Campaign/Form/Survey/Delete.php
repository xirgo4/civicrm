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
 * This class is to build the form for deleting a Survey.
 */
class CRM_Campaign_Form_Survey_Delete extends CRM_Core_Form {

  /**
   * The id of the object being deleted
   *
   * @var int
   */
  protected $_surveyId;

  /**
   * SurveyTitle
   *
   * @var string
   */
  protected $_surveyTitle;

  /**
   * Set variables up before form is built.
   */
  public function preProcess() {
    if (!CRM_Campaign_BAO_Campaign::accessCampaign()) {
      CRM_Utils_System::permissionDenied();
    }

    $this->_surveyId = CRM_Utils_Request::retrieve('id', 'Positive', $this, FALSE);
    $params = ['id' => $this->_surveyId];
    CRM_Campaign_BAO_Survey::retrieve($params, $surveyInfo);
    $this->_surveyTitle = $surveyInfo['title'];
    $this->assign('surveyTitle', $this->_surveyTitle);
    $this->setTitle(ts('Delete Survey') . ' - ' . $this->_surveyTitle);
  }

  /**
   * Build the form object.
   */
  public function buildQuickForm() {
    $this->addButtons([
      [
        'type' => 'next',
        'name' => ts('Delete'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => ts('Cancel'),
      ],
    ]);
  }

  /**
   * Process the form when submitted.
   */
  public function postProcess() {
    if ($this->_surveyId) {
      CRM_Campaign_BAO_Survey::deleteRecord(['id' => $this->_surveyId]);
      CRM_Core_Session::setStatus('', ts("'%1' survey has been deleted.", [1 => $this->_surveyTitle]), 'success');
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/campaign', 'reset=1&subPage=survey'));
    }
    else {
      CRM_Core_Error::statusBounce(ts('Delete action is missing expected survey ID.'));
    }
  }

}
