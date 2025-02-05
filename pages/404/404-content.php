<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
?>

<div class="col-md-6 text-center mx-auto flex-grow-1 d-flex flex-column justify-content-center align-items-center">
  <h2 class="display-4 mb-4">Ой-ой!</h2>

  <p class="lead lh-lg">Возможно, это ваша ошибка, <br class="d-md-none">а может быть, это наша,<br>но здесь нет нужной
    вам страницы.</p>

  <div class="container m-4">
    <form action="/search-process" method="get" class="mb-3">
      <div class="input-group">
        <input type="text" name="query" class="form-control" placeholder="Искать на 11klassniki.ru...">
        <button type="submit" class="btn btn-primary">Найти</button>
      </div>
    </form>
  </div>
  <a href="/" class="link-custom">Перейти на главную страницу</a>
</div>