<?php
/**
 * php-pagination generates a pagination menu in the form of html markup
 * 
 * https://github.com/chairowner/php-pagination
 * 
 * Copyright © 2023 Danil Sagajdachnyj (https://github.com/chairowner/)
 * @param string $pattern URL pattern, where "#" is the page number
 * @param int $currentPage current page number
 * @param int $limitItems limitation of displayed elements
 * @param int $allItems number of items
 * @param int $maxPage number of pages
 * @param int $showPages maximum number of pages that can be displayed
 * @param string|null $next_title text of the "next" button (the "null" value removes the button)
 * @param string|null $prev_title text of the "previous" button (the "null" value removes the button)
 * @author chairowner (Danil Sagajdachnyj)
 * @copyright Copyright © 2023 Danil Sagajdachnyj (https://github.com/chairowner)
 */
class Pagination {
    public const
        PREVIOUS_BUTTON = 0, NEXT_BUTTON = 1,
        PATTERN_SIGN = '#';
    public string $pattern;
    private array $main_class = ["pagination"];
    private string|null $main_style = null;
    private array $items_class = ["item"];
    private array $itemsContent_class = ["item-content"];
    private string|null $items_noPrevClass = "item-no-prev";
    private string|null $items_noNextClass = "item-no-next";
    private array $activeItems_class = ["active"];
    private string|null $next_id = null;
    private array $next_class = ["next"];
    private string|null $next_title = "Next";
    private string|null $prev_id = null;
    private array $prev_class = ["prev"];
    private string|null $prev_title = "Previous";
    public int $currentPage;
    public int $limitItems = 5;
    public int $allItems;
    private int $maxPage;
    private int $showBeforeCurrent = 2;
    private int $showAfterCurrent = 2;
    private bool $showFirstPageInUrl = true;

    /**
     * 
     * @param string $pattern URL pattern, where "#" is the page number
     * @param int $currentPage current page number
     * @param int $limitItems limitation of displayed elements
     * @param int $allItems number of items
     * @param int $maxPage number of pages
     * @param int $showPages maximum number of pages that can be displayed
     * @param string|null $next_title text of the "next" button (the "null" value removes the button)
     * @param string|null $prev_title text of the "previous" button (the "null" value removes the button)
     */
    public function __construct(int $currentPage, int $allItems, string $pattern, int $limitItems = 30, $showFirstPageInUrl = true) {
        $this->currentPage = $currentPage;
        $this->showFirstPageInUrl = $showFirstPageInUrl;

        if (isset($pattern)) {
            $this->pattern = $pattern;
        }

        if (isset($limitItems)) {
            $this->limitItems = $limitItems < 0 ? 0 : $limitItems;
        }

        if ($allItems <= 0) {
            $this->allItems = $this->maxPage = 0;
        } else {
            $this->allItems = $allItems;
            $this->maxPage = ceil($allItems / $limitItems);
        }
    }

    /**
     * Set main classes
     * @param array $classes classes
     */
    function SetMainClass(array $classes) {
        $this->main_class = $classes;
    }

    /**
     * Set main styles
     * @param string|null $style styles
     */
    function SetMainStyle(string|null $style) {
        $this->main_style = $style;
    }

    /**
     * Set button title (null removes button)
     * @param string|null $title button title
     */
    function SetButtonTitle(int $buttonNumber, string|null $title) {
        if ($buttonNumber === self::PREVIOUS_BUTTON) $this->prev_title = $title;
        if ($buttonNumber === self::NEXT_BUTTON) $this->next_title = $title;
    }

    /**
     * Set button ID
     * @param string|null $id ID
     */
    function SetButtonId(int $buttonNumber, string|null $id) {
        if (isset($id)) $id = str_replace(' ', '', $id); # remove spaces
        if ($buttonNumber === self::PREVIOUS_BUTTON) $this->prev_id = $id;
        else if ($buttonNumber === self::NEXT_BUTTON) $this->next_id = $id;
    }

    /**
     * Set button classes
     * @param array $classes classes
     */
    function SetButtonClass(int $buttonNumber, array $classes) {
        if ($buttonNumber === self::PREVIOUS_BUTTON) $this->prev_class = $classes;
        else if ($buttonNumber === self::NEXT_BUTTON) $this->next_class = $classes;
    }

    /**
     * Set items classes
     * @param array $classes classes
     * @param bool $isActive write classes for active item
     */
    function SetItemsClass(array $classes, bool $isActive = false) {
        if ($isActive) $this->activeItems_class = $classes;
        else $this->items_class = $classes;
    }

    /**
     * Set items classes
     * @param string|null $class class
     */
    function SetNoPrevClass(string|null $class) {
        $this->items_noPrevClass = $class;
    }

    /**
     * Set items classes
     * @param string|null $class class
     */
    function SetNoNextClass(string|null $class) {
        $this->items_noNextClass = $class;
    }

    /**
     * Set items content classes
     * @param array $classes classes
     */
    function SetItemsContentClass(array $classes) {
        $this->itemsContent_class = $classes;
    }

    /**
     * Set the number of pages to the current one (min - 1)
     * @param array $number
     */
    function SetBeforeCurrent(int $number) {
        $this->showBeforeCurrent = $number < 1 ? 1 : $number;
    }

    /**
     * Set the number of pages after the current one (min - 1)
     * @param array $number
     */
    function SetAfterCurrent(int $number) {
        $this->showAfterCurrent = $number < 1 ? 1 : $number;
    }

