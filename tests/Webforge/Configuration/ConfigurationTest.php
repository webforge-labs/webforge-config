<?php

namespace Webforge\Configuration;

/**
 */
class ConfigurationTest extends \Webforge\Code\Test\Base {
  
  protected $configuration;
  
  public function setUp() {
    $conf = array(
      'webforge.staging'=>true,
      'webforge.dev'=>false,
      'webforge.location'=>'D:\www\ka',
      'doctrine.entities'=>array('user','project','customer'),
      'root'=>'thv'
    );

    $this->configuration = new Configuration($conf);
  }
  
  public function testGetReturnsTheValueForTheKey() {
    $this->assertEquals('D:\www\ka', $this->configuration->get('webforge.location'));
    $this->assertEquals(array('user','project','customer'), $this->configuration->get('doctrine.entities'));
    $this->assertEquals('thv', $this->configuration->get('root'));
  }

  public function testReturnsDefaultGivenForKeyWhichIsNotSet() {
    $this->assertEquals(array(), $this->configuration->get('webforge.translations', array()));
  }

  /**
   * @expectedException Webforge\Configuration\MissingConfigVariableException
   */
  public function testMissingConfigException() {
    $this->configuration->req('thiskeydoesnotexist');
  }
  
  /**
   * @depends testMissingConfigException
   */
  public function testKeysExceptionForNonDott() {
    try {
      $this->configuration->req('thiskeydoesnotexist');
      
    } catch (MissingConfigVariableException $e) {
      $this->assertEquals(array('thiskeydoesnotexist'), $e->getKeys());
      return;
    }
    
    $this->fail('Exception not cought');
  }

  /**
   * @depends testMissingConfigException
   */
  public function testKeysExceptionForDott() {
    try {
      $this->configuration->req('thiskey.doesnotexist');
      
    } catch (MissingConfigVariableException $e) {
      $this->assertEquals(array('thiskey','doesnotexist'), $e->getKeys());
      return;
    }
    
    $this->fail('Exception not cought');
  }


  public function testToArrayReturnsTheConfigurationAsArray() {
    $conf = array(
      'webforge' => array(
        'staging'=>true,
        'dev'=>false,
        'location'=>'D:\www\ka',
      ),
      'doctrine'=>array(
        'entities'=>array('user','project','customer'),
      ),
      'root'=>'thv'
    );

    $this->assertEquals(
      $conf,
      $this->configuration->toArray()
    );
  }

  public function testMergeWithFilteredKeys() {
    $defaults = new Configuration(array('defaults'=>array(
      'root'=>'will-be-overridden',
      'new'=>'inherited'
    )));

    $mergedConfig = new Configuration(array());
    $mergedConfig->merge($defaults, array('defaults'));

    $mergedConfig->merge($this->configuration);

    $this->assertNotEquals('will-be-overridden', $mergedConfig->req('root'));
    $this->assertEquals('inherited', $mergedConfig->req('new'));
  }
}
