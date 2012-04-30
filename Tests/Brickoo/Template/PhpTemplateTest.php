<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    use Brickoo\Template\PhpTemplate;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * PhpTemplateTest
     *
     * Test suite for the Response class.
     * @see Brickoo\Http\Response
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class PhpTemplateTest extends \PHPUnit_Framework_TestCase {
        /**
         * Holds an instance of the PhpTemplate class.
         * @var \Brickoo\Template\PhpTemplate
         */
        protected $PhpTemplate;

        /**
         * Holds the template file used.
         * @var string
         */
        protected $templateFile;

        /**
         * Sets up PhpTemplate instance used.
         * @return void
         */
        protected function setUp() {
            $this->templateFile    = __DIR__ .'/assets/UnitTestTemplate.php';
            $this->PhpTemplate     = new PhpTemplate();
        }

        /**
         * Test if the PhpTemplate implements the Template.
         * @covers Brickoo\Template\PhpTemplate::__construct
         */
        public function testConstruct() {
            $Template = new PhpTemplate($this->templateFile);
            $this->assertInstanceOf('Brickoo\Template\Interfaces\Template', $Template);
        }

        /**
         * Test if the template files routines work as expected.
         * @covers Brickoo\Template\PhpTemplate::getTemplateFile
         * @covers Brickoo\Template\PhpTemplate::setTemplateFile
         * @covers Brickoo\Template\PhpTemplate::hasTemplateFile
         */
        public function testTemplateFileRoutines() {
            $this->assertSame($this->PhpTemplate, $this->PhpTemplate->setTemplateFile($this->templateFile));
            $this->assertAttributeEquals($this->templateFile, 'templateFile', $this->PhpTemplate);
            $this->assertEquals($this->templateFile, $this->PhpTemplate->getTemplateFile());
            $this->assertTrue($this->PhpTemplate->hasTemplateFile());
        }

        /**
         * Test if trying to set a non existing file throws an exception.
         * @covers Brickoo\Template\PhpTemplate::setTemplateFile
         * @covers Brickoo\Template\Exceptions\TemplateFileDoesNotExist::__construct
         * @expectedException Brickoo\Template\Exceptions\TemplateFileDoesNotExist
         */
        public function testSetTemplateFileException() {
            $this->PhpTemplate->setTemplateFile('FileDoesNotExists'. uniqid() .'.test');
        }

        /**
         * Test the template variables routines.
         * @covers Brickoo\Template\PhpTemplate::getTemplateVar
         * @covers Brickoo\Template\PhpTemplate::addTemplateVars
         * @covers Brickoo\Template\PhpTemplate::hasTemplateVar
         */
        public function testTemplateVarsRoutines() {
            $variables = array('content' => 'some content');
            $this->assertSame($this->PhpTemplate, $this->PhpTemplate->addTemplateVars($variables));
            $this->assertAttributeEquals($variables, 'templateVars', $this->PhpTemplate);
            $this->assertEquals($variables, $this->PhpTemplate->getTemplateVar());
            $this->assertTrue($this->PhpTemplate->hasTemplateVar());
        }

        /**
         * Test if the content of a single template variable can be retrieved.
         * @covers Brickoo\Template\PhpTemplate::getTemplateVar
         */
        public function testGetTemplateVar() {
            $variables = array('content' => 'some content');
            $this->assertSame($this->PhpTemplate, $this->PhpTemplate->addTemplateVars($variables));
            $this->assertEquals('some content', $this->PhpTemplate->getTemplateVar('content'));
        }

        /**
         * Test if trying to retrieve a non available template variable throws an excception.
         * @covers Brickoo\Template\PhpTemplate::getTemplateVar
         * @expectedException UnexpectedValueException
         */
        public function testGetTemplateVarValueException() {
            $this->PhpTemplate->getTemplateVar('content');
        }

        /**
         * Test if a template is recognized as available.
         * @covers Brickoo\Template\PhpTemplate::hasTemplateVar
         */
        public function testHasTemplateVar() {
            $variables = array('content' => 'some content');
            $this->assertSame($this->PhpTemplate, $this->PhpTemplate->addTemplateVars($variables));
            $this->assertTrue($this->PhpTemplate->hasTemplateVar('content'));
        }

        /**
         * Test if the template can be rendered.
         * @covers Brickoo\Template\PhpTemplate::render
         */
        public function testRender() {
            $expected = '<html><head></head><body>unit test content</body></html>';

            $this->PhpTemplate->setTemplateFile($this->templateFile)
                              ->addTemplateVars(array('content' => 'unit test content'));
            $this->assertEquals($expected, $this->PhpTemplate->render());
        }

        /**
         * Test if trying to render without an assigned template throws an exception.
         * @covers Brickoo\Template\PhpTemplate::render
         * @expectedException UnexpectedValueException
         */
        public function testRenderTemplateException() {
            $this->PhpTemplate->render();
        }

        /**
         * Test if a template variable can be retrived through the magic get method.
         * @covers Brickoo\Template\PhpTemplate::__get
         */
        public function testMagicGet() {
            $this->PhpTemplate->addTemplateVars(array('content' => 'unit test content'));
            $this->assertEquals('unit test content', $this->PhpTemplate->content);
        }

        /**
         * Test if template variables can be added through the magic set method.
         * @covers Brickoo\Template\PhpTemplate::__set
         */
        public function testMagicSet() {
            $this->assertEquals('some value', ($this->PhpTemplate->content = 'some value'));
            $this->assertAttributeEquals(array('content' => 'some value'), 'templateVars', $this->PhpTemplate);
        }

    }