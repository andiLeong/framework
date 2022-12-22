<?php

namespace Andileong\Framework\Core\Database\Model;

class GeneratePaginatorLinks
{

    protected $totalPage;
    protected $currentPage;
    private array $groups;
    private $currentGroup;
    private $links = [];

    public function __construct(protected Paginator $paginator)
    {
        $this->totalPage = $this->paginator->totalPage();
        $this->currentPage = $this->paginator->currentPage;
    }

    public function __invoke($groupBy = 3): array
    {
        $this->createGroup($groupBy);
        $this->setCurrentGroup();

        $this
            ->setLinksFromCurrentGroup()
            ->addPreviousGroupLink()
            ->addFirstPage()
            ->addNextGroupLink()
            ->addLastPage();

        return $this->links;
    }

    /**
     * add the last page url to the links collection if we are not in the last page
     * @return $this
     */
    protected function addLastPage()
    {
        if ($this->paginator->hasMorePage()) {
            $this->links[] = $this->link($this->totalPage);
        }
        return $this;
    }

    /**
     * add the next group first item to links collection if it has next group
     * @return $this
     */
    protected function addNextGroupLink()
    {
        if ($nextGroup = $this->getNextGroup()) {
            $this->links[] = $this->link($nextGroup[0], '...');
        }

        return $this;
    }

    /**
     * add the first page url to links collection when current group is not the first group
     * @return $this
     */
    protected function addFirstPage()
    {
        if (!$this->inFirstGroup()) {
            array_unshift($this->links, $this->link(1));
        }

        return $this;
    }

    /**
     * set links for the current group
     * @return $this
     */
    protected function setLinksFromCurrentGroup()
    {
        $this->links = array_map(fn($page) => $this->link($page), array_values($this->currentGroup)[0]
        );
        return $this;
    }

    /**
     * add the previous group last item to links collection if it has previous group
     * @return $this
     */
    protected function addPreviousGroupLink()
    {
        if ($lastItem = $this->getPreviousGroupLastItem()) {
            array_unshift($this->links, $this->link($lastItem, '...'));
        }
        return $this;
    }

    /**
     * determined if the current group is the first group inside group array
     * @return bool
     */
    protected function inFirstGroup()
    {
        return $this->currentGroupIndex() === 0;
    }

    /**
     * get the current group index
     * @return int|string|null
     */
    protected function currentGroupIndex()
    {
        return array_key_first($this->currentGroup);
    }

    /**
     * try to retrieve the previous group
     * @return mixed|void
     */
    protected function getPreviousGroup()
    {
        $previousGroupIndex = $this->currentGroupIndex() - 1;
        if (array_key_exists($previousGroupIndex, $this->groups)) {
            return $this->groups[$previousGroupIndex];
        }
    }

    /**
     * get the last item from the previous group if it has previous group
     * @return mixed|void
     */
    protected function getPreviousGroupLastItem()
    {
        if ($previousGroup = $this->getPreviousGroup()) {
            return $previousGroup[array_key_last($previousGroup)];
        }
    }

    /**
     * check if next group is available
     * @return bool
     */
    protected function hasNextGroup()
    {
        return array_key_exists($this->currentGroupIndex() + 1, $this->groups);
    }

    /**
     * try to get the next group
     * @return mixed|void
     */
    protected function getNextGroup()
    {
        if ($this->hasNextGroup()) {
            return $this->groups[$this->currentGroupIndex() + 1];
        }
    }

    /**
     * set the current group based on the current page
     * the current group array key is the key from group array
     * the key without mutated
     */
    protected function setCurrentGroup(): void
    {
        $this->currentGroup = array_filter(
            $this->groups,
            fn($group) => in_array($this->currentPage, $group)
        );
    }


    /**
     * indicate each link item should contain
     * @param $page
     * @param $label
     * @return array
     */
    protected function link($page, $label = null)
    {
        return [
            'url' => $this->paginator->url($page),
            'label' => $label ?? $page,
            'page' => $page,
            'active' => $this->currentPage == $page,
        ];
    }

    /**
     * create group
     * @param $by
     */
    protected function createGroup($by)
    {
        $this->groups = array_chunk(range(1, $this->totalPage), $by);
    }


}