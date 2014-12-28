<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Tests\Component\Template;

use Brickoo\Component\Template\PhpTemplate;
use PHPUnit_Framework_TestCase;

/**
 * PhpTemplateTest
 *
 * Test suite for the PhpTemplate class.
 * @see Brickoo\Component\Template\PhpTemplate
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class PhpTemplateTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Template\PhpTemplate::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidTemplateFileThrowsException() {
        new PhpTemplate(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Template\PhpTemplate::__construct
     * @covers Brickoo\Component\Template\PhpTemplate::setTemplateFile
     * @covers Brickoo\Component\Template\PhpTemplate::addVariables
     * @covers Brickoo\Component\Template\PhpTemplate::render
     */
    public function testRender() {
        $templateFile = __DIR__."/assets/UnitTestPhpTemplate.php";
        $expectedValue = "<html><head></head><body>test content</body></html>";

        $template = new PhpTemplate("");
        $template->setTemplateFile($templateFile);
        $template->addVariables(array("content" => "test content"));
        $this->assertEquals($expectedValue, $template->render());
    }

    /**
     * @covers Brickoo\Component\Template\PhpTemplate::render
     * @covers Brickoo\Component\Template\Exception\RenderingException
     * @expectedException \Brickoo\Component\Template\Exception\RenderingException
     */
    public function testRenderThrowsRenderingException() {
        $template = new PhpTemplate(__DIR__."/assets/ExceptionThrowingPhpTemplate.php");
        $template->render();
    }

}
