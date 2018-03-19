<?php declare(strict_types=1);

namespace Shopware\Api\Category\Event\Category;

use Shopware\Api\Category\Collection\CategoryDetailCollection;
use Shopware\Api\Category\Event\CategoryTranslation\CategoryTranslationBasicLoadedEvent;
use Shopware\Api\Media\Event\Media\MediaBasicLoadedEvent;
use Shopware\Api\Product\Event\ProductStream\ProductStreamBasicLoadedEvent;
use Shopware\Context\Struct\ShopContext;
use Shopware\Framework\Event\NestedEvent;
use Shopware\Framework\Event\NestedEventCollection;

class CategoryDetailLoadedEvent extends NestedEvent
{
    public const NAME = 'category.detail.loaded';

    /**
     * @var ShopContext
     */
    protected $context;

    /**
     * @var CategoryDetailCollection
     */
    protected $categories;

    public function __construct(CategoryDetailCollection $categories, ShopContext $context)
    {
        $this->context = $context;
        $this->categories = $categories;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): ShopContext
    {
        return $this->context;
    }

    public function getCategories(): CategoryDetailCollection
    {
        return $this->categories;
    }

    public function getEvents(): ?NestedEventCollection
    {
        $events = [];
        if ($this->categories->getParents()->count() > 0) {
            $events[] = new CategoryBasicLoadedEvent($this->categories->getParents(), $this->context);
        }
        if ($this->categories->getMedia()->count() > 0) {
            $events[] = new MediaBasicLoadedEvent($this->categories->getMedia(), $this->context);
        }
        if ($this->categories->getProductStreams()->count() > 0) {
            $events[] = new ProductStreamBasicLoadedEvent($this->categories->getProductStreams(), $this->context);
        }
        if ($this->categories->getChildren()->count() > 0) {
            $events[] = new CategoryBasicLoadedEvent($this->categories->getChildren(), $this->context);
        }
        if ($this->categories->getTranslations()->count() > 0) {
            $events[] = new CategoryTranslationBasicLoadedEvent($this->categories->getTranslations(), $this->context);
        }

        return new NestedEventCollection($events);
    }
}
