Gratis, Version 8.x-2.0-dev, December, 2014 for Drupal 8

-- SUMMARY --

Gratis is a responsive Drupal 8 HTML5 (Lib)Sass based theme designed and developed
by Danny Englander (Twitter: @Danny_Englander). It allows for a choice color
palettes on the theme's settings page.

Gratis for Drupal 8 now uses Grunt, LibSass, Bourbon, and Neat to generate its CSS.
For more information on how to set this up, see the readme file under /sass.

Gratis is aimed at users who want to get a nice looking theme up and
running in short order, may not want to take the time to create a sub-theme
and mess with regions, settings, media queries, and other highly technical
things. It's also aimed at a casual Drupal user who has some familiarity
with building sites. This theme also does not require any base theme.

-- CONFIGURATION --

- Configure theme settings in Administration > Appearance > Settings >
Gratis or /admin/appearance/settings/gratis and choose various options
available.

-- THEME SETTINGS UI --

- Choice of several color palettes: Turquoise Blue, Cool Purple, Pumpkin
Orange, Olive Green, Pomegranate Red, Seafoam Green, Green Gray,
Pink, Mustard, Surf Green, Maillot Jaune (Dark background),
Caribe (Dark background), and Chartreuse (Dark background)

- Toggle Breadcrumbs on or off

- Local CSS - Choose to enable local.css file within the theme folder.

- Custom Path CSS -  Define a custom path for your own css file to use with
the theme.

- Customizable layout width in the theme settings UI, go as wide as you
want! It's all percentage based within the parent container. That's one of
the drawbacks of Bamboo and there were a lot of feature requests for this.

- Main navigation - set to the Primary menu block located at /admin/structure/block.

- Pinch and Zoom for Touch friendly devices - Option to choose whether to
pinch and zoom on a touch sensitive device or not. Default is off. Note,
there is no support for layouts breaking or otherwise if you choose to
enable this option.

-- ADDITIONAL FEATURES --

- Responsive for phone, tablet, and desktop using media queries

- Mobile, touch friendly menu

- Drop down menus (for desktop)

- Note for drop down / sub-menus to work, you need to set a main menu item
to "Show as expanded" in the Drupal menu settings UI. This setting is
located at /admin/structure/menu/item/xxx/edit where "xxx" is the id of
your menu item. Or simply go to: Administration > Structure > Menus > Main
menu and then click a menu item and edit. If you need help with this,
please consult Drupal core documentation.

- Tertiary Menus -  Currently not supported but possibly planned.

-- REQUIREMENTS --

No base theme needed, simply use this theme on its own.

-- INSTALLATION --

Install as usual, see http://drupal.org/node/176045 for further
information.

-- CUSTOMIZATION --

* As with any other Drupal theme you are able to customize just about every
aspect of this theme but some nice defaults are provided out of the box
with Gratis.

- drupal.org theme guide is here : http://drupal.org/documentation/theme

-- UPGRADING -- Nothing too tricky here other than if you have a local.css
or custom path CSS file as per the documentation. When upgrading, you must
preserve local.css somewhere, otherwise it could get overwritten with the
upgrade. After you upgrade, you can then drop local.css back in to the
theme. Of course if you have modified other files, they will all get
overwritten.

- In many cases, a subtheme is probably recommended then as opposed to
using local.css. You can create a sub-theme of your own to put all your
overrides in: "Creating a sub-theme" - http://drupal.org/node/225125 A
future version of this theme may allow for a custom path for local.css to
avoid upgrade snags.

-- NOTES --

This theme supports CSS3 / HTML5 and media queries. There is no support for
IE9 and below.

Buy me a Latte - Help support Gratis but it's not a requirement.
http://dannyenglander.com/buy-me-latte

-- Danny Englander Drupal Themer and Photographer --
San Diego, California
http://dannyenglander.com
http://highrockphoto.com
