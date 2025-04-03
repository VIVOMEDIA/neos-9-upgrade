<?php

namespace VIVOMEDIA\Neos9\Upgrade\Transformations;

use Doctrine\ORM\EntityManagerInterface;
use Neos\ContentRepository\Domain\Model\NodeData;
use Neos\ContentRepository\Domain\Repository\NodeDataRepository;
use Neos\ContentRepository\Migration\Transformations\AbstractTransformation;
use Neos\Flow\Utility\Algorithms;
use Neos\Flow\Annotations as Flow;

class DetachVariantsWithDifferentNodeTypesTransformation extends AbstractTransformation
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
        $nodeTypes = [];
        foreach ($variants as $variant) {
            if (isset($nodeTypes[$variant->getNodeType()->getName()])) {
                $nodeTypes[$variant->getNodeType()->getName()]++;
            } else {
                $nodeTypes[$variant->getNodeType()->getName()] = 1;
            }
        }
        if (count($nodeTypes) == 1) {
            return false;
        }
        if ($nodeTypes[$node->getNodeType()->getName()] == 1 && count($variants) > 1) {
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
