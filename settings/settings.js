/* global ajaxurl */
(function ($) {
  'use strict';
  
  $(document).ready(function () {
    $('body').on('change', '#person', function() {
      if (this.value != 0 || this.value != '0') {
        var activeClassPerson = "form-settings-person-wrapper-active";

        $("." + activeClassPerson).hide();
        $("." + activeClassPerson).removeClass(activeClassPerson);

        $("[data-toggle='"+ this.value +"']").show();
        $("[data-toggle='"+ this.value +"']").addClass(activeClassPerson);
        refresh();
      }
    });

    $('body').on('change', '#history', function() {
      if (this.value != 0 || this.value != '0') {
        var activeClassHistory = "form-settings-history-wrapper-active";

        $("." + activeClassHistory).hide();
        $("." + activeClassHistory).removeClass(activeClassHistory);

        $("[data-toggle='"+ this.value +"']").show();
        $("[data-toggle='"+ this.value +"']").addClass(activeClassHistory);
        refresh();
      }
    });


    function refresh() {
      for(var i = 0; i < window.editors.length; i++) {
        window.editors[i].refresh();
      }
    }
  });

})(jQuery);