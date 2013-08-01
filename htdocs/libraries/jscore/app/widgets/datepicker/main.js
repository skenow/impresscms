/* global icms: true */
/*
  Module: Date Picker
  Handles displaying the datepicker

  Method: initialize
*/
define([
  'jquery'
  , 'i18n!libs/datepicker/nls/lang'
  , 'css!libs/datepicker/datepicker.css'
  , 'libs/datepicker/datepicker'
]
, function($, dateLocale) {
  return {
    initialize: function(ele) {
      if(typeof ele !== 'undefined') {
        $(document).ready(function() {
          console.log(dateLocale);
          // ignore that this says en :) we are tricking it into i18n
          $.fn.datepicker.dates.en = dateLocale;
          ele.find('input').datepicker();
        });
      }
    }
  };
});
