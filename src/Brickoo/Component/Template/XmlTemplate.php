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

namespace Brickoo\Component\Template;

use Brickoo\Component\Template\Exception,
    Brickoo\Component\Template\Exception\RenderingException,
    Brickoo\Component\Template\Exception\UnableToLoadFileException,
    Brickoo\Component\Template\Exception\XmlTransformationException,
    Brickoo\Component\Validation\Argument,
    DOMDocument,
    LibXMLError,
    XSLTProcessor;

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
     * @param string $xsltFilename
     * @param \DOMDocument $xmlDocument
     * @throws \InvalidArgumentException
     */
    public function __construct(DOMDocument $xmlDocument, $xsltFilename = null) {
        if ($xsltFilename !== null) {
            Argument::IsString($xsltFilename);
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
        Argument::IsString($xsltFilename);
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
        }
        catch (Exception $exception) {
            libxml_use_internal_errors($lastErrorsState);
            throw new RenderingException($exception);
        }

        libxml_use_internal_errors($lastErrorsState);
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

        if (! $stylesheet->load($this->xsltFilename)) {
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
