INTRODUCTION
------------

Extends the [External-use Icons](https://www.drupal.org/project/ex_icons) into
Drupal menu items, allowing an icon selection for top-level menu links or chosen
menus.

Note: this module does not display any of the icons selected. It is up to the
theme to display the icons as required.

Menu links from `extension.links.menu.yml` and similar declarations of links
created in code can add their own icon choice by setting the value of
`ex_icons_menu_icon` in the `metadata` of the menu link plugin definition.


REQUIREMENTS
------------

This module requires the following modules:

* [External-use Icons](https://www.drupal.org/project/ex_icons)


INSTALLATION
------------

* Install as you would normally install a contributed Drupal module. Visit
  https://www.drupal.org/node/1897420 for further information.


CONFIGURATION
-------------

To enable icon selection for top-level menu links for a menu:

1. Go to the edit form of the menu (Structure → Menus → _Your Menu_)
2. Check _Enable icon selection_ checkbox
3. Hit _Save_
