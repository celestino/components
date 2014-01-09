<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Brickoo\Tests\Template;

use Brickoo\Template\PhpTemplate,
    PHPUnit_Framework_TestCase;

/**
 * PhpTemplateTest
 *
 * Test suite for the PhpTemplate class.
 * @see Brickoo\Template\PhpTemplate
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class PhpTemplateTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Template\PhpTemplate::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidTemplateFileThrowsException() {
        $Template = new PhpTemplate(["wrongtype"]);
    }

    /**
     * @covers Brickoo\Template\PhpTemplate::__construct
     * @covers Brickoo\Template\PhpTemplate::render
     */
    public function testRender() {
        $templateFile = __DIR__ ."/assets/UnitTestTemplate.php";
        $templateDirectory = realpath(dirname($templateFile));

        $expectedDirectory = $templateDirectory . DIRECTORY_SEPARATOR;
        $expectedValue = "<html><head></head><body>test content</body></html>";

        $Template = new PhpTemplate($templateFile, array("content" => "test content"));
        $this->assertEquals($expectedValue, $Template->render());
    }

    /**
     * @covers Brickoo\Template\PhpTemplate::render
     * @covers Brickoo\Template\Exception\RenderingAbortedException
     * @expectedException Brickoo\Template\Exception\RenderingAbortedException
     */
    public function testRenderThrowsRenderingAbortedException() {
        $Template = new PhpTemplate(__DIR__ ."/assets/ExceptionThrowingTemplate.php");
        $Template->render();
    }

}