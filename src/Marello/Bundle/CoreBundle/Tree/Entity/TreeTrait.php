<?php

namespace Marello\Bundle\CoreBundle\Tree\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait TreeTrait
{
    /**
     * @var integer
     *
     * @Gedmo\TreeLeft
     */
    #[ORM\Column(name: 'tree_left', type: Types::INTEGER)]
    protected $left;

    /**
     * @var integer
     *
     * @Gedmo\TreeLevel
     */
    #[ORM\Column(name: 'tree_level', type: Types::INTEGER)]
    protected $level;

    /**
     * @var integer
     *
     * @Gedmo\TreeRight
     */
    #[ORM\Column(name: 'tree_right', type: Types::INTEGER)]
    protected $right;

    /**
     * @var integer
     *
     * @Gedmo\TreeRoot
     */
    #[ORM\Column(name: 'tree_root', type: Types::INTEGER, nullable: true)]
    protected $root;

    /**
     * @return int
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param int $left
     * @return $this
     */
    public function setLeft($left)
    {
        $this->left = $left;

        return $this;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return int
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param int $right
     * @return $this
     */
    public function setRight($right)
    {
        $this->right = $right;

        return $this;
    }

    /**
     * @return int
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param int $root
     * @return $this
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }
}
