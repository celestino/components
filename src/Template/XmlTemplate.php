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

namespace Brickoo\Component\Template;

use Brickoo\Component\Template\Exception;
use Brickoo\Component\Template\Exception\RenderingException;
use Brickoo\Component\Template\Exception\UnableToLoadFileException;
use Brickoo\Component\Template\Exception\XmlTransformationException;
use Brickoo\Component\Validation\Argument;
use DOMDocument;
use LibXMLError;
use XSLTProcessor;

/**
 * XmlTemplate
 *
 * Implements a xml template with the
 * usage of the xslt processor.
 */
class XmlTemplate implements Template {

    /** @var string */
    private $xsltFilename;

    /** @var \DOMDocument */
    private $xmlDocument;

    /**
     * Class constructor.
     * @param \DOMDocument $xmlDocument
     * @param null|string $xsltFilename
     * @throws \InvalidArgumentException
     */
    public function __construct(DOMDocument $xmlDocument, $xsltFilename = null) {
        if ($xsltFilename !== null) {
            Argument::isString($xsltFilename);
        }
        $this->xsltFilename = $xsltFilename;
        $this->xmlDocument = $xmlDocument;
    }

    /**
     * Set XSLT filename.
     * @param string $xsltFilename
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Template\XmlTemplate
     */
    public function setXsltFilename($xsltFilename) {
        Argument::isString($xsltFilename);
        $this->xsltFilename = $xsltFilename;
        return $this;
    }

    /**
     * Set the xml template document.
     * @param \DOMDocument $xmlDocument
     * @return \Brickoo\Component\Template\XmlTemplate
     */
    public function setXmlDocument(DOMDocument $xmlDocument) {
        $this->xmlDocument = $xmlDocument;
        return $this;
    }

    /** {@inheritdoc} */
    public function render() {
        if ($this->xsltFilename === null) {
            return $this->xmlDocument->saveXML();
        }

        $lastErrorsState = libxml_use_internal_errors(true);
        try {
            $processor = $this->createXsltProcessor();
            if (($output = @$processor->transformToXml($this->xmlDocument)) === false) {
                throw new XmlTransformationException($this->getLibXmlErrorMessage());
            }
            libxml_use_internal_errors($lastErrorsState);
        }
        catch (\Exception $exception) {
            libxml_use_internal_errors($lastErrorsState);
            throw new RenderingException($exception);
        }
        return $output;
    }

    /**
     * Create the XSLT processor.
     * @throws \Brickoo\Component\Template\Exception\XmlTransformationException
     * @return \XSLTProcessor
     */
    private function createXsltProcessor() {
        $xsltProcessor = new XSLTProcessor();
        $xsltProcessor->registerPHPFunctions();
        $xsltProcessor->importStylesheet($this->createStylesheet());
        return $xsltProcessor;
    }

    /**
     * Create the xslt processor stylesheet equal the xml document version and encoding.
     * @throws \Brickoo\Component\Template\Exception\UnableToLoadFileException
     * @return \DOMDocument
     */
    private function createStylesheet() {
        $stylesheet = new DOMDocument(
            $this->xmlDocument->xmlVersion, $this->xmlDocument->xmlEncoding
        );

        if (empty($this->xsltFilename)
            || (! file_exists($this->xsltFilename))
            || (! $stylesheet->load($this->xsltFilename))) {
                throw new UnableToLoadFileException($this->xsltFilename);
        }
        return $stylesheet;
    }

    /**
     * Return the concatenated error messages.
     * @return string the concatenated libXml messages
     */
    private function getLibXmlErrorMessage() {
        $errorMessage = "";
        foreach (libxml_get_errors() as $index => $error) {
            $errorMessage .= $this->getErrorMessage($index, $error);
        }
        libxml_clear_errors();
        return $errorMessage;
    }

    /**
     * Return a formatted error message.
     * @param integer $index
     * @param LibXMLError $error
     * @return string the formatted error message
     */
    private function getErrorMessage($index, LibXMLError $error) {
        return sprintf("%s%s", ($index > 0 ? "\n\t-> " : ""), rtrim($error->message, "\r\n"));
    }

}
