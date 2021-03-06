<?php

namespace LinkedSwissbibBundle\ApiPlatform\Serializer;

use ApiPlatform\Core\Api\ResourceClassResolverInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Hydra\Serializer\CollectionNormalizer as ApiPlatformCollectionNormalizer;
use LinkedSwissbibBundle\Serializer\Encoder\HtmlEncoder;
use Symfony\Component\Serializer\Normalizer\scalar;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Decorated CollectionNormalizer in order to be able to use json-ld as input format for all encoders
 *
 * @author   Melanie Stucki <melanie.stucki@students.fhnw.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class CollectionNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    /**
     * @var ApiPlatformCollectionNormalizer
     */
    protected $collectionNormalizer;

    /**
     * @var ResourceClassResolverInterface
     */
    protected $resourceClassResolver;

    /**
     * @param ApiPlatformCollectionNormalizer $collectionNormalizer
     * @param ResourceClassResolverInterface $resourceClassResolver
     */
    public function __construct(ApiPlatformCollectionNormalizer $collectionNormalizer, ResourceClassResolverInterface $resourceClassResolver)
    {
        $this->collectionNormalizer = $collectionNormalizer;
        $this->resourceClassResolver = $resourceClassResolver;
    }

    public function normalize($object, $format = null, array $context = array())
    {
        if ($format !== ApiPlatformCollectionNormalizer::FORMAT && $format !== HtmlEncoder::FORMAT) {
            //embed context to avoid an additional request on encoding to e.g. rdf
            $context['jsonld_embed_context'] = true;
        }

        return $this->collectionNormalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!(is_array($data) || ($data instanceof \Traversable))) {
            return false;
        }

        return true;
    }

    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->collectionNormalizer->setNormalizer($normalizer);
    }
}
