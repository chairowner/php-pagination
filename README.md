# php-pagination
The class is designed to create html markup for the pagination menu
<br>
Класс рассчитан на создание html-разметки меню пагинации

# Example / Пример
```
$currentPageNumber = 2;
$all_products_count = 120;
$pattern = "/shop/all/popular/#";
$viewCount = 30;

$pagination = new Pagination($currentPageNumber, $all_products_count, $pattern, $viewCount);
$pagination->SetBeforeCurrent(3);
$pagination->SetAfterCurrent(3);
$pagination->SetButtonTitle(Pagination::PREVIOUS_BUTTON, 'Назад');
$pagination->SetButtonTitle(Pagination::NEXT_BUTTON, 'Далее');
$pagination->SetMainStyle("margin-top:40px;");

echo($pagination->Render());
```
![image info](./example/Screenshot_1.png)

# Author
[Danil Sagajdachnyj](https://github.com/chairowner)

# Copyright
MIT License Copyright © 2023 Danil Sagajdachnyj
