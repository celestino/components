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

use Brickoo\Component\Template\Exception\RenderingException;
use Brickoo\Component\Common\Assert;

/**
 * PhpTemplate
 *
 * Implements a PHP based template.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class PhpTemplate implements Template {

    /** @var string */
    protected $templateFile;

    /** @var array */
    protected $templateVars;

    /**
     * Class constructor.
     * @param string $templateFile the php template to use
     * @param array $templateVars the template variables to make accessible
     * @throws \InvalidArgumentException if an argument is not valid
     */
    public function __construct($templateFile, array $templateVars = []) {
        Assert::isString($templateFile);
        $this->templateFile = $templateFile;
        $this->templateVars = $templateVars;
    }

    /**
     * Set the template filename.
     * @param string $templateFile
     * @return \Brickoo\Component\Template\PhpTemplate
     */
    public function setTemplateFile($templateFile) {
        Assert::isString($templateFile);
        $this->templateFile = $templateFile;
        return $this;
    }

    /**
     * Add template variables.
     * Duplicate keys will be overridden.
     * @param array $templateVars
     * @return \Brickoo\Component\Template\PhpTemplate
     */
    public function addVariables(array $templateVars) {
        $this->templateVars = array_merge($this->templateVars, $templateVars);
        return $this;
    }

    /** {@inheritDoc} */
    public function render() {
        try {
            ob_start();
            extract($this->templateVars, EXTR_SKIP);
            require ($this->templateFile);
            $output = ob_get_contents();
            ob_end_clean();
        }
        catch (\Exception $exception) {
            ob_end_clean();
            throw new RenderingException($exception);
        }

        return $output;
    }

}
