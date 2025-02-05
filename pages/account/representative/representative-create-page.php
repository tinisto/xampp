<div class="mb-5">
    <p>Как представитель учебного заведения или его филиала, вы можете управлять страницей вашего учебного заведения на нашем сайте. Это включает редактирование информации, добавление новостей и обновление контактных данных.<br>
        Мы рекомендуем сначала поискать ваше учебное заведение — возможно, его страница уже существует. Если страница найдена, вы увидите дату последнего обновления и иконку редактирования <i class="fa fa-pencil" style="color: red;"></i>.<br>
        Если страница не найдена, вы можете создать новую. После создания она будет отправлена на модерацию перед публикацией.</p>

    <div class="d-flex gap-2">
        <?php
        if ($occupation === "Представитель ВУЗа") {
            echo '<a class="btn btn-secondary btn-sm" href="/search">Найти ВУЗ</a>';
            echo '<a class="btn btn-secondary btn-sm" href="/pages/common/create.php">Создать страницу ВПО</a>';
        } elseif ($occupation === "Представитель ССУЗа") {
            echo '<a class="btn btn-secondary btn-sm" href="/search">Найти ССУЗ</a>';
            echo '<a class="btn btn-secondary btn-sm" href="/pages/common/create.php">Создать страницу СПО</a>';
        } elseif ($occupation === "Представитель школы") {
            echo '<a class="btn btn-secondary btn-sm" href="/search">Найти школу</a>';
            echo '<a class="btn btn-secondary btn-sm" href="/pages/common/create.php">Создать страницу школы</a>';
        }
        ?>
    </div>
</div>