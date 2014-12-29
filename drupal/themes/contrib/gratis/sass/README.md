#Gratis and LibSass

Gratis now uses Grunt and LibSass via Node.js NPM (Node packaaged modules).  To get up and running, follow the steps below:

1. Install node.js from http://nodejs.org/
2. cd to the gratis folder
3. run sudo npm install (if all went well, you will now have your local node modules)
4. run grunt
5. make changes
6. profit


## Barebones Sass Structure
Multiple Sass partials are used to help organise the styles, these are combined
by including them in styles.scss which is compiled into styles.css in the css/
directory.

The file and directory structure contained in this folder looks something like
this:

### Top level files
These files are the main entry points for the Sass compiler and shouldn't
directly contain any CSS code, instead they only serves to combine the Sass
contained in the partials (see below) through @import directives.

#### gratis.styles.scss
This file aggregates all the components into a single file.

#### gratis.color-palettes.scss
This file contains the color styles sass loops.

#### gratis.normalize.scss
This file provides a CSS reset/normalize generated based on the legacy
variables.

#### gratis.hacks.scss
This file may be used to provide temporary hot-fixes for style issues that
you plan to properly implement as components at a later point in time or simply
don't have a proper solution for yet.

#### variables
This is where you place your Sass variables.

#### abstractions
This is where you place your functions, mixins and extends.

#### partials
This is where you place all your basic, raw HTML element styling.
