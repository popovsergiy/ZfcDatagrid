<?php
namespace ZfcDatagridTest\Service;

use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;
use ZfcDatagrid\Service\DatagridFactory;

/**
 * @covers ZfcDatagrid\Service\DatagridFactory
 */
class DatagridFactoryTest extends PHPUnit_Framework_TestCase
{
    private $config = [
        'ZfcDatagrid' => [
            'cache' => [
                'adapter' => [
                    'name' => 'Filesystem',
                ],
            ],
        ],
    ];

    private $applicationMock;

    public function setUp()
    {
        $mvcEventMock = $this->getMock('Zend\Mvc\MvcEvent');

        $this->applicationMock = $this->getMock('Zend\Mvc\Application', [], [], '', false);
        $this->applicationMock->expects($this->any())
            ->method('getMvcEvent')
            ->will($this->returnValue($mvcEventMock));
    }

    public function testCreateServiceException()
    {
        $this->setExpectedException('InvalidArgumentException', 'Config key "ZfcDatagrid" is missing');

        $sm = new ServiceManager();
        $sm->setService('config', []);

        $factory = new DatagridFactory();
        $grid    = $factory->createService($sm);
    }

    public function testCanCreateService()
    {
        $sm = new ServiceManager();
        $sm->setService('config', $this->config);
        $sm->setService('application', $this->applicationMock);

        $factory = new DatagridFactory();
        $grid    = $factory->createService($sm);

        $this->assertInstanceOf('ZfcDatagrid\Datagrid', $grid);
    }

    public function testCanCreateServiceWithTranslator()
    {
        $translatorMock = $this->getMock('Zend\I18n\Translator\Translator', [], [], '', false);

        $sm = new ServiceManager();
        $sm->setService('config', $this->config);
        $sm->setService('application', $this->applicationMock);
        $sm->setService('translator', $translatorMock);

        $factory = new DatagridFactory();
        $grid    = $factory->createService($sm);

        $this->assertInstanceOf('ZfcDatagrid\Datagrid', $grid);
        $this->assertEquals($translatorMock, $grid->getTranslator());
    }

    public function testCanCreateServiceWithMvcTranslator()
    {
        $mvcTranslatorMock = $this->getMock('Zend\Mvc\I18n\Translator', [], [], '', false);

        $sm = new ServiceManager();
        $sm->setService('config', $this->config);
        $sm->setService('application', $this->applicationMock);
        $sm->setService('translator', $mvcTranslatorMock);

        $factory = new DatagridFactory();
        $grid    = $factory->createService($sm);

        $this->assertInstanceOf('ZfcDatagrid\Datagrid', $grid);
        $this->assertEquals($mvcTranslatorMock, $grid->getTranslator());
    }
}
