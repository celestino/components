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

    namespace Brickoo\Module;

    use Brickoo\Validator\TypeValidator;

    /**
     * Description
     *
     * Implements methods to describe a module.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Description implements Interfaces\DescriptionInterface
    {

        /**
         * Holds the available status keys.
         * @var array
         */
        protected $availableStatus;

        /**
         * Holds the vendor name of the module.
         * @var string
         */
        protected $vendor;

        /**
         * Returns the vendor name.
         * @throws \UnexpectedValueException if the vendor name is not set
         * @return string the vendor name
         */
        public function getVendor()
        {
            if ($this->vendor === null) {
                throw new \UnexpectedValueException('The vendor name is `null`.');
            }

            return $this->vendor;
        }

        /**
         * Sets the vendor name.
         * @param string $vendor the vendor name to set
         * @return \Brickoo\Module\Description
         */
        public function setVendor($vendor)
        {
            TypeValidator::IsString($vendor);

            $this->vendor = $vendor;

            return $this;
        }

        /**
         * Holds the website url of the module vendor.
         * @var string
         */
        protected $website;

        /**
         * Returns the website url.
         * @throws \UnexpectedValueException if the website url is not set
         * @return string the website url
         */
        public function getWebsite()
        {
            if ($this->website === null) {
                throw new \UnexpectedValueException('The website url is `null`.');
            }

            return $this->website;
        }

        /**
         * Sets the website url.
         * @param string $website the website url to set
         * @return \Brickoo\Module\Description
         */
        public function setWebsite($website)
        {
            TypeValidator::MatchesRegex('~^[^:/?#]+://[^/?#]+(\?[^#]*)?(#.*)?~', $website);

            $this->website = $website;

            return $this;
        }

        /**
         * Holds the contact adress of the module vendor.
         * @var string
         */
        protected $contact;

        /**
         * Returns the contact adress.
         * @throws \UnexpectedValueException if the contact adress is not set
         * @return string the contact adress
         */
        public function getContact()
        {
            if ($this->contact === null) {
                throw new \UnexpectedValueException('The contact adress is `null`.');
            }

            return $this->contact;
        }

        /**
         * Sets the contact adress.
         * @param string $contact the contact adress to set
         * @return \Brickoo\Module\Description
         */
        public function setContact($contact)
        {
            TypeValidator::IsString($contact);

            $this->contact = $contact;

            return $this;
        }

        /**
         * Holds the status of the module.
         * @var string
         */
        protected $status;

        /**
         * Returns the module status.
         * @return string the module status
         */
        public function getStatus()
        {
            return $this->status;
        }

        /**
         * Sets the status.
         * @param string $status the status to set
         * @throws \InvalidArgumentException if the status in unknowed
         * @return \Brickoo\Module\Description
         */
        public function setStatus($status)
        {
            TypeValidator::IsString($status);

            if (! in_array(strtolower($status), $this->availableStatus)) {
                throw new \InvalidArgumentException(sprintf('The status `%s` unknowed.', $status));
            }

            $this->status = $status;

            return $this;
        }

        /**
         * Holds the version of the module.
         * @var string
         */
        protected $version;

        /**
         * Returns the module version.
         * @return string the module version
         */
        public function getVersion()
        {
            return $this->version;
        }

        /**
         * Sets the version.
         * @param string $version the version to set
         * @return \Brickoo\Module\Description
         */
        public function setVersion($version)
        {
            TypeValidator::IsString($version);

            $this->version = $version;

            return $this;
        }

        /**
         * Holds the description of the module.
         * @var string
         */
        protected $description;

        /**
         * Returns the module description.
         * @return string the module description
         */
        public function getDescription()
        {
            return $this->description;
        }

        /**
         * Sets the description.
         * @param string $description the description to set
         * @return \Brickoo\Module\Description
         */
        public function setDescription($description)
        {
            TypeValidator::IsString($description);

            $this->description = $description;

            return $this;
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct()
        {
            $this->availableStatus = array('stable', 'dev', 'beta', 'alpha', 'rc');
        }

        /**
         * Returns the module description as string.
         * @return string the module description
         */
        public function toString()
        {
            $result = '';

            $result .= "Vendor: " . $this->getVendor() . "\n";
            $result .= "Website: " . $this->getWebsite() . "\n";
            $result .= "Contact: " . $this->getContact() . "\n";

            if ($stautus = $this->getStatus()) {
                $result .= "Status: " . $stautus . "\n";
            }

            if ($version = $this->getVersion()) {
                $result .= "Version: " . $version . "\n";
            }

            if($description = $this->getDescription()) {
                $result .= "Description: " . $description;
            }

            return $result;
        }

        /**
         * Returns the module description as string.
         * @return string the module description
         */
        public function __toString()
        {
            return $this->toString();
        }

    }