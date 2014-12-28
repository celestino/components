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

use Brickoo\Component\Template\XmlTemplate;
use Brickoo\Component\Template\Exception\RenderingException;
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

        /*
        if (defined("HHVM_VERSION")) {
            $this->markTestSkipped(
                "Problems caused by HHVM v3.2.0 at importing stylesheets."
            );
        }
        */
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
        if (defined("HHVM_VERSION")) {
            // HHVM will throw a fatal error if the stylesheet is broken, which can not be catch
            throw new RenderingException(new \Exception("HHVM fatal error replacement"));
        }
        $xsltFilename = __DIR__ . "/assets/ExceptionThrowingTemplate.xsl";
        $xmlDocument = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><content>test content</content>";

        $document = new DOMDocument();
        $document->loadXML($xmlDocument);

        $template = new XmlTemplate($document, $xsltFilename);
        $template->render();
    }

}
