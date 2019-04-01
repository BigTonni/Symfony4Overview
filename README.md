# GeekHub Advanced PHP 2018-2019

[![Build Status](https://travis-ci.org/BigTonni/Symfony4Overview.svg?branch=master)](https://travis-ci.org/BigTonni/Symfony4Overview)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/BigTonni/Symfony4Overview/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/BigTonni/Symfony4Overview/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/BigTonni/Symfony4Overview/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

## Lessons by Symfony#4 
<ol>
<li>Entities: article, comment, category, tag, user and etc.</li>
<li>Pages: all articles, sorting articles by category and tag, single article with new comment form and list of all comments.</li>
<li>Pagination via **KnpPaginatorBundle**.</li>
<li>Breadcrumbs via **BreadcrumbsBundle**.</li>
<li>Меню (KnpMenuBundle).</li>
<li>To use Timestampable и Sluggable behaviour from **StofDoctrineExtensionsBundle**.</li>
<li>Fixtures for all entities.</li>
<li>Menu with categories on all pages except admin pages.</li>
<li>Notifications about new articles.</li>
<li>Adminpanel via **SonataAdminBundle**:
<ul>
<li>to manage all entities in adminpanel (list with filters, review, add/edit/delete)</li>
<li>to sort list by different fields</li>
</ul>
<li>Tags are as text lines.</li>
<li>Search articles.</li>
<li>Tree category behaviour via **StofDoctrineExtensionsBundle**.</li>
<li>Configure pagination (article count per page via own bundle).</li>
<li>Like-action for articles.</li>
<li>Wysiwyg-editor for articles.</li>
<li>Override error pages (404, 403, 500).</li>
<li>Translated files.</li>
<li>User profile (settings, articles, comments, likes)</li>
<li>REST-API with API-doc.</li>
<li>Tests (PHPUnit, Behat). Code coverage.</li>
</ol>

To show list all the existing users:
>  php bin/console app:list-users

To send notifications about new notifications:
>  php bin/console app:send-notification
