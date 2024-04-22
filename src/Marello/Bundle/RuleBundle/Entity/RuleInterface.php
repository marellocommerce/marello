<?php

namespace Marello\Bundle\RuleBundle\Entity;

interface RuleInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self;

    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled(bool $enabled): self;

    /**
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * @param int $sortOrder
     *
     * @return $this
     */
    public function setSortOrder(int $sortOrder): self;

    /**
     * @return bool
     */
    public function isStopProcessing(): bool;

    /**
     * @param bool $stopProcessing
     *
     * @return $this
     */
    public function setStopProcessing(bool $stopProcessing): self;

    /**
     * @return string
     */
    public function getExpression(): string;

    /**
     * @param string $expression
     *
     * @return $this
     */
    public function setExpression(string $expression): self;

    /**
     * @return bool
     */
    public function isSystem(): bool;

    /**
     * @param bool $system
     *
     * @return $this
     */
    public function setSystem(bool $system): self;
}
