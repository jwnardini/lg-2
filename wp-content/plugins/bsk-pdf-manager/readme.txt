=== BSK PDF Manager ===
Contributors: bannersky
Plugin URI: http://www.bannersky.com/bsk-pdf-manager/
Tags: PDF, manager, category, widget
Requires at least: 4.0
Tested up to: 5.0.3
Stable tag: 2.2.1

== Description ==

Although the plugin called "PDF Manager" but it also can manage other files such as: pdf, zip, gz, rar, png, jpg, jpeg, gif, tif, tiff, swf, docx, xlsx, pptx, csv, crtfsv, pages, numbers, keynote, ies.


The plugin help you manage your PDFs / Files in WordPress. You may upload by categories and display by categories or show one special PDF / File.

It is easy to use. You just need add the shortcodes into the page / post where you want to show. Then it will show the PDFs / Files link in your page / post.

With widget feature you may show PDFs list in a widget area.

Please visit <a href="http://www.bannersky.com/bsk-pdf-manager/">https://www.bannersky.com/bsk-pdf-manager/</a> for documentations.

Check demos from: <a href="https://demo.bannersky.com/bsk-pdf-manager-demos/">https://demo.bannersky.com/bsk-pdf-manager-demos/</a>

Pro version available now! You may upgrade to pro version for more features.


== Installation ==

Activate the plugin then you can use either a shortcode [bsk-pdfm-pdfs-ul id="ALL" order_by="date" order="DESC" target="_blank"] to show all PDFs / Documents in date descending order. Or use [bsk-pdfm-pdfs-ul id="8,9,10,11,12" target="_blank"] to show special PDFs / Documents. 

Check <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-specific-pdfs-in-list/" target="_blank">here for more shortcode attributes</a> and <a href="https://demo.bannersky.com/bsk-pdf-manager-demos/display-all-specific-pdfs/all-pdfs-in-unordered-list-in-date-descending-order-open-in-new-window-with-pagination/" target="_blank">here for demos</a> about this shortcode.

You may use [bsk-pdfm-category-ul id="1" show_cat_title="yes" order_by="date" order="DESC"] to show all PDFs / Documents under the category of id 1 or [bsk-pdfm-category-ul id="1,2,3" show_cat_title="yes" order_by="date" order="DESC"] to show all PDFs under categories of id 1, 2, 3 in date descending order.

Check <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-by-category-in-list/" target="_blank">here for more shortcode attributes</a> and <a href="https://demo.bannersky.com/bsk-pdf-manager-demos/display-pdfs-by-category/display-pdfs-by-category-in-unordered-list-with-pagination/" target="_blank">here for demos</a> about this shortcode.

The plugin has a very easy admin page that allows you to manage categories and PDF documents.

== Frequently Asked Questions ==

Please visit <a href="http://www.bannersky.com/bsk-pdf-manager/">http://www.bannersky.com/bsk-pdf-manager/</a> for documentations or supporting.

== Screenshots ==

1. Settings interface, you may support more file types there
2. Upload your PDF / File from computer or WordPress Media library
3. Categories manager interface


== Changelog ==

2.2.1

* Fix the bug of removing data when upgrade to Pro version

* Fix a bug of causing failed uploading

* Compatible with WordPress 5.0.3

2.2

* Fix bug when create new directory to upload PDFs

* Update database structure to compatible with Pro version

* Admin interface improved

* Compatible with WordPress 5.0.2

2.1

* Fix bug when upload PDF

* Admin interface improved

* Compatible with Wordpress 5.0.1

2.0

* Support more file types

* Support order by PDFs id sequence

* Support show all categories in one shortcode

* Improve dashboard user interface

* Remove category password feature from free version as it cannot work on some hosting

* Compatible with Wordpress 4.9.8

* Compatible with PHP 5 & PHP 7

1.8.2

* Ready for version 2.0

* Fix PHP warnings

* Compatible with Wordpress 4.9.8

* Compatible with PHP 5 & PHP 7

1.8.1

* Add new shortcode parameter of nolist, set to yes will only output html a element.

* Improve parameters compatibility

* Compatible with Wordpress 4.9.5

1.8

* Fix small bug in widget

* Use new singleton design pattern

1.7.5

* Improve user interface

* Compatible with Wordpress 4.9.4

1.7.4

* Add the feature to show all PDFs when use shortcode [bsk-pdf-manager-pdf showall="yes"]

* Compatible with Wordpress 4.9.2

1.7.3

* Fixed the bug of unclosed list tag when using widgets that for unordered lists

1.7.2

* Add show category title option to widget

* Fixed a small bug

1.7.1

* Fixed the bug of bulk delete triggered when change category filter drop down


1.7

* Change PDF widget title to use h2 tag.

* Add open new target parameter to PDF widget.

* Add PDF Category widget to show all PDF within given category.

* Compatible with Wordpress 4.7.3.


1.6

* Fixed warning message.

* Improved backend interface.

* Add search feature on backend PDFs list page.

1.5.2

* Fixed warning message.

1.5.1

* Fixed the bug of putting an "/" slash at the end of the unordered list in widget.

1.5

* Change Datepicker to use the latest jQuery UI theme css
* Security improvement
* Support show PDF as ordered list

1.4

* Make shortcode support new attributes.
* Fix the bug that only show one category even there are multiple ids.

1.3.9

* Fix a small bug.

1.3.8

* Change PDF file name to XXXX_ID.pdf.

1.3.7

* Fix two warnings & make users who above Editor can add / edit PDFs.

1.3.6

* Fix the bug of cannot delete PDF item or category.

1.3.5

* Fixed a typo.

* Support out PDFs in a dropdown(select).

* Make date for category and PDF item editable.

* Support setting the number of most recent PDFs.

1.3.4

* Fixed a small bug.

1.3.3

* Changed order by parameter, now you may include orderby="title" or orderby="filename" or orderby="date" to order all PDF, also can be order by PDFs' id sequence.

1.3.2

* Fixed the bug of wrong output when category doesn't have a PDF.

1.3.1

* Widget supported now.

* Support wordPress site with subdirectory installation.

* Changed special PDF list from &lt;p&gt; tag to &lt;li&gt; tag, only return PDF link is possilbe. Support multiple PDF id and category id, so show all PDFs in one list is possible.

* Changed backend interface.

1.3

* Support PDF list order by Date, Title and File Name.

1.2.0

* Fixed several small bug.

* Support order by in admin dashboard.

* Hide category title when list PDF.

1.1.0

* Add option to open PDF document in new window.

* Show shortcode in Categories page and PDF Documents page. For convenience user just need copy and paste it to where you want to show the PDF documents.

1.0.0 First version.

