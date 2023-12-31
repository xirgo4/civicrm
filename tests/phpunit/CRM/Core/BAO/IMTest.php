<?php

use Civi\Api4\IM;

/**
 * Class CRM_Core_BAO_IMTest
 * @group headless
 */
class CRM_Core_BAO_IMTest extends CiviUnitTestCase {

  /**
   * Create() method (create and update modes)
   */
  public function testCreate(): void {
    $contactId = $this->individualCreate();

    $params = [
      'name' => 'jane.doe',
      'provider_id' => 1,
      'is_primary' => 1,
      'location_type_id' => 1,
      'contact_id' => $contactId,
    ];

    IM::create(FALSE)->setValues($params)->execute();

    $imId = $this->assertDBNotNull('CRM_Core_DAO_IM', 'jane.doe', 'id', 'name',
      'Database check for created IM name.'
    );

    // Now call add() to modify an existing IM

    $params = [
      'id' => $imId,
      'contact_id' => $contactId,
      'provider_id' => 3,
      'name' => 'doe.jane',
    ];

    IM::update(FALSE)->addWhere('id', '=', $imId)->setValues($params)->execute();

    $isEditIM = $this->assertDBNotNull('CRM_Core_DAO_IM', $imId, 'provider_id', 'id', 'Database check on updated IM provider_name record.');
    $this->assertEquals($isEditIM, 3, 'Verify IM provider_id value is 3.');
    $isEditIM = $this->assertDBNotNull('CRM_Core_DAO_IM', $imId, 'name', 'id', 'Database check on updated IM name record.');
    $this->assertEquals($isEditIM, 'doe.jane', 'Verify IM provider_id value is doe.jane.');

    $this->contactDelete($contactId);
  }

  /**
   * AllIMs() method - get all IMs for our contact, with primary IM first
   */
  public function testAllIMs(): void {
    $this->loadXMLDataSet(dirname(__FILE__) . '/dataset/im_test.xml');

    $contactId = 69;
    $IMs = CRM_Core_BAO_IM::allIMs($contactId);

    $this->assertEquals(count($IMs), 3, 'Checking number of returned IMs.');

    $firstIMValue = array_slice($IMs, 0, 1);

    $this->assertEquals('alan1.smith1', $firstIMValue[0]['name'], 'Confirm primary IM value.');
    $this->assertEquals(1, $firstIMValue[0]['is_primary'], 'Confirm first IM is primary.');

    $this->contactDelete($contactId);
  }

}
