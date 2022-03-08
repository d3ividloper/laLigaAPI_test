<?php

namespace App\Tests\Service;

use App\Entity\Club;
use App\Form\Model\ClubDto;
use App\Service\Club\ClubDtoManager;
use App\Service\Club\ClubFormProcessor;
use App\Service\Club\ClubManager;
use App\Service\Common\FileUploader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;


class ClubFormProcessorTest extends TestCase
{
    public function testSuccess() {
        $club = new Club();
        $clubDto = new ClubDto();
        $clubDto->base64Badge = '';
        $clubDto->name = '';
        $clubDto->budget = 0;

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $clubDtoManager = $this->createMock(ClubDtoManager::class);
        $form = $this->createMock(FormInterface::class);
        $fileUploader = $this->createMock(FileUploader::class);
        $clubManager = $this->createMock(ClubManager::class);

        $clubDtoManager->expects($this->any())
        ->method('create')
        ->willReturn($clubDto);

        $clubManager
            ->expects($this->any())
            ->method('find')
            ->willReturn($club);

        $clubManager
            ->expects($this->any())
            ->method('create')
            ->willReturn($club);

        $formFactory->expects($this->any())
            ->method('create')
            ->willReturn($form);

        $fileUploader->expects($this->any())
            ->method('uploadBase64File')
            ->willReturn("");

        $clubManager
            ->expects($this->any())
            ->method('save')
            ->willReturn($club);

        $form->expects($this->any())
            ->method('isValid')
            ->willReturn(true);

        $form->expects($this->any())
            ->method('isSubmitted')
            ->willReturn(true);

        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue(json_encode([])));

        $formProcessor = new ClubFormProcessor($fileUploader, $formFactory,$clubDtoManager, $clubManager);
        $response=$formProcessor($request, 1);
        $this->assertEquals([$club,null], $response);
    }

    public function testFailFormNotValid() {
        $club = new Club();
        $clubDto = new ClubDto();
        $clubDto->base64Badge = '';
        $clubDto->name = '';
        $clubDto->budget = 0;

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $clubDtoManager = $this->createMock(ClubDtoManager::class);
        $form = $this->createMock(FormInterface::class);
        $fileUploader = $this->createMock(FileUploader::class);
        $clubManager = $this->createMock(ClubManager::class);

        $clubDtoManager->expects($this->any())
            ->method('create')
            ->willReturn($clubDto);

        $clubManager
            ->expects($this->any())
            ->method('find')
            ->willReturn($club);

        $clubManager
            ->expects($this->any())
            ->method('create')
            ->willReturn($club);

        $formFactory->expects($this->any())
            ->method('create')
            ->willReturn($form);

        $fileUploader->expects($this->any())
            ->method('uploadBase64File')
            ->willReturn("");

        $clubManager
            ->expects($this->any())
            ->method('save')
            ->willReturn($club);

        $form->expects($this->any())
            ->method('isValid')
            ->willReturn(false);

        $form->expects($this->any())
            ->method('isSubmitted')
            ->willReturn(true);

        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue(json_encode([])));

        $formProcessor = new ClubFormProcessor($fileUploader, $formFactory,$clubDtoManager, $clubManager);
        $response=$formProcessor($request, 1);
        $this->assertEquals([null, $form], $response);
    }
}
