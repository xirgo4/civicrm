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
 * Upgrade logic for the 5.47.x series.
 *
 * Each minor version in the series is handled by either a `5.47.x.mysql.tpl` file,
 * or a function in this class named `upgrade_5_47_x`.
 * If only a .tpl file exists for a version, it will be run automatically.
 * If the function exists, it must explicitly add the 'runSql' task if there is a corresponding .mysql.tpl.
 *
 * This class may also implement `setPreUpgradeMessage()` and `setPostUpgradeMessage()` functions.
 */
class CRM_Upgrade_Incremental_php_FiveFortySeven extends CRM_Upgrade_Incremental_Base {

  /**
   * Upgrade step; adds tasks including 'runSql'.
   *
   * @param string $rev
   *   The version number matching this function name
   */
  public function upgrade_5_47_alpha1($rev): void {
    $this->addTask(ts('Upgrade DB to %1: SQL', [1 => $rev]), 'runSql', $rev);
    $this->addTask('Migrate CiviGrant component to an extension', 'migrateCiviGrant');
  }

  /**
   * @param \CRM_Queue_TaskContext $ctx
   * @return bool
   */
  public static function migrateCiviGrant(CRM_Queue_TaskContext $ctx): bool {
    $civiGrantEnabled = in_array('CiviGrant', Civi::settings()->get('enable_components'), TRUE);
    if ($civiGrantEnabled) {
      CRM_Core_BAO_ConfigSetting::disableComponent('CiviGrant');
    }
    $civiGrantId = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Component', 'CiviGrant', 'id', 'name');
    if ($civiGrantId) {
      foreach (['civicrm_menu', 'civicrm_option_value'] as $table) {
        CRM_Core_DAO::executeQuery("UPDATE $table SET component_id = NULL WHERE component_id = $civiGrantId", [], TRUE, NULL, FALSE, FALSE);
      }
      CRM_Core_DAO::executeQuery("DELETE FROM civicrm_component WHERE name = 'CiviGrant'", [], TRUE, NULL, FALSE, FALSE);
    }
    $ext = new CRM_Core_DAO_Extension();
    $ext->full_name = 'civigrant';
    if (!$ext->find(TRUE)) {
      $ext->type = 'module';
      $ext->name = 'CiviGrant';
      $ext->label = ts('CiviGrant');
      $ext->file = 'civigrant';
      $ext->is_active = (int) $civiGrantEnabled;
      $ext->save();

      $managedItems = [
        'OptionGroup_advanced_search_options_OptionValue_CiviGrant' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'advanced_search_options',
            'name' => 'CiviGrant',
          ],
        ],
        'OptionGroup_contact_view_options_OptionValue_CiviGrant' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'contact_view_options',
            'name' => 'CiviGrant',
          ],
        ],
        'OptionGroup_mapping_type_OptionValue_Export Grant' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'mapping_type',
            'name' => 'Export Grant',
          ],
        ],
        'OptionGroup_grant_status' => [
          'entity' => 'OptionGroup',
          'values' => [
            'name' => 'grant_status',
          ],
        ],
        'OptionGroup_grant_status_OptionValue_Submitted' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'grant_status',
            'name' => 'Submitted',
          ],
        ],
        'OptionGroup_grant_status_OptionValue_Eligible' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'grant_status',
            'name' => 'Eligible',
          ],
        ],
        'OptionGroup_grant_status_OptionValue_Ineligible' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'grant_status',
            'name' => 'Ineligible',
          ],
        ],
        'OptionGroup_grant_status_OptionValue_Paid' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'grant_status',
            'name' => 'Paid',
          ],
        ],
        'OptionGroup_grant_status_OptionValue_Awaiting Information' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'grant_status',
            'name' => 'Awaiting Information',
          ],
        ],
        'OptionGroup_grant_status_OptionValue_Withdrawn' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'grant_status',
            'name' => 'Withdrawn',
          ],
        ],
        'OptionGroup_grant_status_OptionValue_Approved for Payment' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'grant_status',
            'name' => 'Approved for Payment',
          ],
        ],
        'OptionGroup_grant_type' => [
          'entity' => 'OptionGroup',
          'values' => [
            'name' => 'grant_type',
          ],
        ],
        'OptionGroup_grant_type_OptionValue_Emergency' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'grant_type',
            'name' => 'Emergency',
          ],
        ],
        'OptionGroup_grant_type_OptionValue_Family Support' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'grant_type',
            'name' => 'Family Support',
          ],
        ],
        'OptionGroup_grant_type_OptionValue_General Protection' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'grant_type',
            'name' => 'General Protection',
          ],
        ],
        'OptionGroup_grant_type_OptionValue_Impunity' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'grant_type',
            'name' => 'Impunity',
          ],
        ],
        'OptionGroup_report_template_OptionValue_CRM_Report_Form_Grant_Detail' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'report_template',
            'name' => 'CRM_Report_Form_Grant_Detail',
          ],
        ],
        'OptionGroup_report_template_OptionValue_CRM_Report_Form_Grant_Statistics' => [
          'entity' => 'OptionValue',
          'values' => [
            'option_group_id:name' => 'report_template',
            'name' => 'CRM_Report_Form_Grant_Statistics',
          ],
        ],
        'Navigation_Grants' => [
          'entity' => 'Navigation',
          'values' => [
            'name' => 'Grants',
            'domain_id' => 'current_domain',
          ],
        ],
        'Navigation_Grants_Navigation_Dashboard' => [
          'entity' => 'Navigation',
          'values' => [
            'name' => 'Dashboard',
            'url' => 'civicrm/grant?reset=1',
            'domain_id' => 'current_domain',
          ],
        ],
        'Navigation_Grants_Navigation_New_Grant' => [
          'entity' => 'Navigation',
          'values' => [
            'name' => 'New Grant',
            'url' => 'civicrm/grant/add?reset=1&action=add&context=standalone',
            'domain_id' => 'current_domain',
          ],
        ],
        'Navigation_Grants_Navigation_Find_Grants' => [
          'entity' => 'Navigation',
          'values' => [
            'name' => 'Find Grants',
            'url' => 'civicrm/grant/search?reset=1',
            'domain_id' => 'current_domain',
          ],
        ],
        'Navigation_CiviGrant' => [
          'entity' => 'Navigation',
          'values' => [
            'name' => 'CiviGrant',
            'domain_id' => 'current_domain',
          ],
        ],
        'Navigation_CiviGrant_Navigation_Grant_Types' => [
          'entity' => 'Navigation',
          'values' => [
            'name' => 'Grant Types',
            'domain_id' => 'current_domain',
          ],
        ],
        'Navigation_CiviGrant_Navigation_Grant_Status' => [
          'entity' => 'Navigation',
          'values' => [
            'name' => 'Grant Status',
            'domain_id' => 'current_domain',
          ],
        ],
        'Navigation_Grants_Navigation_Grant_Reports' => [
          'entity' => 'Navigation',
          'values' => [
            'name' => 'Grant Reports',
            'domain_id' => 'current_domain',
          ],
        ],
      ];
      // Create an entry in civicrm_managed for each existing record that will be managed by the extension
      foreach ($managedItems as $name => $item) {
        $params = ['checkPermissions' => FALSE];
        foreach ($item['values'] as $k => $v) {
          $params['where'][] = [$k, '=', $v];
        }
        $record = civicrm_api4($item['entity'], 'get', $params)->first();
        if ($record) {
          $mgd = new CRM_Core_DAO_Managed();
          $mgd->name = $name;
          $mgd->module = 'civigrant';
          $mgd->entity_type = $item['entity'];
          if (!$mgd->find(TRUE)) {
            $mgd->entity_id = $record['id'];
            $mgd->cleanup = 'unused';
            $mgd->save();
          }
          // Disable record if CiviGrant disabled
          if (!$civiGrantEnabled && !empty($record['is_active'])) {
            civicrm_api4($item['entity'], 'update', [
              'checkPermissions' => FALSE,
              'values' => ['id' => $record['id'], 'is_active' => FALSE],
            ]);
          }
        }
      }
    }
    return TRUE;
  }

}