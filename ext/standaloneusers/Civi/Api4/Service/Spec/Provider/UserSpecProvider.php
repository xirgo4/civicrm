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

namespace Civi\Api4\Service\Spec\Provider;

use Civi\Api4\Service\Spec\FieldSpec;
use Civi\Api4\Service\Spec\RequestSpec;

/**
 * @service
 * @internal
 */
class UserSpecProvider extends \Civi\Core\Service\AutoService implements Generic\SpecProviderInterface {

  /**
   * @inheritDoc
   */
  public function modifySpec(RequestSpec $spec) {
    $password = new FieldSpec('password', 'User', 'String');
    $password->setTitle(ts('New password'));
    $password->setDescription('Provide a new password for this user.');
    $password->setInputType('Password');
    $spec->addFieldSpec($password);
  }

  /**
   * @inheritDoc
   */
  public function applies($entity, $action) {
    return $entity === 'User' && in_array($action, ['create', 'update', 'save']);
  }

}
