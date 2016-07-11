# Block region injector
This module swaps out the page rendering layer, and caches the blocks, allowing
them to be exposed to templates other than the page template. Using this module
you are able to set up more complex regions for your theme, such as regions
within the node template. This will allow you to output both fields and blocks
within the subject of a page (even blocks within fields) to create a more
dynamic front-end than Drupal allows out of the box.

This helps developers that are working to front-end designs and templates that
have a more complex layout than the encapsulated way that Drupal wants to work.
The most common example of which is perhaps wanting to display your tags in the
sidebar of a page, however due to the way that Drupal requires you to layout
your pages, you either have to:

* Set up the sidebar in your page template: create a block that will display on
  node pages, and output tags if it finds any. This is presents the problem of
  additional work to output a field (that Drupal already does well out of the
  box), and solutions that integrate well into the node-preview workflow are
  difficult.
* Set up the sidebar in your node template: write a preprocess that will get
  the particular block that you want to inject, which lacks flexibility. Or
  grab all blocks by region and inject them with a preprocess manually, which
  generally adds the overhead of re-loading and re-rendering blocks; in Drupal
  7 this is particularly an issue with dynamically assigned paginators (ie. via
  EntityFieldQuery.)

This solution is more robust, as it takes ownership of the blocks, before they
are placed on the page, and keeps them cached for dynamically re-using through
the site. This way you can simply leave the page template to take ownership of
the header and footer of the page, and have your sidebar within the node
template.

This module uses a generic preprocess hook to inject the block regions into any
template, allowing the designer to plan dynamic regions anywhere. This also
provides a consistent, minimal overhead solution that doesn't require
constantly checking underlying preprocess hooks to determine where injected
elements are being derived from.

## Usage
All blocks are exposed to templates in the blocks variable, keyed by region.
Therefore if you wanted to output blocks in the region content_sidebar in the
node template, you would need to use the following twig:
```twig
{{ blocks.content_sidebar }}
```
Using this you are able to define a sidebar within your node template, and
output for example your field_tags normally using the field formatter, but then
directly underneath output any dynamically assigned sidebar blocks to the page,
such as advertisements, or user-related actions.

All blocks are available with the exception of the main content block (which it
is likely you will be wanting to use this module to output regions within,) and
the page title block; which requires the main content block to have been built
first, however if you are on the subject of the page, you could output the
title directly (ie. `{{ label }}` for a node.)
