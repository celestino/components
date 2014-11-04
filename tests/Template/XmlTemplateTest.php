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

namespace Brickoo\Tests\Component\Template;

use Brickoo\Component\Template\XmlTemplate;
use DOMDocument;
use PHPUnit_Framework_TestCase;

/**
 * XmlTemplateTest
 *
 * Test suite for the XmlTemplate class.
 * @see Brickoo\Component\Template\XmlTemplate
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class XmlTemplateTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        if ((! class_exists("DOMDocument")) || (! class_exists("XSLTProcessor"))) {
            $this->markTestSkipped("Missing DOMDocument|XSLTProcessor dependencies.");
        }

        if (defined("HHVM_VERSION")) {
            $this->markTestSkipped(
                "Problems caused by HHVM v3.2.0 at importing stylesheets."
            );
        }
    }

    /**
     * @covers Brickoo\Component\Template\XmlTemplate::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidXsltFilenameThrowsException() {
        new XmlTemplate(new DOMDocument(), ["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Template\XmlTemplate::__construct
     * @covers Brickoo\Component\Template\XmlTemplate::render
     */
    public function testRenderWithoutXslt() {
        $xmlDocument = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>".PHP_EOL."<content>test content</content>".PHP_EOL;
        $document = new DOMDocument();
        $document->loadXML($xmlDocument);
        $template = new XmlTemplate($document);
        $this->assertEquals($xmlDocument, $template->render());
    }

    /**
     * @covers Brickoo\Component\Template\XmlTemplate::__construct
     * @covers Brickoo\Component\Template\XmlTemplate::setXmlDocument
     * @covers Brickoo\Component\Template\XmlTemplate::setXsltFilename
     * @covers Brickoo\Component\Template\XmlTemplate::createXsltProcessor
     * @covers Brickoo\Component\Template\XmlTemplate::createStylesheet
     * @covers Brickoo\Component\Template\XmlTemplate::render
     */
    public function testRender() {
        $xsltFilename = __DIR__."/assets/XsltTemplate.xsl";
        $xmlDocument = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><content>test content</content>";
        $expectedValue = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>".PHP_EOL."<root>test content</root>".PHP_EOL;

        $document = new DOMDocument("1.0", "UTF-8");
        $document->loadXML($xmlDocument);

        $template = new XmlTemplate(new DOMDocument(), $xsltFilename);
        $template->setXmlDocument($document)
                 ->setXsltFilename($xsltFilename);

        $this->assertEquals($expectedValue, $template->render());
    }

    /**
     * @covers Brickoo\Component\Template\XmlTemplate::render
     * @covers Brickoo\Component\Template\XmlTemplate::createXsltProcessor
     * @covers Brickoo\Component\Template\XmlTemplate::createStylesheet
     * @covers Brickoo\Component\Template\Exception\UnableToLoadFileException
     * @covers Brickoo\Component\Template\Exception\RenderingException
     * @expectedException \Brickoo\Component\Template\Exception\RenderingException
     */
    public function testRenderThrowsUnableToLoadFileException() {
        $template = new XmlTemplate(new DOMDocument(), "doesNotExist.xsl");
        $template->render();
    }

    /**
     * @covers Brickoo\Component\Template\XmlTemplate::render
     * @covers Brickoo\Component\Template\XmlTemplate::getLibXmlErrorMessage
     * @covers Brickoo\Component\Template\XmlTemplate::getErrorMessage
     * @covers Brickoo\Component\Template\Exception\XmlTransformationException
     * @covers Brickoo\Component\Template\Exception\RenderingException
     * @expectedException \Brickoo\Component\Template\Exception\RenderingException
     */
    public function testXsltTemplateThrowsRenderException() {
        $xsltFilename = __DIR__."/assets/ExceptionThrowingTemplate.xsl";
        $xmlDocument = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><content>test content</content>";

        $document = new DOMDocument();
        $document->loadXML($xmlDocument);

        $template = new XmlTemplate($document, $xsltFilename);
        $template->render();
    }

}
