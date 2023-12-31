<?php

/**
 * Class CRM_Core_BAO_CustomValueTableMultipleTest
 * @group headless
 */
class CRM_Core_BAO_CustomValueTableMultipleTest extends CiviUnitTestCase {

  public function testCustomGroupMultipleSingle(): void {
    $contactID = $this->individualCreate();
    $customGroup = $this->customGroupCreate(['is_multiple' => 1]);
    $fields = [
      'custom_group_id' => $customGroup['id'],
      'dataType' => 'String',
      'htmlType' => 'Text',
    ];
    $customField = $this->customFieldCreate($fields);

    $params = [
      'entityID' => $contactID,
      "custom_{$customField['id']}_-1" => 'First String',
    ];
    CRM_Core_BAO_CustomValueTable::setValues($params);

    $newParams = [
      'entityID' => $contactID,
      "custom_{$customField['id']}" => 1,
    ];
    $result = CRM_Core_BAO_CustomValueTable::getValues($newParams);

    $this->assertEquals($params["custom_{$customField['id']}_-1"], $result["custom_{$customField['id']}_1"]);
    $this->assertEquals($params['entityID'], $result['entityID']);

    $updateParams = [
      'id' => 1,
      'entityID' => $contactID,
      "custom_{$customField['id']}" => 2,
    ];
    CRM_Core_BAO_CustomValueTable::setValues($updateParams);

    $criteria = [
      'id' => 1,
      'entityID' => $contactID,
    ];
    $result = CRM_Core_BAO_CustomValueTable::getValues($criteria);
    $this->assertEquals(2, $result["custom_{$customField['id']}_1"]);

    $this->customFieldDelete($customField['id']);
    $this->customGroupDelete($customGroup['id']);
    $this->contactDelete($contactID);
  }

  public function testCustomGroupMultipleDouble(): void {
    $contactID = $this->individualCreate();
    $customGroup = $this->customGroupCreate(['is_multiple' => 1]);
    $fields = [
      'custom_group_id' => $customGroup['id'],
      'dataType' => 'String',
      'htmlType' => 'Text',
    ];
    $customField = $this->customFieldCreate($fields);

    $params = [
      'entityID' => $contactID,
      "custom_{$customField['id']}_-1" => 'First String',
      "custom_{$customField['id']}_-2" => 'Second String',
    ];
    CRM_Core_BAO_CustomValueTable::setValues($params);

    $newParams = [
      'entityID' => $contactID,
      "custom_{$customField['id']}" => 1,
    ];
    $result = CRM_Core_BAO_CustomValueTable::getValues($newParams);

    $this->assertEquals($params["custom_{$customField['id']}_-1"], $result["custom_{$customField['id']}_1"]);
    $this->assertEquals($params["custom_{$customField['id']}_-2"], $result["custom_{$customField['id']}_2"]);
    $this->assertEquals($params['entityID'], $result['entityID']);

    $this->customFieldDelete($customField['id']);
    $this->customGroupDelete($customGroup['id']);
    $this->contactDelete($contactID);
  }

  public function testCustomGroupMultipleUpdate(): void {
    $contactID = $this->individualCreate();
    $customGroup = $this->customGroupCreate(['is_multiple' => 1]);
    $fields = [
      'custom_group_id' => $customGroup['id'],
      'dataType' => 'String',
      'htmlType' => 'Text',
    ];
    $customField = $this->customFieldCreate($fields);

    $params = [
      'entityID' => $contactID,
      "custom_{$customField['id']}_-1" => 'First String',
      "custom_{$customField['id']}_-2" => 'Second String',
      "custom_{$customField['id']}_-3" => 'Third String',
    ];
    CRM_Core_BAO_CustomValueTable::setValues($params);

    $newParams = [
      'entityID' => $contactID,
      "custom_{$customField['id']}_1" => 'Updated First String',
      "custom_{$customField['id']}_3" => 'Updated Third String',
    ];
    CRM_Core_BAO_CustomValueTable::setValues($newParams);

    $getParams = [
      'entityID' => $contactID,
      "custom_{$customField['id']}" => 1,
    ];
    $result = CRM_Core_BAO_CustomValueTable::getValues($getParams);

    $this->assertEquals($newParams["custom_{$customField['id']}_1"], $result["custom_{$customField['id']}_1"]);
    $this->assertEquals($params["custom_{$customField['id']}_-2"], $result["custom_{$customField['id']}_2"]);
    $this->assertEquals($newParams["custom_{$customField['id']}_3"], $result["custom_{$customField['id']}_3"]);
    $this->assertEquals($params['entityID'], $result['entityID']);

    $this->customFieldDelete($customField['id']);
    $this->customGroupDelete($customGroup['id']);
    $this->contactDelete($contactID);
  }

  public function testCustomGroupMultipleOldFormat(): void {
    $contactID = $this->individualCreate();
    $customGroup = $this->customGroupCreate(['is_multiple' => 1]);
    $fields = [
      'custom_group_id' => $customGroup['id'],
      'dataType' => 'String',
      'htmlType' => 'Text',
    ];
    $customField = $this->customFieldCreate($fields);

    $params = [
      'entityID' => $contactID,
      "custom_{$customField['id']}" => 'First String',
    ];
    CRM_Core_BAO_CustomValueTable::setValues($params);

    $newParams = [
      'entityID' => $contactID,
      "custom_{$customField['id']}" => 1,
    ];
    $result = CRM_Core_BAO_CustomValueTable::getValues($newParams);

    $this->assertEquals($params["custom_{$customField['id']}"], $result["custom_{$customField['id']}_1"]);
    $this->assertEquals($params['entityID'], $result['entityID']);

    $this->customFieldDelete($customField['id']);
    $this->customGroupDelete($customGroup['id']);
    $this->contactDelete($contactID);
  }

}