    /**
     * @return string|null
     */
    public function Render() {
        if ($this->currentPage > $this->maxPage || $this->maxPage <= 1) return null;
        
        $response = null;
        $items = [];

        # next page
        $nextPage = $this->currentPage + 1;
        if ($nextPage > $this->maxPage) $nextPage = $this->maxPage;
        
        # previous page
        $prevPage = $this->currentPage - 1;
        if ($prevPage < 1) $prevPage = 1;
        
        # show first page
        if (($this->currentPage - $this->showBeforeCurrent) >= $this->showBeforeCurrent) {
            $items[] =
            '<li class="gost '.implode(' ', $this->items_class).'">'.
                '<a class="'.implode(' ', $this->itemsContent_class).'" href="'.(str_replace('/'.self::PATTERN_SIGN, '', $this->pattern)).'">1</a>'.
            '</li>';
        }
        
        # first pages
        $start = $this->currentPage - $this->showBeforeCurrent; # 7 - 2 = 5 (выводим с 5ой страницы)
        if ($start < 1) $start = 1; # /\ 1 - 2 = -1 => 1
        for ($pageNumber = $start; $pageNumber < $this->currentPage; $pageNumber++) {
            $item =
            '<li class="'.implode(' ', $this->items_class).'">';
            if (!$this->showFirstPageInUrl && $start <= 1 && $pageNumber <= 1) {
                $item .=
                '<a class="'.implode(' ', $this->itemsContent_class).'" href="'.(str_replace('/'.self::PATTERN_SIGN, '', $this->pattern)).'">';
            } else {
                $item .=
                '<a class="'.implode(' ', $this->itemsContent_class).'" href="'.(str_replace(self::PATTERN_SIGN, $pageNumber, $this->pattern)).'">';
            }
            $item .= $pageNumber.
                '</a>'.
            '</li>';
            $items[] = $item;
            unset($item);
        }

        # current page
        if ($this->currentPage <= 1) /* для закругления первого элемента */ {
            $item = '<li class="'.$this->items_noPrevClass.' '.implode(' ', $this->activeItems_class).' '.implode(' ', $this->items_class).'">';
        } elseif ($this->currentPage >= $this->maxPage) /* для закругления последнего элемента */ {
            $item = '<li class="'.$this->items_noNextClass.' '.implode(' ', $this->activeItems_class).' '.implode(' ', $this->items_class).'">';
        } else /* остальные элементы */ {
            $item = '<li class="'.implode(' ', $this->activeItems_class).' '.implode(' ', $this->items_class).'">';
        }
        $item .= '<span class="'.implode(' ', $this->itemsContent_class).'">'.$this->currentPage.'</span>'.
        '</li>';
        $items[] = $item;
        unset($item);
        
        # end pages
        $end = $this->currentPage + $this->showAfterCurrent; # 7 + 2 = 9 (выводим до 9ой страницы)
        if ($end > $this->maxPage) $end = $this->maxPage; # /\ 8 + 2 = 10 (макс. 9) => 9
        for ($pageNumber = ($this->currentPage + 1); $pageNumber <= $end; $pageNumber++) {
            $items[] =
            '<li class="'.implode(' ', $this->items_class).'">'.
                '<a class="'.implode(' ', $this->itemsContent_class).'" href="'.(str_replace(self::PATTERN_SIGN, $pageNumber, $this->pattern)).'">'.
                    $pageNumber.
                '</a>'.
            '</li>';
        }
        
        # show last page
        if (($this->currentPage + $this->showAfterCurrent) < $this->maxPage) {
            $items[] =
            '<li class="gost '.implode(' ', $this->items_class).'">'.
                '<a class="'.implode(' ', $this->itemsContent_class).'" href="'.(str_replace(self::PATTERN_SIGN, $this->maxPage, $this->pattern)).'">'.
                    $this->maxPage.
                '</a>'.
            '</li>';
        }

        $response = isset($this->main_style) ?
            '<nav style="'.$this->main_style.'" class="'.implode(' ', $this->main_class).'">':
            '<nav class="'.implode(' ', $this->main_class).'">';

            if (isset($this->prev_title)) {
                $prevPage = $this->currentPage - 1;
                if ($prevPage > 0) {
                    if (!$this->showFirstPageInUrl && $prevPage === 1) {
                        $response .=
                        '<a href="'.(str_replace('/'.self::PATTERN_SIGN, '', $this->pattern)).'" id="'.$this->prev_id.'"class="pagination-button '.implode(' ', $this->prev_class).'">'.
                            $this->prev_title.
                        '</a>';
                    } else {
                        $response .=
                        '<a href="'.(str_replace(self::PATTERN_SIGN, $prevPage, $this->pattern)).'" id="'.$this->prev_id.'"class="pagination-button '.implode(' ', $this->prev_class).'">'.
                            $this->prev_title.
                        '</a>';
                    }
                }
            }

            $response .= '<ul class="pagination-pages">'.implode('', $items).'</ul>';

            if (isset($this->next_title)) {
                $nextPage = $this->currentPage + 1;
                if ($nextPage < ($this->maxPage + 1)) {
                    $response .= '<a href="'.(str_replace(self::PATTERN_SIGN, $nextPage, $this->pattern)).'" id="'.$this->next_id.'"class="pagination-button '.implode(' ', $this->next_class).'">'.$this->next_title.'</a>';
                }
            }

        $response .= '</nav>';

        return $response;
    }
}