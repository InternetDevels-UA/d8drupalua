/**
 * @file
 * Misc js for gratis 2.
 */

(function ($, Drupal) {

  Drupal.behaviors.gratisMiscfunctions = {
    attach: function (context) {

      // Scroll to top.
      $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
          $('.scrolltop').fadeIn();
        } else {
          $('.scrolltop').fadeOut();
        }
      });

      $('.scrolltop').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 500);
        return false;
      });

      // End scroll to top.

    }
  };

  /**
   * Toggle show/hide links for off canvas layout.
   *
   */
  Drupal.behaviors.gratisOffCanvasLayout = {
    attach: function (context) {

      // Off-canvas menu.

      $('.l-page').click(function (e) {
        var offCanvasVisible = $('.l-page-wrapper').hasClass('off-canvas-left-is-visible') || $('.l-page-wrapper').hasClass('off-canvas-right-is-visible');
        var targetIsOfOffCanvas = $(e.target).closest('.l-off-canvas').length !== 0;
        if (offCanvasVisible && !targetIsOfOffCanvas) {
          $('.l-page-wrapper').removeClass('off-canvas-left-is-visible off-canvas-right-is-visible');
          e.preventDefault();
        }
      });

      $('.l-off-canvas-show--left').click(function (e) {
        $('.l-page-wrapper').removeClass('off-canvas-left-is-visible off-canvas-right-is-visible');
        $('.l-page-wrapper').addClass('off-canvas-left-is-visible');
        e.stopPropagation();
        e.preventDefault();
      });

      $('.l-off-canvas-show--right').click(function (e) {
        $('.l-page-wrapper').removeClass('off-canvas-left-is-visible off-canvas-right-is-visible');
        $('.l-page-wrapper').addClass('off-canvas-right-is-visible');
        e.stopPropagation();
        e.preventDefault();
      });

      $('.l-off-canvas-hide').click(function (e) {
        $('.l-page-wrapper').removeClass('off-canvas-left-is-visible off-canvas-right-is-visible');
        e.stopPropagation();
        e.preventDefault();
      });

    }
  };

  /**
   * Toggle expanded menu states.
   */
  Drupal.behaviors.gratisExpandMenus = {
    attach: function (context) {

      // Off-canvas, check for child elements on parent menu.
      $('.main-menu-wrapper ul li').once(function(){
        if($('ul',this).length){
          $(this).addClass('has-child');
        }
      });

      // Nested off canvas menu items.
      $('.menu .has-child').not('.active-trail').removeClass('has-child');
      $('.menu li a').once(function () {
        if ($(this).parent().children('ul').length !== 0) {
          $(this).addClass('alink');
          $(this).after('<a href="#" class="nested-menu-item-toggle"></a>');
        }
      });
      $('.nested-menu-item-toggle').click(function () {
        $(this).closest('li').toggleClass('has-child');
        return false;
      });
    }
  };

})(jQuery, Drupal);
