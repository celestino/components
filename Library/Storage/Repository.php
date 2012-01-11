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

    namespace Brickoo\Library\Storage;

    use Brickoo\Library\Storage\Exceptions;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Repository
     *
     * Used to save different versions of commited values.
     * The data can be commited, retrieved, restored, imported, exported and locked.
     * If the repository is locked the methods comit, import, restore and remove do not work
     * until the lock has been removed to give full access for any user.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Repository implements \Countable
    {

        /**
         * Holds the repository used to store the versioned content.
         * @var array
         */
        protected $repository;

        /**
         * Sets the repository with the containing versions.
         * Take care by using this method as it will erase any holding content.
         * @param array $repository the repositoryList to use
         * @throws InvalidArgumentException if the passed repository is empty
         * @return object reference
         */
        protected function setRepository(array $repository)
        {
            TypeValidator::IsArray($repository);

            $this->repository = $repository;

            return $this;
        }

        /**
         * Returns the full stored repository.
         * @return array the current repository
         */
        public function getRepository()
        {
            return $this->repository;
        }

        /**
         * Returns the current stored revision versions.
         * @return array the stored revision versions
         */
        public function getRepositoryVersions()
        {
            return array_keys($this->repository);
        }

        /**
         * Checks if the given version is available in the repository.
         * @param integer $version the revision version to check
         * @throws InvalidArgumentException if the version passed is not a integer
         * @return boolean check result
         */
        public function isVersionAvailable($version)
        {
            TypeValidator::IsInteger($version);

            return array_key_exists($version, $this->repository);
        }

        /**
         * Holds the current revision used.
         * @var integer
         */
        protected $currentVersion;

        /**
         * Returns the current used version.
         * @return integer the current used version
         */
        public function getCurrentVersion()
        {
            return $this->currentVersion;
        }

        /**
         * Sets the current version to use.
         * If the version is available the content will be restored
         * and the version is set as current used version.
         * @param integer $version the version to use
         * @throws InvalidArgumentException if the version passed is not a integer
         * @return object reference
         */
        protected function setCurrentVersion($version)
        {
            TypeValidator::IsInteger($version);

            $this->currentVersion = $version;

            return $this;
        }

        /**
         * Sets the last version available as the one currently used.
         * @return object reference
         */
        public function useLastVersion()
        {
            if ($versions = $this->getRepositoryVersions())
            {
                $this->setCurrentVersion(array_pop($versions));
            }
            else
            {
                $this->setCurrentVersion(0);

                if(! isset($this->repository[0]))
                {
                    $this->repository[0] = 'initialized';
                }
            }

            return $this;
        }

        /**
         * Holds the locked status.
         * @var boolean
         */
        protected $locked;

        /**
         * Checks if the current status is locked.
         * @return boolean status
         */
        public function isLocked()
        {
            return $this->locked;
        }

        /**
         * Locks the repository.
         * While the repository is locked the methods
         * commit, restore, remove and import will be disabled.
         * @return void
         */
        public function lock()
        {
            $this->locked = true;
        }

        /**
         * Unlocks the repository.
         * Gives full access to all methods provided.
         * @return void
         */
        public function unlock()
        {
            $this->locked = false;
        }

        /**
         * Class constructor.
         * Intialize the class properties.
         * @return void
         */
        public function __construct()
        {
            $this->resetRepository();
        }

        /**
         * Resets the class properties.
         * @return object reference
         */
        public function resetRepository()
        {
            $this->locked            = false;
            $this->repository        = array(0 => 'initialized');
            $this->currentVersion    = 0;

            return $this;
        }

        /**
         * Returns the current amount of versions in the repository list.
         * @return integer the repository amount of versions
         */
        public function count()
        {
            return count($this->repository);
        }

        /**
         * Returns the revision by the given version.
         * @param integer $version the revision version to retrieve from
         * @throws VersionNotAvailableException if the verion passed is not available
         * @throws InvalidArgumentException if the version passed is not a integer
         * @return array the revision content
         */
        public function checkout($version = null)
        {
            if($version !== null)
            {
                TypeValidator::IsInteger($version);
            }

            if
            (
                ($version !== null)
                &&
                ! $this->isVersionAvailable($version)
            )
            {
                throw new Exceptions\VersionNotAvailableException($version);
            }

            return array
            (
                'version'  => ($version !== null ?: $this->getCurrentVersion()),
                'content'  => $this->export(($version !== null ?: $this->getCurrentVersion()))
            );
        }

        /**
         * Returns the content checked and prepared to commit.
         * Containing objects will be cloned.
         * @param array $content the content to check recursively
         * @return array the prepared content
         */
        protected function getRecursiveCommit(array $content)
        {
            if (empty($content))
            {
                return $content;
            }

            $result = array();

            foreach($content as $key => $value)
            {
                if (is_object($value))
                {
                    $result[$key] = clone $value;
                    continue;
                }

                if (is_array($value))
                {
                    $result[$key] = $this->getRecursiveCommit($value);
                    continue;
                }

                $result[$key] = $value;
            }

            return $result;
        }

        /**
         * Stores the given content under the current revision.
         * @param mixed $content the content to store under the next revision
         * @throws RepositoryLockedException if the Repository is locked
         * @return object reference
         */
        public function commit($content)
        {
            if ($this->isLocked())
            {
                throw new Exceptions\RepositoryLockedException();
            }

            $this->setCurrentVersion(++$this->currentVersion);

            if (is_object($content))
            {
                $content = clone $content;
            }

            if (is_array($content))
            {
                $content = $this->getRecursiveCommit($content);
            }

            $this->repository[$this->currentVersion] = $content;

            return $this;
        }

        /**
         * Restores an available version from the current repository
         * or the next version on stack if the version is not passed.
         * @param integer $version the version to restore
         * @throws RepositoryLockedException if the Repository is locked
         * @throws VersionNotAvailableException if the Repository version is passed and not available
         * @return object reference
         */
        public function restore($version)
        {
            TypeValidator::IsInteger($version);

            if ($this->isLocked())
            {
                throw new Exceptions\RepositoryLockedException();
            }

            if (! $this->isVersionAvailable($version))
            {
                throw new Exceptions\VersionNotAvailableException($version);
            }

            return $this->commit($this->export($version));
        }

        /**
         * Removes an version from the repository.
         * If the version is the current version used
         * it will be set back to the previous version.
         * If a previous version does not exist it will be set to zero.
         * @param integer $version the version to remove
         * @throws RepositoryLockedException if the Repository is locked
         * @throws VersionNotAvailableException if the Repository version is passed and not available
         * @return object reference
         */
        public function remove($version)
        {
            TypeValidator::IsInteger($version);

            if ($this->isLocked())
            {
                throw new Exceptions\RepositoryLockedException();
            }

            if (! $this->isVersionAvailable($version))
            {
                throw new Exceptions\VersionNotAvailableException($version);
            }

            unset ($this->repository[$version]);

            if($version == $this->currentVersion)
            {
                $this->useLastVersion();
            }

            return $this;
        }

        /**
         * Check if the repository versions are integers and the order is ascending.
         * @param array $repository the repository content to check
         * @return boolean check result
         */
        protected function checkImportVersions(array $repository)
        {
            $checkPassed = true;
            $lastVersion = 0;

            foreach($repository as $version => $content)
            {
                if
                (
                    (! is_int($version)) ||
                    (
                        is_int($version)
                        &&
                        ($version <= $lastVersion)
                    )
                )
                {
                    $checkPassed = false;
                    break;
                }

                $lastVersion = $version;
            }

            return $checkPassed;
        }

        /**
         * Imports the given repository.
         * The last repositoryList key will be used as currennt version.
         * @param array $repository the repository to import, can not be empty
         * @throws InvalidArgumentException if the repository passed is empty
         * @throws RepositoryLockedException if the Repository is locked
         * @throws InvalidRepositoryStructureException if the Repository has not incremented key order
         * @return object reference
         */
        public function import(array $repository)
        {
            TypeValidator::IsArray($repository);

            if ($this->isLocked())
            {
                throw new Exceptions\RepositoryLockedException();
            }

            if (! $checkPassed = $this->checkImportVersions($repository))
            {
                throw new Exceptions\InvalidRepositoryStructureException();
            }

            $this->resetRepository()->setRepository($repository)->useLastVersion();

            return $this;
        }

        /**
         * Returns the repository content from the given version
         * or if none is given the current from the current version.
         * @param integer $version the version to export
         * @return mixed the content of the given version
         */
        public function export($version = null)
        {
            if ($version !== null)
            {
                TypeValidator::IsInteger($version);
            }

            if
            (
                $version !== null
                &&
                ! $this->isVersionAvailable($version)
            )
            {
                throw new Exceptions\VersionNotAvailableException($version);
            }

            if ($version !== null)
            {
                return $this->repository[$version];
            }

            return $this->repository[$this->getCurrentVersion()];
        }

    }

?>