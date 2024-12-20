<?php

namespace VIVOMEDIA\Neos9\Upgrade\Transformations;

use Doctrine\ORM\EntityManagerInterface;
use Neos\ContentRepository\Domain\Model\NodeData;
use Neos\ContentRepository\Domain\Repository\NodeDataRepository;
use Neos\ContentRepository\Migration\Transformations\AbstractTransformation;
use Neos\Flow\Utility\Algorithms;
use Neos\Flow\Annotations as Flow;

class DetachVariantsWithDifferentParentTransformation extends AbstractTransformation
{
    /**
     * @Flow\Inject
     * @var NodeDataRepository
     */
    protected $nodeDataRepository;
    /**
     * @Flow\Inject
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param NodeData $node
     * @return boolean
     */
    public function isTransformable(NodeData $node)
    {
        $variants = $this->nodeDataRepository->findByNodeIdentifier($node->getIdentifier())->toArray();
        if (empty($variants)) {
            return false;
        }
        $pathlist = [];
        foreach ($variants as $variant) {
            if (isset($pathlist[$variant->getPath()])) {
                $pathlist[$variant->getPath()]++;
            } else {
                $pathlist[$variant->getPath()] = 1;
            }
        }
        if (count($pathlist) == 1) {
            return false;
        }
        if ($pathlist[$node->getPath()] == 1) {
            return true;
        }
        return false;
    }

    /**
     * Generate a new identifier to detach the variant from the other variants
     *
     * @param NodeData $node
     * @return void
     */
    public function execute(NodeData $node)
    {
        $identifier = Algorithms::generateUUID();
        $node->setIdentifier($identifier);
    }
}
