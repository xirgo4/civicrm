<?php

/**
 * Class CRM_Utils_Cache_SqlGroupTest
 * @group headless
 */
class CRM_Utils_Cache_SqlGroupTest extends CiviUnitTestCase {

  /**
   * Add and remove two items from the same cache instance.
   */
  public function testSameInstance(): void {
    $a = new CRM_Utils_Cache_SqlGroup([
      'group' => 'testSameInstance',
    ]);
    $this->assertDBQuery(0, 'SELECT count(*) FROM civicrm_cache WHERE group_name = "testSameInstance"');
    $fooValue = ['whiz' => 'bang', 'bar' => 2];
    $a->set('foo', $fooValue);
    $this->assertDBQuery(1, 'SELECT count(*) FROM civicrm_cache WHERE group_name = "testSameInstance"');
    $this->assertEquals($a->get('foo'), ['whiz' => 'bang', 'bar' => 2]);

    $barValue = 45.78;
    $a->set('bar', $barValue);
    $this->assertDBQuery(2, 'SELECT count(*) FROM civicrm_cache WHERE group_name = "testSameInstance"');
    $this->assertEquals($a->get('bar'), 45.78);

    $a->delete('foo');
    $this->assertDBQuery(1, 'SELECT count(*) FROM civicrm_cache WHERE group_name = "testSameInstance"');

    $a->flush();
    $this->assertDBQuery(0, 'SELECT count(*) FROM civicrm_cache WHERE group_name = "testSameInstance"');
  }

  /**
   * Add item to one cache instance then read with another.
   */
  public function testTwoInstance(): void {
    $a = new CRM_Utils_Cache_SqlGroup([
      'group' => 'testTwoInstance',
    ]);
    $fooValue = ['whiz' => 'bang', 'bar' => 3];
    $a->set('foo', $fooValue);
    $getValue = $a->get('foo');
    $expectValue = ['whiz' => 'bang', 'bar' => 3];
    $this->assertEquals($getValue, $expectValue);

    $b = new CRM_Utils_Cache_SqlGroup([
      'group' => 'testTwoInstance',
      'prefetch' => FALSE,
    ]);
    $this->assertEquals($b->get('foo'), ['whiz' => 'bang', 'bar' => 3]);
  }

  /**
   * Add item to one cache instance then read (with or without prefetch) from another
   */
  public function testPrefetch(): void {
    // 1. put data in cache
    $a = new CRM_Utils_Cache_SqlGroup([
      'group' => 'testPrefetch',
      'prefetch' => FALSE,
    ]);
    $fooValue = ['whiz' => 'bang', 'bar' => 4];
    $a->set('foo', $fooValue);
    $this->assertEquals($a->get('foo'), ['whiz' => 'bang', 'bar' => 4]);

    // 2. see what happens when prefetch is TRUE
    $b = new CRM_Utils_Cache_SqlGroup([
      'group' => 'testPrefetch',
      'prefetch' => TRUE,
    ]);
    // should work b/c value was prefetched
    $this->assertEquals($fooValue, $b->getFromFrontCache('foo'));
    // should work b/c value was prefetched
    $this->assertEquals($fooValue, $b->get('foo'));

    // 3. see what happens when prefetch is FALSE
    $c = new CRM_Utils_Cache_SqlGroup([
      'group' => 'testPrefetch',
      'prefetch' => FALSE,
    ]);
    // should be NULL b/c value was NOT prefetched
    $this->assertEquals(NULL, $c->getFromFrontCache('foo'));
    // should work b/c value is fetched on demand
    $this->assertEquals($fooValue, $c->get('foo'));
    // should work b/c value was fetched on demand
    $this->assertEquals($fooValue, $c->getFromFrontCache('foo'));
  }

}
