<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4">Тестовое задание</h1>

        <p class="lead">Каталог книг</p>

    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4 mb-3">
                <h2>Книги</h2>

                <p>Добавление, просмотр, редактирование, удаление</p>

                <p><a class="btn btn-outline-secondary" href="/book">Книги &raquo;</a></p>
            </div>
            <div class="col-lg-4 mb-3">
                <h2>Авторы</h2>

                <p>Добавление, просмотр, редактирование, удаление, подписка на новые книги автора</p>

                <p><a class="btn btn-outline-secondary" href="/author">Авторы &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Отчёт</h2>

                <p>ТОП-10 авторов, выпустивших больше всего книг за указанный год</p>

                <p><a class="btn btn-outline-secondary" href="/report">Отчёт &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
