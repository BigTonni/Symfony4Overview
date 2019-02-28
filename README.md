# GeekHub Advanced PHP 2019

## Lessons by Symfony#4 
<ol>
<li>Entities: article, comment, category, tag, user and etc.</li>
<li>Pages: all articles, sorting articles by category and tag, single article with new comment form and list of all comments.</li>
<li>Pagination via ** KnpPaginatorBundle **.</li>
<li>Breadcrumbs via **BreadcrumbsBundle.</li>
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
<li>Configure pagination (article count per page).</li>
<li>Like for articles.</li>
<li>Wysiwyg-editor.</li>
<li>Override error pages (404, 403, 500)</li>
<li>Translated files</li>
<li>REST-API with API-doc</li>
<li>Tests</li>
</ol>

Example to register a new user:
```sh
curl -X POST http://127.0.0.1:8000/api/register -d _email=john@doe.com -d _password=test
```
Example, get a JWT Token:
```sh
curl -X POST -H "Content-Type: application/json" http://127.0.0.1:8000/api/login_check -d '{"username":"john@doe.com","password":"test"}'
-> { "token": "[TOKEN]" }
```
Example of accessing secured routes:
```sh
curl -H "Authorization: Bearer [TOKEN]" http://127.0.0.1:8000/api/articles
-> ALL ARTICLES
```