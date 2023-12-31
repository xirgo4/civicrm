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
 * This class is to build the form for Deleting Group.
 */
class CRM_Event_Form_ManageEvent_Delete extends CRM_Event_Form_ManageEvent {

  /**
   * Page title.
   *
   * @var string
   */
  protected $_title;

  /**
   * Set variables up before form is built.
   *
   * @throws \CRM_Core_Exception
   */
  public function preProcess() {
    parent::preProcess();

    if ($this->_isTemplate) {
      $this->_title = CRM_Core_DAO::getFieldValue('CRM_Event_DAO_Event', $this->_id, 'template_title');
    }
    else {
      $this->_title = CRM_Core_DAO::getFieldValue('CRM_Event_DAO_Event', $this->_id, 'title');
    }

    if (!CRM_Event_BAO_Event::checkPermission((int) $this->_id, CRM_Core_Permission::DELETE)) {
      CRM_Core_Error::statusBounce(ts('You do not have permission to access this page.'));
    }
  }

  /**
   * Build the form object.
   */
  public function buildQuickForm() {
    $this->assign('title', $this->_title);

    $buttons = [
      [
        'type' => 'next',
        'name' => $this->_isTemplate ? ts('Delete Event Template') : ts('Delete Event'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => ts('Cancel'),
      ],
    ];
    $this->addButtons($buttons);
  }

  /**
   * Process the form when submitted.
   */
  public function postProcess() {
    $participant = new CRM_Event_DAO_Participant();
    $participant->event_id = $this->_id;

    if ($participant->find()) {
      $searchURL = CRM_Utils_System::url('civicrm/event/search', 'reset=1');
      CRM_Core_Session::setStatus(ts('This event cannot be deleted because there are participant records linked to it. If you want to delete this event, you must first find the participants linked to this event and delete them. You can use use <a href=\'%1\'> CiviEvent >> Find Participants page </a>.',
        [1 => $searchURL]
      ), ts('Deletion Error'), 'error');
      return;
    }
    CRM_Event_BAO_Event::deleteRecord(['id' => $this->_id]);
    if ($this->_isTemplate) {
      CRM_Core_Session::setStatus(ts("'%1' has been deleted.", [1 => $this->_title]), ts('Template Deleted'), 'success');
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/admin/eventTemplate', 'reset=1'));
    }
    else {
      CRM_Core_Session::setStatus(ts("'%1' has been deleted.", [1 => $this->_title]), ts('Event Deleted'), 'success');
    }
  }

}
