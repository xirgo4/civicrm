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
 * This class generates form components
 * for previewing Civicrm Profile Group
 *
 */
class CRM_UF_Form_Preview extends CRM_UF_Form_AbstractPreview {

  /**
   * Pre processing work done here.
   *
   * gets session variables for group or field id
   *
   * @return void
   */
  public function preProcess() {
    $flag = FALSE;
    $gid = CRM_Utils_Request::retrieve('gid', 'Integer', $this, TRUE);
    $fieldId = CRM_Utils_Request::retrieve('fieldId', 'Integer', $this, FALSE, 0);

    if ($fieldId) {
      $fields = CRM_Core_BAO_UFGroup::getFields($gid, FALSE, NULL, NULL, NULL, TRUE);
      $fieldDAO = new CRM_Core_DAO_UFField();
      $fieldDAO->id = $this->get('fieldId');
      $fieldDAO->find(TRUE);

      if ($fieldDAO->is_active == 0) {
        CRM_Core_Error::statusBounce(ts('This field is inactive so it will not be displayed on profile form.'));
      }
      elseif ($fieldDAO->is_view == 1) {
        CRM_Core_Error::statusBounce(ts('This field is view only so it will not be displayed on profile form.'));
      }
      $name = $fieldDAO->field_name;

      if ($fieldDAO->field_name === 'phone_and_ext') {
        $name = 'phone';
      }

      // preview for field
      $specialFields = [
        'address_name',
        'street_address',
        'supplemental_address_1',
        'supplemental_address_2',
        'supplemental_address_3',
        'city',
        'postal_code',
        'postal_code_suffix',
        'geo_code_1',
        'geo_code_2',
        'state_province',
        'country',
        'county',
        'phone',
        'email',
        'im',
      ];

      if ($fieldDAO->location_type_id) {
        $name .= '-' . $fieldDAO->location_type_id;
      }
      elseif (in_array($name, $specialFields)) {
        $name .= '-Primary';
      }

      if (isset($fieldDAO->phone_type_id)) {
        $name .= '-' . $fieldDAO->phone_type_id;
      }

      $fieldArray[$name] = $fields[$name];

      if ($fieldDAO->field_name === 'phone_and_ext') {
        $phoneExtField = str_replace('phone', 'phone_ext', $name);
        $fieldArray[$phoneExtField] = $fields[$phoneExtField];
      }

      $fields = $fieldArray;
      if (!is_array($fields[$name])) {
        $flag = TRUE;
      }
      $this->setProfile($fields, TRUE, $flag);
    }
    else {
      $fields = CRM_Core_BAO_UFGroup::getFields($gid);
      $this->setProfile($fields);
    }
  }

  /**
   * Build the form object.
   *
   * @return void
   */
  public function buildQuickForm() {
    parent::buildQuickForm();

    $this->addButtons([
      [
        'type' => 'cancel',
        'name' => ts('Done with Preview'),
        'isDefault' => TRUE,
      ],
    ]);
  }

}
