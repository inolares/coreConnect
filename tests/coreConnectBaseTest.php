<?php declare(strict_types=1);
/**
 * Basic tests for coreConnectBase.php
 * Only concrete methods are tested!
 * @package coreConnect
 * @author Sascha 'SieGeL' Pfalz <s.pfalz@inolares.de>
 * @copyright Inolares GmbH & Co. KG
 * @version 2.0.2 (02-Aug-2022)
 * @license BSD
 */

use inolares\coreConnectBase;
use PHPUnit\Framework\TestCase;

/**
 * PHPUnit tests for coreConnectBase class.
 * @package coreConnect
 * @author Sascha 'SieGeL' Pfalz <s.pfalz@inolares.de>
 * @version 2.0.2 (03-Aug-2022)
 */
final class coreConnectBaseTest extends TestCase
  {
  private coreConnectBase $ccb;
  
  /**
   * Provide our abstract class for tests
   * @return void
   */
  protected function setUp(): void
    {
    $this->ccb = $this->getMockForAbstractClass('inolares\coreConnectBase');
    }

  /**
   * Test that init() is working as expected
   * @return void
   * @throws Exception
   */
  public function testInit():void
    {
    $this->assertTrue($this->ccb->init('peter','pan','http://www.google.de'));
    }
  
  /**
   * Make sure that invalid URLs are correctly detected
   * @return void
   * @throws Exception
   */
  public function testInitException():void
    {
    $this->expectExceptionMessage('API URL is not valid!');
    $this->ccb->init('peter','pan','http:/invali.d.url');
    }
  
  }
